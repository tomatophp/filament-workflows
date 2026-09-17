<?php

use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages\CreateAction;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages\EditAction;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages\ListActions;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages\CreateTrigger;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages\EditTrigger;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages\ListTriggers;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages\CreateWorkflowLog;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages\EditWorkflowLog;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages\ListWorkflowLogs;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\CreateWorkflow;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\EditWorkflow;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\ListWorkflows;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;
use TomatoPHP\FilamentWorkflows\Tests\Models\Post;
use TomatoPHP\FilamentWorkflows\Tests\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->records = makeWorkflow(['type' => 'model', 'model_type' => Post::class, 'model_action' => 'created']);

    $this->log = WorkflowLog::query()->create([
        'workflow_id' => $this->records['workflow']->id,
        'trigger_id' => $this->records['trigger']->id,
        'action_id' => $this->records['action']->id,
        'log' => 'Action executed',
        'payload' => ['id' => 1],
    ]);
});

it('renders the list pages', function (string $page) {
    livewire($page)->assertSuccessful();
})->with([
    'workflows' => ListWorkflows::class,
    'triggers' => ListTriggers::class,
    'actions' => ListActions::class,
    'logs' => ListWorkflowLogs::class,
]);

it('renders the create pages', function (string $page) {
    livewire($page)->assertSuccessful();
})->with([
    'workflows' => CreateWorkflow::class,
    'triggers' => CreateTrigger::class,
    'actions' => CreateAction::class,
    'logs' => CreateWorkflowLog::class,
]);

it('renders the edit pages', function (string $page, string $key) {
    $record = $key === 'log' ? $this->log : $this->records[$key];

    livewire($page, ['record' => $record->getRouteKey()])->assertSuccessful();
})->with([
    'workflows' => [EditWorkflow::class, 'workflow'],
    'triggers' => [EditTrigger::class, 'trigger'],
    'actions' => [EditAction::class, 'action'],
    'logs' => [EditWorkflowLog::class, 'log'],
]);

it('lists the records on the list pages', function () {
    livewire(ListWorkflows::class)->assertCanSeeTableRecords([$this->records['workflow']]);
    livewire(ListTriggers::class)->assertCanSeeTableRecords([$this->records['trigger']]);
    livewire(ListActions::class)->assertCanSeeTableRecords([$this->records['action']]);
    livewire(ListWorkflowLogs::class)->assertCanSeeTableRecords([$this->log]);
});

it('creates and edits a workflow', function () {
    livewire(CreateWorkflow::class)
        ->fillForm([
            'name' => 'Welcome new users',
            'key' => 'welcome-new-users',
            'description' => 'Send a welcome message',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $workflow = Workflow::query()->where('key', 'welcome-new-users')->firstOrFail();

    expect($workflow->name)->toBe('Welcome new users')
        ->and($workflow->description)->toBe('Send a welcome message')
        ->and($workflow->is_active)->toBeTrue();

    livewire(EditWorkflow::class, ['record' => $workflow->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => 'Welcome new users',
            'key' => 'welcome-new-users',
        ])
        ->fillForm([
            'name' => 'Greet new users',
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($workflow->refresh()->name)->toBe('Greet new users')
        ->and($workflow->key)->toBe('welcome-new-users')
        ->and($workflow->is_active)->toBeFalse();
});

it('uses a valid default icon for new actions', function () {
    livewire(CreateAction::class)
        ->assertSchemaStateSet(['icon' => 'heroicon-s-arrows-up-down']);

    expect(fn () => svg('heroicon-s-arrows-up-down')->toHtml())->not->toThrow(Exception::class);
});
