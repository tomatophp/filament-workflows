<?php

namespace TomatoPHP\FilamentWorkflows;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource;

class FilamentWorkflowsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-workflows';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            WorkflowResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): self
    {
        return new FilamentWorkflowsPlugin;
    }
}
