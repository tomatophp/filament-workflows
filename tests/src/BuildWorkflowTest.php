<?php

use Filament\Actions\Testing\TestAction;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\BuildWorkflow;
use TomatoPHP\FilamentWorkflows\Models\Action;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowAction;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowModel;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\NoSettingsAction;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\RecordRunAction;
use TomatoPHP\FilamentWorkflows\Tests\Models\Post;
use TomatoPHP\FilamentWorkflows\Tests\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    FilamentWorkflows::action([
        WorkflowAction::make('Record run')->action(RecordRunAction::class),
        WorkflowAction::make('No settings')->action(NoSettingsAction::class),
    ]);

    FilamentWorkflows::model(WorkflowModel::make('Posts')->model(Post::class));

    $this->records = makeWorkflow(['name' => 'Post created', 'type' => 'model', 'model_type' => Post::class, 'model_action' => 'created']);
});

it('renders the builder page with triggers and actions', function () {
    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('Post created')
        ->assertSee('Record the run')
        ->assertSee('RecordRunAction')
        ->assertSeeHtml('Edit Trigger')
        ->assertSeeHtml('Detach Trigger')
        ->assertSeeHtml('Create Action')
        ->assertSeeHtml('Action Settings')
        ->assertSeeHtml('Remove Action');
});

it('renders the builder page for a workflow without triggers', function () {
    $this->records['workflow']->triggers()->detach();

    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('This workflow has no triggers.');
});

it('creates a trigger from the builder', function () {
    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->callAction('trigger', data: [
            'type' => 'webhook',
            'name' => 'Stripe paid',
            'webhook' => 'invoice_paid',
        ])
        ->assertHasNoActionErrors();

    $trigger = Trigger::query()->where('name', 'Stripe paid')->firstOrFail();

    expect($this->records['workflow']->triggers()->pluck('triggers.id'))->toContain($trigger->id);
});

it('edits the trigger through the rendered trigger action', function () {
    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->callAction(TestAction::make('editTrigger')->arguments(['trigger' => $this->records['trigger']->id]), data: [
            'name' => 'Post published',
            'model_action' => 'updated',
        ])
        ->assertHasNoActionErrors();

    expect($this->records['trigger']->refresh())
        ->name->toBe('Post published')
        ->model_action->toBe('updated');
});

it('creates an action on a trigger from the builder', function () {
    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->callAction(TestAction::make('createActionForTrigger')->arguments(['trigger' => $this->records['trigger']->id]), data: [
            'name' => 'Second action',
            'action' => NoSettingsAction::class,
        ])
        ->assertHasNoActionErrors();

    expect(TriggerHasAction::query()->count())->toBe(2);
});

it('only shows the action settings when the action class has a form', function () {
    $noSettingsAction = Action::query()->create(['name' => 'Plain action', 'action' => NoSettingsAction::class]);

    $noSettingsLink = TriggerHasAction::query()->create([
        'workflows_has_triggers_id' => $this->records['triggerAction']->workflows_has_triggers_id,
        'action_id' => $noSettingsAction->id,
    ]);

    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->assertActionVisible(TestAction::make('editActionSettings')->arguments(['action' => $this->records['triggerAction']->id]))
        ->assertActionHidden(TestAction::make('editActionSettings')->arguments(['action' => $noSettingsLink->id]));
});

it('saves the action settings', function () {
    livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()])
        ->callAction(TestAction::make('editActionSettings')->arguments(['action' => $this->records['triggerAction']->id]), data: [
            'message' => 'changed',
        ])
        ->assertHasNoActionErrors();

    expect($this->records['triggerAction']->refresh()->payload)->toBe(['message' => 'changed']);
});

it('removes an action and detaches a trigger', function () {
    $page = livewire(BuildWorkflow::class, ['record' => $this->records['workflow']->getRouteKey()]);

    $page->callAction(TestAction::make('removeActionFromTrigger')->arguments(['action' => $this->records['triggerAction']->id]));
    expect(TriggerHasAction::query()->count())->toBe(0);

    $page->callAction(TestAction::make('deleteTrigger')->arguments(['trigger' => $this->records['trigger']->id]));
    expect($this->records['workflow']->triggers()->count())->toBe(0)
        ->and(Trigger::query()->count())->toBe(1);
});
