<?php

namespace Modules\Whatsappcall\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Whatsappcall\Events\CallClaimed;
use Modules\Whatsappcall\Models\Call as CallModel;
use Modules\Wpbox\Models\Contact;

class CallController extends Controller
{
    use \Modules\Whatsappcall\Traits\WhatsappCall;

    public function getPermissionStatus(Request $request)
    {
        // Get current call permission status for a contact
        $validated = $request->validate([
            'contact_id' => 'required|integer',
        ]);

        $company = $this->getCompany();
        $contact = Contact::where('company_id', $company->id)->findOrFail($validated['contact_id']);

        // Remove + prefix if present for WhatsApp API
        $userWaId = ltrim($contact->phone, '+');

        $result = $this->getCallPermissionStatus($userWaId, $company);

        return response()->json($result);
    }

    public function requestPermission(Request $request)
    {
        // Send a permission request inside an active conversation window
        $validated = $request->validate([
            'contact_id' => 'required|integer',
            'message' => 'nullable|string',
        ]);

        $company = $this->getCompany();
        $contact = Contact::where('company_id', $company->id)->findOrFail($validated['contact_id']);

        $result = $this->sendCallPermissionRequest($contact, $validated['message'] ?? null);
        // Track an intent record
        CallModel::create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'direction' => 'BIC',
            'status' => 'permission_requested',
            'wa_user_id' => $contact->phone,
            'meta' => $result,
        ]);

        return response()->json($result);
    }

    public function startBusinessCall(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|integer',
            'sdp' => 'required|string',
        ]);
        $company = $this->getCompany();
        $contact = Contact::where('company_id', $company->id)->findOrFail($validated['contact_id']);

        $sdp = $request->input('sdp');

        $result = $this->initiateBusinessCall($contact, $sdp);
        CallModel::create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'direction' => 'BIC',
            'status' => 'link_sent',
            'wa_user_id' => $contact->phone,
            'meta' => $result,
        ]);

        return response()->json($result);
    }

    // WhatsApp Calling webhook (UIC/BIC updates)
    public function receiveCallingWebhook(Request $request, $token = null)
    {
        return $this->handleCallingWebhook($request, $token);
    }

    // Calls dashboard (owner menu)
    public function index()
    {
        $company = $this->getCompany();
        $calls = CallModel::where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        $setup = [
            'items' => $calls,
            'title' => __('WhatsApp Calls'),
            'item_names' => __('calls'),
            'custom_table' => true,
        ];

        return view('whatsappcall::calls.index', compact('setup'));
    }

    // Lightweight polling endpoint for chat UI to detect active UIC calls
    public function activeForChat(Request $request)
    {
        $company = $this->getCompany();
        $since = now()->subMinutes(5);
        $calls = CallModel::where('company_id', $company->id)
            ->where('direction', 'UIC')
            ->where('status', 'connect')
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($call) use ($company) {
                $contact = Contact::where('company_id', $company->id)
                    ->where(function ($q) use ($call) {
                        $q->where('phone', $call->wa_user_id)
                            ->orWhere('phone', '+'.$call->wa_user_id);
                    })
                    ->first();
                $offerSdp = data_get($call->meta, 'calls.0.session.sdp');
                $offerType = data_get($call->meta, 'calls.0.session.sdp_type', 'offer');
                $waCallId = data_get($call->meta, 'calls.0.id') ?: $call->wa_call_id;

                return [
                    'id' => $call->id,
                    'created_at' => $call->created_at,
                    'wa_user_id' => $call->wa_user_id,
                    'status' => $call->status,
                    'contact_id' => $contact?->id,
                    'contact_name' => $contact?->name,
                    'wa_call_id' => $waCallId,
                    'offer' => [
                        'sdp' => $offerSdp,
                        'type' => $offerType,
                    ],
                ];
            });

        return response()->json(['ok' => true, 'calls' => $calls]);
    }

    // UIC Part 2: Pre-accept inbound call with SDP answer
    public function preAcceptWhatsAppCall(Request $request)
    {
        // Log::info('WA pre_accept request (server)', ['request' => $request->all()]);

        $validated = $request->validate([
            'call_id' => 'required|integer', // our internal Call model id
            'sdp' => 'required|string',
        ]);

        $company = $this->getCompany();
        $call = CallModel::where('company_id', $company->id)->findOrFail($validated['call_id']);
        $waCallId = data_get($call->meta, 'calls.0.id') ?: $call->wa_call_id;
        Log::info('WA pre_accept request (server)', [
            'company_id' => $company->id,
            'call_id' => $call->id,
            'wa_call_id' => $waCallId,
            'sdp_len' => strlen($validated['sdp'] ?? ''),
        ]);
        if (! $waCallId) {
            return response()->json(['ok' => false, 'error' => 'No WhatsApp call id'], 422);
        }

        $phoneId = $this->getPhoneID($company);
        $token = $this->getToken($company);
        if (! $phoneId || ! $token) {
            return response()->json(['ok' => false, 'error' => 'Phone ID or token missing'], 422);
        }

        $url = self::$facebookAPI.$phoneId.'/calls';
        $payload = [
            'messaging_product' => 'whatsapp',
            'call_id' => $waCallId,
            'action' => 'pre_accept',
            'session' => [
                'sdp_type' => 'answer',
                'sdp' => $validated['sdp'],
            ],
        ];

        Log::info('WA pre_accept request (server)', ['payload' => $payload]);

        $res = Http::withToken($token)->post($url, $payload);
        Log::info('WA pre_accept response', ['status' => $res->status(), 'body' => $res->json() ?? $res->body()]);
        if ($res->failed()) {
            return response()->json(['ok' => false, 'status' => $res->status(), 'body' => $res->json() ?? $res->body()], $res->status());
        }

        $call->update(['status' => 'pre_accept']);

        return response()->json(['ok' => true, 'success' => true]);
    }

    // UIC Part 3: Accept call after WebRTC connection established
    public function acceptWhatsAppCall(Request $request)
    {
        $validated = $request->validate([
            'call_id' => 'required|integer',
            'sdp' => 'required|string',
        ]);

        $company = $this->getCompany();
        $call = CallModel::where('company_id', $company->id)->findOrFail($validated['call_id']);
        $waCallId = data_get($call->meta, 'calls.0.id') ?: $call->wa_call_id;
        Log::info('WA accept request (server)', [
            'company_id' => $company->id,
            'call_id' => $call->id,
            'wa_call_id' => $waCallId,
            'sdp_len' => strlen($validated['sdp'] ?? ''),
        ]);
        if (! $waCallId) {
            return response()->json(['ok' => false, 'error' => 'No WhatsApp call id'], 422);
        }

        $phoneId = $this->getPhoneID($company);
        $token = $this->getToken($company);
        if (! $phoneId || ! $token) {
            return response()->json(['ok' => false, 'error' => 'Phone ID or token missing'], 422);
        }

        $url = self::$facebookAPI.$phoneId.'/calls';
        $payload = [
            'messaging_product' => 'whatsapp',
            'call_id' => $waCallId,
            'action' => 'accept',
            'session' => [
                'sdp_type' => 'answer',
                'sdp' => $validated['sdp'],
            ],
        ];

        $res = Http::withToken($token)->post($url, $payload);
        Log::info('WA accept response', ['status' => $res->status(), 'body' => $res->json() ?? $res->body()]);
        if ($res->failed()) {
            return response()->json(['ok' => false, 'status' => $res->status(), 'body' => $res->json() ?? $res->body()], $res->status());
        }

        $call->update([
            'status' => 'accept',
            'answered_at' => now(),
            'answered_by' => Auth::id(),
        ]);
        // Broadcast that this call has been claimed/answered
        try {
            event(new CallClaimed((int) $company->id, [
                'id' => $call->id,
                'wa_call_id' => $waCallId,
                'handled_by' => Auth::id(),
                'status' => 'accept',
            ]));
        } catch (\Throwable $th) {
        }

        return response()->json(['ok' => true, 'success' => true]);
    }

    // UIC Part 4: Terminate the call
    public function terminateWhatsAppCall(Request $request)
    {
        $validated = $request->validate([
            'call_id' => 'required|integer',
        ]);

        $company = $this->getCompany();
        $call = CallModel::where('company_id', $company->id)->findOrFail($validated['call_id']);
        $waCallId = data_get($call->meta, 'calls.0.id') ?: $call->wa_call_id;
        if (! $waCallId) {
            return response()->json(['ok' => false, 'error' => 'No WhatsApp call id'], 422);
        }

        $phoneId = $this->getPhoneID($company);
        $token = $this->getToken($company);
        if (! $phoneId || ! $token) {
            return response()->json(['ok' => false, 'error' => 'Phone ID or token missing'], 422);
        }

        $url = self::$facebookAPI.$phoneId.'/calls';
        $payload = [
            'messaging_product' => 'whatsapp',
            'call_id' => $waCallId,
            'action' => 'terminate',
        ];

        $res = Http::withToken($token)->post($url, $payload);
        Log::info('WA terminate response', ['status' => $res->status(), 'body' => $res->json() ?? $res->body()]);
        if ($res->failed()) {
            return response()->json(['ok' => false, 'status' => $res->status(), 'body' => $res->json() ?? $res->body()], $res->status());
        }

        $call->update(['status' => 'terminate', 'ended_at' => now()]);

        return response()->json(['ok' => true, 'success' => true]);
    }
}
