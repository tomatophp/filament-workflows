<?php

namespace TomatoPHP\FilamentWorkflows\Console;

use Illuminate\Console\Command;

class FilamentWorkflowsInstall extends Command
{
    /**
     * @var string
     */
    protected $name = 'filament-workflows:install';

    /**
     * @var string
     */
    protected $description = 'Install the workflows package: run its migrations and clear the caches';

    public function handle(): int
    {
        $this->info('Running the workflow migrations');
        $this->call('migrate', ['--force' => true]);
        $this->call('optimize:clear');
        $this->info('Filament workflows installed successfully.');

        return self::SUCCESS;
    }
}
