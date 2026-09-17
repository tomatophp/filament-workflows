<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use TomatoPHP\FilamentWorkflows\Events\Models\ModelCreated;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;

class ModelCreatedListener
{
    /**
     * Run the actions of every active workflow using the "created" trigger.
     */
    public function handle(ModelCreated $event): void
    {
        WorkflowRunner::run($event->trigger, $event, WorkflowRunner::toPayload($event->data), $event->data);
    }
}
