<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrrateModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrrate-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force migration modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('module:seed', ['--force' => true]);
    }
}
