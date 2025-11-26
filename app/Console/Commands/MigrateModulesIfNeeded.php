<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateModulesIfNeeded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-modules-if-needed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force migration modules if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Try retrieving the "is_modules_migrated" flag from env_settings, default to false
        $isMigrated = false;
        try {
            $row = DB::table('env_settings')->where('key', 'is_modules_migrated')->first();
            $isMigrated = $row ? filter_var($row->value, FILTER_VALIDATE_BOOLEAN) : false;
        } catch (\Exception $e) {
            // Table may not exist yet – treat as not migrated
            $isMigrated = false;
        }

        if ($isMigrated) {
            $this->info('✓ Modules already migrated, skipping...');
            return Command::SUCCESS;
        }

        $this->info('🔄 Migrating modules...');
        try {
            // Run the module migration
            Artisan::call('module:seed', ['--force' => true]);
            // Set the flag in env_settings
            if (DB::table('env_settings')->where('key', 'is_modules_migrated')->exists()) {
                DB::table('env_settings')->where('key', 'is_modules_migrated')->update(['value' => 'true']);
            } else {
                DB::table('env_settings')->insert(['key' => 'is_modules_migrated', 'value' => 'true']);
            }
            $this->info('✅ Modules migrated successfully and flag set!');
        } catch (\Exception $e) {
            $this->error('❌ Failed to migrate modules: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
