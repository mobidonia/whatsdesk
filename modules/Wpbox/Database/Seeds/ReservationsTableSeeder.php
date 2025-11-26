<?php

namespace Modules\Wpbox\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Reminders\Models\Reservation;
use Modules\Reminders\Models\Source;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //If not demo
        if(!config('settings.is_demo',false)){
            return;
        }

        try {
            // Add demo reservations here if needed
            Source::create([
                'name' => 'Web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //Create a new reservation
            DB::table('rem_reservations')->insert([
                'id' => 1,
                'company_id' => 1,
                'contact_id' => 1,
                'source_id' => 1,
                'start_date' => '2025-01-03 20:59:00',
                'end_date' => '2025-01-05 20:59:00',
                'status' => 1,
                'notes' => null,
                'external_id' => '1234',
                'created_at' => '2025-01-03 19:59:56',
                'updated_at' => '2025-01-03 19:59:56'
            ]);
            
        } catch (\Exception $e) {
            // Log error
            Log::error('Error creating reservation: ' . $e->getMessage());
        }

        Model::reguard();
    }
}
