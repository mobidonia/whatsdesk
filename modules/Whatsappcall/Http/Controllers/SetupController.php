<?php

namespace Modules\Whatsappcall\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    use \Modules\Whatsappcall\Traits\WhatsappCall;

    public function index()
    {
        $company = $this->getCompany();
        $settings = [
            'enabled' => (bool) $company->getConfig('whatsapp_calling_enabled', false),
            'inbound_allowed' => (bool) $company->getConfig('whatsapp_calling_inbound_allowed', true),
            'timezone_id' => $company->getConfig('whatsapp_calling_timezone_id', 'UTC'),
            'hours_status' => $company->getConfig('whatsapp_calling_hours_status', 'DISABLED'),
            'weekly_operating_hours' => json_decode($company->getConfig('whatsapp_calling_weekly_operating_hours', '[]'), true) ?: [],
            'holiday_schedule' => json_decode($company->getConfig('whatsapp_calling_holiday_schedule', '[]'), true) ?: [],
            'call_icon_visibility' => $company->getConfig('whatsapp_call_icon_visibility', 'DEFAULT'),
            'callback_permission_status' => $company->getConfig('whatsapp_calling_callback_permission_status', 'DISABLED'),
        ];
        return view('whatsappcall::setup.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'sometimes|boolean',
            'inbound_allowed' => 'sometimes|boolean',
            'hours_status' => 'required|in:ENABLED,DISABLED',
            'timezone_id' => 'required|string',
            'call_icon_visibility' => 'nullable|string',
            'callback_permission_status' => 'required|in:ENABLED,DISABLED',
            // weekly schedule fields are processed dynamically
        ]);

        $company = $this->getCompany();
        $company->setConfig('whatsapp_calling_enabled', $validated['enabled'] ?? false);
        $company->setConfig('whatsapp_calling_inbound_allowed', $validated['inbound_allowed'] ?? true);
        $company->setConfig('whatsapp_calling_hours_status', $validated['hours_status']);
        $company->setConfig('whatsapp_calling_timezone_id', $validated['timezone_id']);
        $company->setConfig('whatsapp_call_icon_visibility', $validated['call_icon_visibility'] ?? 'DEFAULT');
        $company->setConfig('whatsapp_calling_callback_permission_status', $validated['callback_permission_status']);

        // Build weekly operating hours from request data
        $days = ['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'];
        $weekly = [];
        foreach ($days as $day) {
            $enabled = (bool) $request->input("weekly.$day.enabled", false);
            $open = $request->input("weekly.$day.open_time");
            $close = $request->input("weekly.$day.close_time");
            if ($enabled && $open && $close) {
                $weekly[] = [
                    'day_of_week' => $day,
                    'open_time' => str_replace(':', '', $open),
                    'close_time' => str_replace(':', '', $close),
                ];
            }
        }
        $company->setConfig('whatsapp_calling_weekly_operating_hours', json_encode($weekly));

        // Holidays
        $holidaysInput = $request->input('holidays', []);
        $holidays = [];
        if (is_array($holidaysInput)) {
            foreach ($holidaysInput as $h) {
                if (!empty($h['date'])) {
                    $holidays[] = [
                        'date' => $h['date'],
                        'start_time' => isset($h['start_time']) ? str_replace(':','',$h['start_time']) : '0000',
                        'end_time' => isset($h['end_time']) ? str_replace(':','',$h['end_time']) : '2359',
                    ];
                }
            }
        }
        $company->setConfig('whatsapp_calling_holiday_schedule', json_encode($holidays));

        // Push these to Meta call settings if token/number is configured
        $result = $this->syncCallSettingsWithMeta($company);

        if (is_array($result) && isset($result['ok']) && !$result['ok']) {
            $body = $result['body'] ?? null;
            $details = is_array($body) ? json_encode($body) : ($result['error_message'] ?? (string) $body);
            $msg = __('Failed to update WhatsApp Calling settings: ').$details;
            return redirect()->back()->with('error', $msg);
        }

        $okMsg = __('WhatsApp Calling settings saved.');
        if (is_array($result)) {
            $okMsg .= ' (HTTP '.($result['status'] ?? '200').')';
            if (isset($result['body'])) {
                $okMsg .= ' - '.(is_array($result['body']) ? json_encode($result['body']) : (string) $result['body']);
            }
        }
        return redirect()->back()->with('status', $okMsg);
    }
}

