<?php

namespace TomatoPHP\FilamentWorkflows\Traits;

use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelCreated;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelDeleted;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelUpdated;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowModel;
use function Filament\Support\get_model_label;

trait InteractsWithWorkflow
{

    public static function boot(){
        parent::boot();

        FilamentWorkflows::model(
            WorkflowModel::make(get_model_label(self::class))
                ->model(get_class(new self()))
        );

        $triggers = Trigger::query()->where('model_type', get_class(new self()))->get();

        if(count($triggers) > 0){
            static::created(function ($instance) use ($triggers){
                foreach ($triggers as $trigger){
                    if($trigger->model_action === 'created') {
                        Event::dispatch(new ModelCreated($instance, $trigger));
                    }
                }
            });

            static::updated(function ($instance) use ($triggers){
                foreach ($triggers as $trigger){
                    if($trigger->model_action === 'updated'){
                        Event::dispatch(new ModelUpdated($instance, $trigger));
                    }
                }
            });

            static::deleted(function ($instance) use ($triggers){
                foreach ($triggers as $trigger){
                    if($trigger->model_action === 'deleted'){
                        Event::dispatch(new ModelDeleted($instance, $trigger));
                    }
                }
            });
        }
    }
}
