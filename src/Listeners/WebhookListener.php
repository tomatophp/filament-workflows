<?php

namespace TomatoPHP\FilamentWorkflows\Listeners;

use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;

class WebhookListener
{
    /**
     * A webhook trigger matches when the request payload contains the key stored
     * in the trigger "webhook" field (and its optional where / is / value condition passes).
     */
    public function handle(WebhookEvent $event): void
    {
        if (! WorkflowRunner::tablesExist()) {
            return;
        }

        $triggers = Trigger::query()->where('type', 'webhook')->whereNotNull('webhook')->get();

        foreach ($triggers as $trigger) {
            if (! array_key_exists($trigger->webhook, $event->data)) {
                continue;
            }

            WorkflowRunner::run($trigger, $event, $event->data);
        }
    }
}
