<?php

namespace Modules\Whatsappcall\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Whatsappcall\Models\Call;
use App\Models\Company;
use Modules\Contacts\Models\Contact;

class SeedWeekCalls extends Command
{
    protected $signature = 'whatsappcall:seed-week-calls {--company=} {--per-day=20}';

    protected $description = 'Generate fake WhatsApp calls for the past week';

    public function handle(): int
    {
        $companyId = $this->option('company');

        if (!$companyId) {
            $companyId = Company::query()->value('id');
        } else {
            if (!Company::query()->whereKey($companyId)->exists()) {
                $this->error('Company not found.');
                return self::FAILURE;
            }
        }

        if (!$companyId) {
            $this->error('No companies found. Create a company first or pass --company=');
            return self::FAILURE;
        }

        $perDay = (int) $this->option('per-day') ?: 20;

        $faker = \Faker\Factory::create();

        $directions = ['UIC', 'BIC'];
        $statuses = ['initiated', 'ringing', 'in_progress', 'ended', 'missed', 'declined', 'failed'];

        $contacts = Contact::query()
            ->where('company_id', $companyId)
            ->pluck('id')
            ->all();

        $total = 0;

        for ($daysAgo = 0; $daysAgo < 7; $daysAgo++) {
            $day = Carbon::now()->subDays($daysAgo);
            $dayStart = $day->copy()->startOfDay();
            $dayEnd = $day->copy()->endOfDay();

            for ($i = 0; $i < $perDay; $i++) {
                $status = $faker->randomElement($statuses);
                $direction = $faker->randomElement($directions);

                $startedAt = Carbon::createFromTimestamp(
                    $faker->numberBetween($dayStart->timestamp, $dayEnd->timestamp)
                );

                $answeredAt = null;
                $endedAt = null;

                if (in_array($status, ['in_progress', 'ended'])) {
                    $answeredAt = (clone $startedAt)->addSeconds($faker->numberBetween(1, 30));
                }

                if (in_array($status, ['ended', 'missed', 'declined', 'failed'])) {
                    $base = $answeredAt ?: $startedAt;
                    $endedAt = (clone $base)->addSeconds($faker->numberBetween(10, 900));
                }

                $contactId = null;
                if (!empty($contacts) && $faker->boolean(60)) {
                    $contactId = $faker->randomElement($contacts);
                }

                Call::query()->create([
                    'company_id' => $companyId,
                    'contact_id' => $contactId,
                    'direction' => $direction,
                    'status' => $status,
                    'wa_call_id' => $faker->boolean(30) ? 'wa_' . Str::uuid()->toString() : null,
                    'wa_user_id' => $faker->boolean(40) ? (string) $faker->numberBetween(1000, 9999) : null,
                    'started_at' => $startedAt,
                    'answered_at' => $answeredAt,
                    'ended_at' => $endedAt,
                    'meta' => [
                        'duration_seconds' => $endedAt && $startedAt ? $endedAt->diffInSeconds($startedAt) : null,
                        'quality' => $faker->randomElement(['good', 'average', 'poor']),
                        'notes' => $faker->sentence(),
                    ],
                ]);

                $total++;
            }
        }

        $this->info("Created {$total} fake calls for company ID {$companyId} over the past 7 days.");

        return self::SUCCESS;
    }
}


