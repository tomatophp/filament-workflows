<?php

use TomatoPHP\FilamentWorkflows\Models\Action;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\RecordRunAction;
use TomatoPHP\FilamentWorkflows\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Build a workflow with one trigger and one action attached to that trigger.
 *
 * @param  array<string, mixed>  $trigger
 * @return array{workflow: Workflow, trigger: Trigger, action: Action, triggerAction: TriggerHasAction}
 */
function makeWorkflow(array $trigger, string $handler = RecordRunAction::class, bool $active = true): array
{
    $workflow = Workflow::query()->create([
        'name' => 'Notify on new post',
        'key' => 'notify-on-new-post-'.uniqid(),
        'is_active' => $active,
    ]);

    $triggerModel = Trigger::query()->create(array_merge(['name' => 'A trigger'], $trigger));
    $workflow->triggers()->attach($triggerModel->id);

    $action = Action::query()->create([
        'name' => 'Record the run',
        'action' => $handler,
    ]);

    $workflowTrigger = WorkflowHasTrigger::query()
        ->where('workflow_id', $workflow->id)
        ->where('trigger_id', $triggerModel->id)
        ->firstOrFail();

    $triggerAction = $workflowTrigger->actions()->create([
        'action_id' => $action->id,
        'payload' => ['message' => 'hello'],
    ]);

    return [
        'workflow' => $workflow,
        'trigger' => $triggerModel,
        'action' => $action,
        'triggerAction' => $triggerAction,
    ];
}
