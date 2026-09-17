<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use TomatoPHP\FilamentWorkflows\Events\Models\ModelUpdated;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;

class ModelUpdatedListener
{
    /**
     * Run the actions of every active workflow using the "updated" trigger.
     */
    public function handle(ModelUpdated $event): void
    {
        WorkflowRunner::run($event->trigger, $event, WorkflowRunner::toPayload($event->data), $event->data);
    }
}
