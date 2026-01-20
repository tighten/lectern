<?php

namespace Tightenco\Lectern\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'lectern:install';

    protected $description = 'Install the Lectern forum package';

    public function handle(): int
    {
        $this->info('Installing Lectern...');

        $this->publishConfig();
        $this->publishMigrations();
        $this->runMigrations();

        $this->newLine();
        $this->info('Lectern installed successfully!');
        $this->newLine();
        $this->line('Add the HasLectern trait to your User model:');
        $this->newLine();
        $this->line('  use Tightenco\Lectern\Traits\HasLectern;');
        $this->newLine();
        $this->line('  class User extends Authenticatable');
        $this->line('  {');
        $this->line('      use HasLectern;');
        $this->line('  }');

        return self::SUCCESS;
    }

    protected function publishConfig(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'lectern-config',
        ]);
    }

    protected function publishMigrations(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'lectern-migrations',
        ]);
    }

    protected function runMigrations(): void
    {
        if ($this->confirm('Run migrations now?', true)) {
            $this->call('migrate');
        }
    }
}
