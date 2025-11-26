<?php

namespace Modules\Websupportwidget\Console;

use Illuminate\Console\Command;

class SeedWebsupportwidget extends Command
{
    protected $signature = 'websupportwidget:seed {--demo : Temporarily enable demo mode for this run}';

    protected $description = 'Seed Websupportwidget module demo data using WebsupportwidgetDatabaseSeeder';

    public function handle(): int
    {
        if ($this->option('demo')) {
            config(['settings.is_demo' => true]);
        }

        $this->call('db:seed', [
            '--class' => \Modules\Websupportwidget\Database\Seeds\WebsupportwidgetDatabaseSeeder::class,
            '--force' => true,
        ]);

        $this->info('Websupportwidget module seeded.');

        return self::SUCCESS;
    }
}


