<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use TomatoPHP\FilamentWorkflows\Events\Models\ModelDeleted;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;

class ModelDeletedListener
{
    /**
     * Run the actions of every active workflow using the "deleted" trigger.
     */
    public function handle(ModelDeleted $event): void
    {
        WorkflowRunner::run($event->trigger, $event, WorkflowRunner::toPayload($event->data), $event->data);
    }
}
