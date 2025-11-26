<?php

namespace Modules\Knowledge\Console;

use Illuminate\Console\Command;

class SeedKnowledge extends Command
{
    protected $signature = 'knowledge:seed {--demo : Temporarily enable demo mode for this run}';

    protected $description = 'Seed Knowledge module demo data using KnowledgeDatabaseSeeder';

    public function handle(): int
    {
        if ($this->option('demo')) {
            config(['settings.is_demo' => true]);
        }

        $this->call('db:seed', [
            '--class' => \Modules\Knowledge\Database\Seeds\KnowledgeDatabaseSeeder::class,
            '--force' => true,
        ]);

        $this->info('Knowledge module seeded.');

        return self::SUCCESS;
    }
}


