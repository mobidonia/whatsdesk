<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class SeedIfNeeded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-if-needed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database only if it hasn\'t been seeded yet';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if the database has already been seeded
        
        if (User::exists()) {
            $this->info('✓ Database already seeded, skipping...');
            return Command::SUCCESS;
        }

        $this->info('🌱 Seeding database...');
        
        // Run the seeder
        Artisan::call('db:seed', ['--force' => true]);

        /*try {
            $this->info('✨ Importing translations...');
            Artisan::call('translation:import');
        } catch (\Exception $e) {
            $this->error('Error importing translations: ' . $e->getMessage());
        }*/
        
        $this->info('✅ Database seeded successfully!');
        
        return Command::SUCCESS;
    }
}