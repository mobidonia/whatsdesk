<?php

namespace Modules\Whatsappcall\Traits;

use App\Models\Company;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Whatsappcall\Events\CallEnded;
use Modules\Whatsappcall\Events\IncomingCall;
use Modules\Whatsappcall\Models\Call as CallModel;
use Modules\Wpbox\Models\Contact;

trait WhatsappCall
{
    public static $facebookAPI = 'https://graph.facebook.com/v23.0/';

    private function getToken(?Company $company = null): string
    {
        $company = $company ?: $this->getCompany();

        return $company->getConfig('whatsapp_permanent_access_token', '');
    }

    private function getPhoneID(?Company $company = null): string
    {
        $company = $company ?: $this->getCompany();

        return $company->getConfig('whatsapp_phone_number_id', '');
    }

    private function getAccountID(?Company $company = null): string
    {
        $company = $company ?: $this->getCompany();

        return $company->getConfig('whatsapp_business_account_id', '');
    }

    // Sync call settings to Meta per docs: calling/call-settings
    protected function syncCallSettingsWithMeta(Company $company): array
    {
        try {
            $phoneId = $this->getPhoneID($company);
            // Per Cloud API Calling docs, call settings are configured on the phone number
            $url = self::$facebookAPI.$phoneId.'/settings';
            $accessToken = $this->getToken($company);
            if (! $accessToken || ! $phoneId) {
                Log::warning('syncCallSettingsWithMeta: missing token or phone id', [
                    'company_id' => $company->id,
                    'has_token' => (bool) $accessToken,
                    'phone_id' => $phoneId,
                ]);

                return [
                    'ok' => false,
                    'status' => 400,
                    'error_message' => 'Missing token or phone id',
                ];
            }
            // Build payload following Meta calling settings structure
            $weekly = json_decode($company->getConfig('whatsapp_calling_weekly_operating_hours', '[]'), true) ?: [];
            $holidays = json_decode($company->getConfig('whatsapp_calling_holiday_schedule', '[]'), true) ?: [];
            $payload = [
                'calling' => [
                    'status' => ($company->getConfig('whatsapp_calling_enabled', false) ? 'ENABLED' : 'DISABLED'),
                    'call_icon_visibility' => $company->getConfig('whatsapp_call_icon_visibility', 'DEFAULT'),
                    'call_hours' => [
                        'status' => $company->getConfig('whatsapp_calling_hours_status', 'DISABLED'),
                        'timezone_id' => $company->getConfig('whatsapp_calling_timezone_id', 'UTC'),
                        'weekly_operating_hours' => $weekly,
                        'holiday_schedule' => $holidays,
                    ],
                    'callback_permission_status' => $company->getConfig('whatsapp_calling_callback_permission_status', 'DISABLED'),
                ],
            ];

            Log::info('syncCallSettingsWithMeta: sending', [
                'company_id' => $company->id,
                'phone_id' => $phoneId,
                'url' => $url,
                'payload' => $payload,
            ]);

            $response = Http::withToken($accessToken)->post($url, $payload);

            Log::info('syncCallSettingsWithMeta: response', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            if ($response->failed()) {
                $body = $response->json() ?? $response->body();
                Log::error('syncCallSettingsWithMeta: failed', [
                    'status' => $response->status(),
                    'body' => $body,
                ]);

                return [
                    'ok' => false,
                    'status' => $response->status(),
                    'error_message' => is_array($body) && isset($body['error']['message']) ? $body['error']['message'] : (is_string($body) ? $body : 'Unknown error'),
                    'body' => $body,
                ];
            }

            return [
                'ok' => true,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $th) {
            Log::error('syncCallSettingsWithMeta: exception', [
                'company_id' => $company->id ?? null,
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'ok' => false,
                'status' => 500,
                'error_message' => $th->getMessage(),
            ];
        }
    }

    // Get current call permission status for a user
    protected function getCallPermissionStatus(string $userWaId, ?Company $company = null): array
    {
        try {
            $company = $company ?: $this->getCompany();
            $phoneId = $this->getPhoneID($company);
            $accessToken = $this->getToken($company);

            if (! $accessToken || ! $phoneId) {
                Log::warning('getCallPermissionStatus: missing token or phone id', [
                    'company_id' => $company->id,
                    'has_token' => (bool) $accessToken,
                    'phone_id' => $phoneId,
                ]);

                return [
                    'ok' => false,
                    'status' => 400,
                    'error_message' => 'Missing token or phone id',
                ];
            }

            $url = self::$facebookAPI.$phoneId.'/call_permissions?user_wa_id='.$userWaId;

            Log::info('getCallPermissionStatus: requesting', [
                'company_id' => $company->id,
                'phone_id' => $phoneId,
                'user_wa_id' => $userWaId,
                'url' => $url,
            ]);

            $response = Http::withToken($accessToken)->get($url);

            Log::info('getCallPermissionStatus: response', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            if ($response->failed()) {
                $body = $response->json() ?? $response->body();
                Log::error('getCallPermissionStatus: failed', [
                    'status' => $response->status(),
                    'body' => $body,
                ]);

                return [
                    'ok' => false,
                    'status' => $response->status(),
                    'error_message' => is_array($body) && isset($body['error']['message']) ? $body['error']['message'] : (is_string($body) ? $body : 'Unknown error'),
                    'body' => $body,
                ];
            }

            $data = $response->json();

            return [
                'ok' => true,
                'status' => $response->status(),
                'data' => $data,
                'permission' => $data['permission'] ?? null,
                'actions' => $data['actions'] ?? [],
            ];
        } catch (\Throwable $th) {
            Log::error('getCallPermissionStatus: exception', [
                'company_id' => $company->id ?? null,
                'user_wa_id' => $userWaId,
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'ok' => false,
                'status' => 500,
                'error_message' => $th->getMessage(),
            ];
        }
    }

    // BIC: Send permission request over active conversation
    protected function sendCallPermissionRequest(Contact $contact, ?string $extraMessage = null): array
    {
        $accessToken = $this->getToken();
        $url = self::$facebookAPI.$this->getPhoneID().'/messages';

        $components = [
            'type' => 'request_call',
        ];
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $contact->phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'call_permission_request',
                'body' => [
                    'text' => $extraMessage ?: __('Can we call you now?'),
                ],
                'action' => [
                    'name' => 'call_permission_request',
                ],
            ],
        ];

        $response = Http::withToken($accessToken)->post($url, $payload);

        return ['status' => $response->status(), 'body' => $response->json()];
    }

    protected function initiateBusinessCall(Contact $contact, $sdp)
    {
        Log::info('Initiating business call', ['contact' => $contact]);

        $accessToken = $this->getToken();
        $url = self::$facebookAPI.$this->getPhoneID().'/calls';

        $components = [
            'type' => 'request_call',
        ];
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $contact->phone,
            'action' => 'connect',
            'session' => [
                'sdp_type' => 'offer',
                'sdp' => $sdp,
            ],
        ];

        Log::info('Business call payload', ['payload' => $payload]);

        $response = Http::withToken($accessToken)->post($url, $payload);

        Log::info('Business call response', ['status' => $response->status(), 'body' => $response->json()]);

        return ['ok' => true, 'status' => $response->status(), 'body' => $response->json()];
    }

    // BIC: initiate the actual call through Infobip WebRTC (Call Link)
    protected function initiateBusinessCallInfobip(Contact $contact): array
    {
        $company = $this->getCompany();
        $infobipBaseUrl = $company->getConfig('infobip_base_url', '');
        $infobipApiKey = $company->getConfig('infobip_api_key', '');
        $infobipAppId = $company->getConfig('infobip_webrtc_application_id', '');
        if (! $infobipBaseUrl || ! $infobipApiKey) {
            return ['status' => 422, 'error' => 'Infobip credentials missing'];
        }

        // Generate a Call Link to share via WhatsApp message
        $callLinkUrl = rtrim($infobipBaseUrl, '/').'/webrtc/1/call-links';
        $payload = [
            'endUser' => [
                'identity' => 'contact_'.$contact->id,
                'displayName' => $contact->name ?: $contact->phone,
            ],
            'destination' => [
                'type' => 'PHONE',
                'phoneNumber' => $company->getConfig('infobip_agent_phone_number', ''),
            ],
            'validity' => [
                'oneTime' => true,
            ],
        ];
        if ($infobipAppId) {
            $payload['applicationId'] = $infobipAppId;
        }

        $response = Http::withHeaders([
            'Authorization' => 'App '.$infobipApiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($callLinkUrl, $payload);

        $data = $response->json();
        if ($response->failed()) {
            return ['status' => $response->status(), 'error' => $data];
        }

        // Send the Call Link to the customer via WhatsApp message
        $link = $data['url'] ?? null;
        if ($link) {
            $waUrl = self::$facebookAPI.$this->getPhoneID().'/messages';
            $waPayload = [
                'messaging_product' => 'whatsapp',
                'to' => $contact->phone,
                'type' => 'text',
                'text' => [
                    'body' => __('Tap to join the call: ').$link,
                ],
            ];
            Http::withToken($this->getToken())->post($waUrl, $waPayload);
        }

        return ['status' => 200, 'link' => $data['url'] ?? null];
    }

    // Webhook to ingest calling events (UIC and BIC)
    protected function handleCallingWebhook(Request $request, $token = null)
    {
        // Token auth (optional)
        try {
            if ($token) {
                $t = PersonalAccessToken::findToken($token);
                if ($t) {
                    $user = \App\Models\User::findOrFail($t->tokenable_id);
                    Auth::login($user);
                }
            }
        } catch (\Throwable $th) {
        }

        $payload = $request->all();
        Log::info('WhatsApp Calling webhook', $payload);

        // Cloud API webhooks surface under object=whatsapp_business_account and field=messages
        // For calling-specific events, Meta is rolling out "calls" field. We handle both.
        try {
            $entry = $payload['entry'][0] ?? null;
            if (! $entry) {
                return response()->json(['ok' => true]);
            }
            $value = $entry['changes'][0]['value'] ?? [];
            $field = $entry['changes'][0]['field'] ?? 'messages';

            // Identify company by WABA id in entry.id, same approach as Wpbox receiveMessage
            $wabaId = $entry['id'] ?? null;
            $companyId = Config::where('value', $wabaId)->first()->model_id ?? null;
            $company = $companyId ? Company::find($companyId) : null;

            // Determine direction and participants
            $waUserId = $value['contacts'][0]['wa_id'] ?? ($value['statuses'][0]['recipient_id'] ?? null);

            // UIC: user-initiated calls likely appear in future under field=calls. For now, store a generic event.
            if ($field === 'calls') {
                $waCallId = $value['calls'][0]['id'] ?? null;
                $event = $value['calls'][0]['event'] ?? ($value['calls'][0]['event'] ?? 'unknown');

                //From
                $from = $value['calls'][0]['from'] ?? null;
                //Find the contact by phone number
                $contact = Contact::where('phone', $from)->orWhere('phone', '+'.$from)->where('company_id', $company->id)->first();

                //Find existing call by wa_call_id
                $call = CallModel::where('wa_call_id', $waCallId)->first();
                if ($call) {
                    $call->update([
                        'status' => strtolower($event),
                    ]);
                } else {
                    $call = CallModel::create([
                        'company_id' => $company?->id ?? 0,
                        'contact_id' => $contact?->id ?? null,
                        'direction' => 'UIC',
                        'status' => strtolower($event),
                        'wa_call_id' => $waCallId,
                        'wa_user_id' => $from,
                        'meta' => $value,
                    ]);
                }
                // Broadcast incoming connect to Pusher for instant UI update
                if (strtolower($event) === 'connect' && $company) {
                    try {
                        event(new IncomingCall((int) $company->id, [
                            'id' => $call->id,
                            'wa_user_id' => $call->wa_user_id,
                            'status' => $call->status,
                            'contact_id' => $call->contact_id,
                            'contact_name' => optional(Contact::find($call->contact_id))->name,
                            'contact_avatar' => optional(Contact::find($call->contact_id))->avatar,
                            'wa_call_id' => $waCallId,
                            'offer' => [
                                'sdp' => data_get($value, 'calls.0.session.sdp'),
                                'type' => data_get($value, 'calls.0.session.sdp_type', 'offer'),
                            ],
                        ]));
                    } catch (\Throwable $th) {
                        Log::error('IncomingCall broadcast error', ['e' => $th->getMessage()]);
                    }
                }

                // Broadcast terminate to close UI
                if (strtolower($event) === 'terminate' && $company) {
                    try {
                        event(new CallEnded((int) $company->id, [
                            'id' => $call->id,
                            'wa_call_id' => $waCallId,
                            'status' => 'terminate',
                        ]));
                    } catch (\Throwable $th) {
                        Log::error('CallEnded broadcast error', ['e' => $th->getMessage()]);
                    }
                }

                return response()->json(['ok' => true, 'id' => $call->id]);
            }

            // Fallback mapping using messages/statuses to track call permission replies (BIC flow)
            if (isset($value['messages'][0])) {
                $msg = $value['messages'][0];
                if (($msg['type'] ?? '') === 'interactive') {
                    $type = $msg['interactive']['type'] ?? '';
                    if ($type === 'button_reply') {
                        $replyId = $msg['interactive']['button_reply']['id'] ?? '';
                        if (in_array($replyId, ['wa_call_ok', 'wa_call_no'])) {
                            $status = $replyId === 'wa_call_ok' ? 'permission_granted' : 'permission_declined';
                            $call = CallModel::create([
                                'company_id' => $company?->id ?? 0,
                                'contact_id' => null,
                                'direction' => 'BIC',
                                'status' => $status,
                                'wa_user_id' => $msg['from'] ?? null,
                                'meta' => $msg,
                            ]);

                            return response()->json(['ok' => true, 'id' => $call->id]);
                        }
                    }
                }
            }

            // Status updates for calls (future) or messages — ignore for now
            return response()->json(['ok' => true]);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json(['ok' => false]);
        }
    }
}
