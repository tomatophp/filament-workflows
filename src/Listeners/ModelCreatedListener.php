<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelCreated;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;

class ModelCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ModelCreated $event): void
    {
        $workflows = Workflow::query()->whereHas('triggers', function ($query) use ($event) {
            $query->where('trigger_id', $event->trigger->id);
        })->where('is_active', 1)->get();

        foreach ($workflows as $workflow) {
            $getWorkflowTrigger = WorkflowHasTrigger::query()->where('workflow_id', $workflow->id)
                ->where('trigger_id', $event->trigger->id)
                ->first();

            if ($getWorkflowTrigger) {
                $actions = $getWorkflowTrigger->actions;
                if ($getWorkflowTrigger->count() > 0) {
                    foreach ($actions as $action) {
                        app($action->action->action)::run($event, $action);

                        $workflow->logs()->create([
                            'action_id' => $action->action_id,
                            'trigger_id' => $event->trigger->id,
                            'log' => 'Action: '.$action->action->action.' executed',
                            'payload' => json_encode($event->data->toArray()),
                        ]);
                    }
                }
            }
        }
    }
}
