<?php

namespace TomatoPHP\FilamentWorkflows;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;
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
            TriggerResource::class,
            ActionResource::class,
            WorkflowLogResource::class,
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
