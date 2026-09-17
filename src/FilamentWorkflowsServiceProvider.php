<?php

namespace TomatoPHP\FilamentWorkflows;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentWorkflows\Console\FilamentWorkflowsInstall;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelCreated;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelDeleted;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelUpdated;
use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;
use TomatoPHP\FilamentWorkflows\Listeners\ModelCreatedListener;
use TomatoPHP\FilamentWorkflows\Listeners\ModelDeletedListener;
use TomatoPHP\FilamentWorkflows\Listeners\ModelUpdatedListener;
use TomatoPHP\FilamentWorkflows\Listeners\WebhookListener;
use TomatoPHP\FilamentWorkflows\Services\WorkflowServices;

class FilamentWorkflowsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register generate command
        $this->commands([
            FilamentWorkflowsInstall::class,
        ]);

        // Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-workflows.php', 'filament-workflows');

        // Publish Config
        $this->publishes([
            __DIR__.'/../config/filament-workflows.php' => config_path('filament-workflows.php'),
        ], 'filament-workflows-config');

        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish Migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-workflows-migrations');
        // Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-workflows');

        // Publish Views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament-workflows'),
        ], 'filament-workflows-views');

        // Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-workflows');

        // Publish Lang
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('lang/vendor/filament-workflows'),
        ], 'filament-workflows-lang');

        // Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->app->singleton('filament-workflows', function () {
            return new WorkflowServices;
        });
    }

    public function boot(): void
    {
        Event::listen(ModelCreated::class, ModelCreatedListener::class);
        Event::listen(ModelUpdated::class, ModelUpdatedListener::class);
        Event::listen(ModelDeleted::class, ModelDeletedListener::class);
        Event::listen(WebhookEvent::class, WebhookListener::class);
    }
}
