<?php

namespace TomatoPHP\FilamentWorkflows\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelCreated;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelDeleted;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelUpdated;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowModel;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;

use function Filament\Support\get_model_label;

/**
 * Add to an Eloquent model so "model" triggers fire on created / updated / deleted.
 *
 * Triggers are looked up when the model event happens (not while the model boots),
 * so the trait is safe before the workflow tables are migrated.
 */
trait InteractsWithWorkflow
{
    public static function bootInteractsWithWorkflow(): void
    {
        FilamentWorkflows::model(
            WorkflowModel::make(get_model_label(static::class))
                ->model(static::class)
        );

        static::created(function (Model $instance): void {
            foreach (WorkflowRunner::modelTriggers($instance, 'created') as $trigger) {
                Event::dispatch(new ModelCreated($instance, $trigger));
            }
        });

        static::updated(function (Model $instance): void {
            foreach (WorkflowRunner::modelTriggers($instance, 'updated') as $trigger) {
                Event::dispatch(new ModelUpdated($instance, $trigger));
            }
        });

        static::deleted(function (Model $instance): void {
            foreach (WorkflowRunner::modelTriggers($instance, 'deleted') as $trigger) {
                Event::dispatch(new ModelDeleted($instance, $trigger));
            }
        });
    }
}
