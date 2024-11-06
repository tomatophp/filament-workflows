<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Models\ModelCreated;
use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;

class WebhookListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(WebhookEvent $event): void
    {
        $triggers = Trigger::query()->where('type', 'webhook')->get();
        if(count($triggers) > 0) {
            foreach ($triggers as $trigger) {
                if (isset($event->data[$trigger->webhook])) {
                    $workflows = Workflow::query()->whereHas('triggers', function ($query) use ($trigger){
                        $query->where('trigger_id', $trigger->id);
                    })->get();

                    foreach ($workflows as $workflow){
                        $getWorkflowTrigger = WorkflowHasTrigger::query()->where('workflow_id', $workflow->id)
                            ->where('trigger_id', $trigger->id)
                            ->first();

                        if($getWorkflowTrigger){
                            $actions = $getWorkflowTrigger->actions;
                            if(count($actions) > 0){
                                foreach ($actions as $action){
                                    app($action->action->action)::run($event, $action);

                                    $workflow->logs()->create([
                                        "action_id" => $action->action_id,
                                        "trigger_id" => $trigger->id,
                                        "log" => "Action: ".$action->action->action." executed",
                                        "payload" => json_encode($event->data)
                                    ]);
                                }
                            }
                        }
                    }
                }

            }
        }
    }
}
