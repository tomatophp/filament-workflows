<?php

use Filament\Facades\Filament;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource;
use TomatoPHP\FilamentWorkflows\FilamentWorkflowsPlugin;
use TomatoPHP\FilamentWorkflows\FilamentWorkflowsServiceProvider;
use TomatoPHP\FilamentWorkflows\Services\WorkflowServices;

it('boots the service provider', function () {
    expect(app()->getProvider(FilamentWorkflowsServiceProvider::class))->not->toBeNull()
        ->and(app('filament-workflows'))->toBeInstanceOf(WorkflowServices::class)
        ->and(app('filament-workflows'))->toBe(app('filament-workflows'))
        ->and(config('filament-workflows'))->toHaveKey('webhook_token');
});

it('registers the plugin and its resources on the panel', function () {
    $panel = Filament::getPanel('admin');

    expect($panel->hasPlugin('filament-workflows'))->toBeTrue()
        ->and($panel->getPlugin('filament-workflows'))->toBeInstanceOf(FilamentWorkflowsPlugin::class)
        ->and($panel->getResources())->toContain(
            WorkflowResource::class,
            TriggerResource::class,
            ActionResource::class,
            WorkflowLogResource::class,
        );
});
