<?php

namespace Modules\Wpbox\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Wpbox\Models\Contact;
use Modules\Wpbox\Models\Message;

class ReportsController extends Controller
{
    /**
     * Get open conversations report data
     */
    public function openConversations(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();

        // Get conversation statistics
        $totalConversations = Contact::where('company_id', $company->id)
            ->where('has_chat', 1)
            ->count();

        $openConversations = Contact::where('company_id', $company->id)
            ->where('has_chat', 1)
            ->where('resolved_chat', 0)
            ->count();

        $unattendedConversations = Contact::where('company_id', $company->id)
            ->where('has_chat', 1)
            ->where('is_last_message_by_contact', 1)
            ->where('resolved_chat', 0)
            ->count();

        $unassignedConversations = Contact::where('company_id', $company->id)
            ->where('has_chat', 1)
            ->whereNull('user_id')
            ->where('resolved_chat', 0)
            ->count();

        $pendingConversations = Contact::where('company_id', $company->id)
            ->where('has_chat', 1)
            ->where('resolved_chat', 0)
            ->where('is_last_message_by_contact', 0)
            ->count();

        $data = [
            'open' => $openConversations,
            'unattended' => $unattendedConversations,
            'unassigned' => $unassignedConversations,
            'pending' => $pendingConversations,
            'total' => $totalConversations,
            'openPercentage' => $totalConversations > 0 ? round(($openConversations / $totalConversations) * 100, 1) : 0,
            'unattendedPercentage' => $totalConversations > 0 ? round(($unattendedConversations / $totalConversations) * 100, 1) : 0,
            'unassignedPercentage' => $totalConversations > 0 ? round(($unassignedConversations / $totalConversations) * 100, 1) : 0,
            'pendingPercentage' => $totalConversations > 0 ? round(($pendingConversations / $totalConversations) * 100, 1) : 0,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Get conversation traffic report data
     */
    public function conversationTraffic(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        $period = $request->get('period', 7);

        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Get daily statistics
        $dailyStats = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $messages = Message::where('company_id', $company->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $newConversations = Contact::where('company_id', $company->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('has_chat', 1)
                ->count();

            $dailyStats[] = [
                'date' => $date->format('M d'),
                'messages' => $messages,
                'conversations' => $newConversations,
            ];
        }

        // Get overall statistics
        $totalMessages = Message::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $newConversations = Contact::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->where('has_chat', 1)
            ->count();

        // Calculate average response time
        $driver = DB::getDriverName();
		$responseDiffExpression = $driver === 'pgsql'
			? "EXTRACT(EPOCH FROM (m2.created_at - m1.created_at)) / 60"
			: "TIMESTAMPDIFF(MINUTE, m1.created_at, m2.created_at)";
		$isMessageByContactTrue = true;  // Works for both MySQL and Postgres
		$isMessageByContactFalse = false; // Works for both MySQL and Postgres
       
        $avgResponseTime = DB::table('messages as m1')
			->join('messages as m2', function ($join) use ($isMessageByContactFalse) {
                $join->on('m1.contact_id', '=', 'm2.contact_id')
					->whereRaw('m2.id = (SELECT MIN(id) FROM messages WHERE contact_id = m1.contact_id AND id > m1.id AND is_message_by_contact = ?)', [$isMessageByContactFalse]);
            })
            ->where('m1.company_id', $company->id)
			->where('m1.is_message_by_contact', $isMessageByContactTrue)
            ->where('m1.created_at', '>=', $startDate)
            ->selectRaw("AVG($responseDiffExpression) as avg_response_time")
            ->value('avg_response_time') ?? 0;

        $resolvedConversations = Contact::where('company_id', $company->id)
            ->where('resolved_chat', 1)
            ->where('updated_at', '>=', $startDate)
            ->count();

        $resolutionRate = $newConversations > 0 ? round(($resolvedConversations / $newConversations) * 100, 1) : 0;

        // Calculate growth compared to previous period
        $previousStartDate = $startDate->copy()->subDays($period);
        $previousMessages = Message::where('company_id', $company->id)
            ->whereBetween('created_at', [$previousStartDate, $startDate])
            ->count();

        $previousConversations = Contact::where('company_id', $company->id)
            ->whereBetween('created_at', [$previousStartDate, $startDate])
            ->where('has_chat', 1)
            ->count();

        $messagesGrowth = $previousMessages > 0 ? round((($totalMessages - $previousMessages) / $previousMessages) * 100, 1) : 0;
        $conversationsGrowth = $previousConversations > 0 ? round((($newConversations - $previousConversations) / $previousConversations) * 100, 1) : 0;

        $data = [
            'stats' => [
                'totalMessages' => $totalMessages,
                'newConversations' => $newConversations,
                'avgResponseTime' => round($avgResponseTime, 1),
                'resolutionRate' => $resolutionRate,
                'resolvedConversations' => $resolvedConversations,
                'messagesGrowth' => $messagesGrowth,
                'conversationsGrowth' => $conversationsGrowth,
            ],
            'chartData' => $dailyStats,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Get conversation by agent report data
     */
    public function conversationByAgent(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        $period = $request->get('period', 7);

        $startDate = Carbon::now()->subDays($period);

        // Get agent statistics
        $agents = User::whereHas('company', function ($query) use ($company) {
            $query->where('id', $company->id);
        })
            ->orWhere('company_id', $company->id)
            ->get();

        $agentStats = [];
        foreach ($agents as $agent) {
            $conversations = Contact::where('company_id', $company->id)
                ->where('user_id', $agent->id)
                ->where('has_chat', 1)
                ->where('updated_at', '>=', $startDate)
                ->count();

            $messagesSent = Message::where('company_id', $company->id)
                ->where('sender_name', $agent->name)
				->where('is_message_by_contact', false)
                ->where('created_at', '>=', $startDate)
                ->count();

            // Calculate average response time for this agent
            $driver = DB::getDriverName();
            $responseDiffExpression = $driver === 'pgsql'
                ? "EXTRACT(EPOCH FROM (m2.created_at - m1.created_at)) / 60"
                : "TIMESTAMPDIFF(MINUTE, m1.created_at, m2.created_at)";

            $avgResponseTime = DB::table('messages as m1')
				->join('messages as m2', function ($join) {
                    $join->on('m1.contact_id', '=', 'm2.contact_id')
						->whereRaw('m2.id = (SELECT MIN(id) FROM messages WHERE contact_id = m1.contact_id AND id > m1.id AND is_message_by_contact = ?)', [false]);
                })
                ->where('m1.company_id', $company->id)
                ->where('m2.sender_name', $agent->name)
				->where('m1.is_message_by_contact', true)
                ->where('m1.created_at', '>=', $startDate)
                ->selectRaw("AVG($responseDiffExpression) as avg_response_time")
                ->value('avg_response_time') ?? 0;

            $resolvedConversations = Contact::where('company_id', $company->id)
                ->where('user_id', $agent->id)
                ->where('resolved_chat', 1)
                ->where('updated_at', '>=', $startDate)
                ->count();

            $resolutionRate = $conversations > 0 ? round(($resolvedConversations / $conversations) * 100, 1) : 0;

            // Determine agent status (simplified)
            $status = 'Offline';
            if ($messagesSent > 0) {
                $lastMessage = Message::where('company_id', $company->id)
                    ->where('sender_name', $agent->name)
                    ->where('is_message_by_contact', 0)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lastMessage && $lastMessage->created_at->diffInHours(now()) < 1) {
                    $status = 'Online';
                } elseif ($lastMessage && $lastMessage->created_at->diffInHours(now()) < 8) {
                    $status = 'Busy';
                }
            }

            if ($conversations > 0 || $messagesSent > 0) {
                $agentStats[] = [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'conversations' => $conversations,
                    'messagesSent' => $messagesSent,
                    'avgResponseTime' => round($avgResponseTime, 1),
                    'resolutionRate' => $resolutionRate,
                    'status' => $status,
                ];
            }
        }

        // Sort by conversations count
        usort($agentStats, function ($a, $b) {
            return $b['conversations'] - $a['conversations'];
        });

        $data = [
            'agents' => $agentStats,
            'chartData' => $agentStats,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
