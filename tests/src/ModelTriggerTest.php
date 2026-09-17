<?php

use Illuminate\Support\Facades\Schema;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;
use TomatoPHP\FilamentWorkflows\Services\WorkflowRunner;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\RecordRunAction;
use TomatoPHP\FilamentWorkflows\Tests\Models\Post;

beforeEach(function () {
    RecordRunAction::$runs = [];
});

it('runs the action and writes a log when a record is created', function () {
    $records = makeWorkflow(['type' => 'model', 'model_type' => Post::class, 'model_action' => 'created']);

    $post = Post::query()->create(['name' => 'Hello world']);

    expect(RecordRunAction::$runs)->toHaveCount(1)
        ->and(RecordRunAction::$runs[0]['payload'])->toBe(['message' => 'hello']);

    $log = WorkflowLog::query()->sole();

    expect($log->workflow_id)->toBe($records['workflow']->id)
        ->and($log->trigger_id)->toBe($records['trigger']->id)
        ->and($log->action_id)->toBe($records['action']->id)
        ->and($log->log)->toBe('Action: '.RecordRunAction::class.' executed')
        ->and($log->payload)->toBeArray()
        ->and($log->payload['name'])->toBe($post->name);
});

it('runs the action when a record is updated', function () {
    makeWorkflow(['type' => 'model', 'model_type' => Post::class, 'model_action' => 'updated']);

    $post = Post::query()->create(['name' => 'Draft']);
    expect(RecordRunAction::$runs)->toHaveCount(0);

    $post->update(['name' => 'Published']);

    expect(RecordRunAction::$runs)->toHaveCount(1)
        ->and(WorkflowLog::query()->sole()->payload['name'])->toBe('Published');
});

it('runs the action when a record is deleted', function () {
    makeWorkflow(['type' => 'model', 'model_type' => Post::class, 'model_action' => 'deleted']);

    $post = Post::query()->create(['name' => 'Removed']);
    $post->delete();

    expect(RecordRunAction::$runs)->toHaveCount(1)
        ->and(WorkflowLog::query()->count())->toBe(1);
});

it('does not run inactive workflows', function () {
    makeWorkflow(['type' => 'model', 'model_type' => Post::class, 'model_action' => 'created'], active: false);

    Post::query()->create(['name' => 'Hello']);

    expect(RecordRunAction::$runs)->toBeEmpty()
        ->and(WorkflowLog::query()->count())->toBe(0);
});

it('applies the where / is / value condition of the trigger', function () {
    makeWorkflow([
        'type' => 'model',
        'model_type' => Post::class,
        'model_action' => 'created',
        'where' => 'status',
        'is' => '=',
        'value' => 'published',
    ]);

    Post::query()->create(['name' => 'Draft post', 'status' => 'draft']);
    expect(RecordRunAction::$runs)->toBeEmpty();

    Post::query()->create(['name' => 'Live post', 'status' => 'published']);
    expect(RecordRunAction::$runs)->toHaveCount(1);
});

it('logs actions whose class is not a workflow action instead of calling it', function () {
    makeWorkflow(['type' => 'model', 'model_type' => Post::class, 'model_action' => 'created'], handler: stdClass::class);

    Post::query()->create(['name' => 'Hello']);

    expect(WorkflowLog::query()->sole()->log)->toStartWith('Action skipped');
});

it('registers the model when it boots', function () {
    new Post;

    expect(FilamentWorkflows::getModels()->pluck('model'))->toContain(Post::class);
});

it('does not query the triggers while the workflow tables do not exist', function () {
    Schema::dropIfExists('triggers_has_actions');
    Schema::dropIfExists('workflow_logs');
    Schema::dropIfExists('workflows_has_triggers');
    Schema::dropIfExists('triggers');
    WorkflowRunner::flushTableCache();

    $post = Post::query()->create(['name' => 'Before migrations']);

    expect($post->exists)->toBeTrue()
        ->and(RecordRunAction::$runs)->toBeEmpty();
});
