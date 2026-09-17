<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;

class EventTriggerListener
{
    /**
     * Listens to every event registered with FilamentWorkflows::event() and runs
     * the triggers of type "event" that point to it.
     */
    public function handle(object $event): void
    {
        if (! WorkflowRunner::tablesExist()) {
            return;
        }

        $triggers = Trigger::query()
            ->where('type', 'event')
            ->where('event', $event::class)
            ->get();

        if ($triggers->isEmpty()) {
            return;
        }

        $payload = WorkflowRunner::toPayload($event);

        foreach ($triggers as $trigger) {
            WorkflowRunner::run($trigger, $event, $payload);
        }
    }
}
