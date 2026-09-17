<?php

use TomatoPHP\FilamentWorkflows\Models\Action;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;

it('creates every model through its factory', function (string $model) {
    $record = $model::factory()->create();

    expect($record->exists)->toBeTrue()
        ->and($model::query()->count())->toBeGreaterThanOrEqual(1);
})->with([
    Workflow::class,
    Trigger::class,
    Action::class,
    WorkflowLog::class,
]);

it('creates an active workflow with the active state', function () {
    expect(Workflow::factory()->active()->create()->is_active)->toBeTrue();
});
