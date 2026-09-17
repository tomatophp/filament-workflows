<?php

use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowEvent;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\OrderPlaced;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\RecordRunAction;

it('runs event triggers when a registered event is dispatched', function () {
    RecordRunAction::$runs = [];

    FilamentWorkflows::event(WorkflowEvent::make('Order placed')->event(OrderPlaced::class));

    makeWorkflow(['type' => 'event', 'event' => OrderPlaced::class, 'where' => 'total', 'is' => '>', 'value' => 100]);

    event(new OrderPlaced(1, 50));
    expect(RecordRunAction::$runs)->toBeEmpty();

    event(new OrderPlaced(2, 250));

    expect(RecordRunAction::$runs)->toHaveCount(1)
        ->and(RecordRunAction::$runs[0]['event'])->toBeInstanceOf(OrderPlaced::class)
        ->and(WorkflowLog::query()->sole()->payload)->toBe(['orderId' => 2, 'total' => 250]);
});
