<?php

namespace Modules\Whatsappcall\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Whatsappcall\Models\Call;
use Modules\Wpbox\Models\Contact;

class ReportsController extends Controller
{
    /**
     * Get call statistics report data
     */
    public function callStatistics(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        $period = $request->get('period', 7);

        $startDate = Carbon::now()->subDays($period);

        // Get call statistics
        $totalCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $answeredCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['answered', 'accept', 'in_progress'])
            ->count();

        $missedCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['missed', 'declined', 'failed'])
            ->count();

        // Calculate average duration for answered calls
        $driver = DB::getDriverName();
        $durationDiffExpression = $driver === 'pgsql'
            ? "EXTRACT(EPOCH FROM (ended_at - answered_at)) / 60"
            : "TIMESTAMPDIFF(MINUTE, answered_at, ended_at)";

        $avgDurationMinutes = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('answered_at')
            ->whereNotNull('ended_at')
            ->selectRaw("AVG($durationDiffExpression) as avg_duration")
            ->value('avg_duration') ?? 0;

        $answerRate = $totalCalls > 0 ? round(($answeredCalls / $totalCalls) * 100, 1) : 0;
        $missedRate = $totalCalls > 0 ? round(($missedCalls / $totalCalls) * 100, 1) : 0;

        // Calculate growth compared to previous period
        $previousStartDate = $startDate->copy()->subDays($period);
        $previousCalls = Call::where('company_id', $company->id)
            ->whereBetween('created_at', [$previousStartDate, $startDate])
            ->count();

        $callsGrowth = $previousCalls > 0 ? round((($totalCalls - $previousCalls) / $previousCalls) * 100, 1) : 0;

        // Get direction breakdown
        $inboundCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->where('direction', 'UIC')
            ->count();

        $outboundCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->where('direction', 'BIC')
            ->count();

        // Get status distribution
        $statusDistribution = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Normalize status names
        $normalizedStatus = [];
        foreach ($statusDistribution as $status => $count) {
            $normalizedKey = $this->normalizeCallStatus($status);
            $normalizedStatus[$normalizedKey] = ($normalizedStatus[$normalizedKey] ?? 0) + $count;
        }

        $data = [
            'stats' => [
                'totalCalls' => $totalCalls,
                'answeredCalls' => $answeredCalls,
                'missedCalls' => $missedCalls,
                'avgDuration' => round($avgDurationMinutes, 1),
                'answerRate' => $answerRate,
                'missedRate' => $missedRate,
                'callsGrowth' => $callsGrowth,
            ],
            'directionData' => [
                'inbound' => $inboundCalls,
                'outbound' => $outboundCalls,
            ],
            'statusData' => $normalizedStatus,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Get call performance report data
     */
    public function callPerformance(Request $request)
    {
        $this->ownerAndStaffOnly();
        $company = $this->getCompany();
        $period = $request->get('period', 7);

        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Get daily call statistics
        $dailyStats = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $totalCalls = Call::where('company_id', $company->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $answeredCalls = Call::where('company_id', $company->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->whereIn('status', ['answered', 'accept', 'in_progress'])
                ->count();

            $missedCalls = Call::where('company_id', $company->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->whereIn('status', ['missed', 'declined', 'failed'])
                ->count();

            $dailyStats[] = [
                'date' => $date->format('M d'),
                'total' => $totalCalls,
                'answered' => $answeredCalls,
                'missed' => $missedCalls,
            ];
        }

		// Get peak call time
		$driver = DB::getDriverName();
		$hourExpression = $driver === 'pgsql'
			? "EXTRACT(HOUR FROM created_at)"
			: "HOUR(created_at)";

		$peakHour = Call::where('company_id', $company->id)
			->where('created_at', '>=', $startDate)
			->selectRaw("$hourExpression as hour, COUNT(*) as count")
			->groupByRaw($hourExpression)
			->orderByDesc('count')
			->first();

        $peakCallTime = $peakHour ? sprintf('%02d:00', $peakHour->hour) : '-';
        $peakCallCount = $peakHour ? $peakHour->count : 0;

        // Calculate success rate (answered calls)
        $totalCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $successfulCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['answered', 'accept'])
            ->count();

        $successRate = $totalCalls > 0 ? round(($successfulCalls / $totalCalls) * 100, 1) : 0;

        // Calculate average wait time (time from started to answered)
        $driver = DB::getDriverName();
        $waitDiffExpression = $driver === 'pgsql'
            ? "EXTRACT(EPOCH FROM (answered_at - started_at))"
            : "TIMESTAMPDIFF(SECOND, started_at, answered_at)";

        $avgWaitTime = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('answered_at')
            ->selectRaw("AVG($waitDiffExpression) as avg_wait")
            ->value('avg_wait') ?? 0;

        // Get recent calls with contact information
        $recentCalls = Call::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($call) use ($company) {
                $contact = null;
                if ($call->contact_id) {
                    $contact = Contact::find($call->contact_id);
                } else {
                    // Try to find contact by phone
                    $contact = Contact::where('company_id', $company->id)
                        ->where(function ($query) use ($call) {
                            $query->where('phone', $call->wa_user_id)
                                ->orWhere('phone', '+'.$call->wa_user_id);
                        })
                        ->first();
                }

                $duration = null;
                if ($call->answered_at && $call->ended_at) {
                    $durationMinutes = $call->answered_at->diffInMinutes($call->ended_at);
                    $duration = $durationMinutes > 0 ? $durationMinutes.'m' : '<1m';
                }

                return [
                    'id' => $call->id,
                    'started_at' => $call->started_at,
                    'contact_name' => $contact?->name,
                    'wa_user_id' => $call->wa_user_id,
                    'direction' => $call->direction,
                    'status' => $call->status,
                    'duration' => $duration,
                ];
            });

        $data = [
            'stats' => [
                'peakCallTime' => $peakCallTime,
                'peakCallCount' => $peakCallCount,
                'successRate' => $successRate,
                'successfulCalls' => $successfulCalls,
                'avgWaitTime' => round($avgWaitTime, 1),
            ],
            'chartData' => $dailyStats,
            'recentCalls' => $recentCalls,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Normalize call status for consistent reporting
     */
    private function normalizeCallStatus(string $status): string
    {
        $statusMap = [
            'answered' => 'answered',
            'accept' => 'answered',
            'in_progress' => 'answered',
            'missed' => 'missed',
            'declined' => 'missed',
            'failed' => 'failed',
            'ended' => 'answered',
            'terminate' => 'answered',
        ];

        return $statusMap[$status] ?? $status;
    }
}
