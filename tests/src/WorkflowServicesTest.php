<?php

use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowAction;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowModel;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\NoSettingsAction;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\RecordRunAction;
use TomatoPHP\FilamentWorkflows\Tests\Models\Post;

it('registers an array of actions', function () {
    FilamentWorkflows::action([
        WorkflowAction::make('Record run')->action(RecordRunAction::class),
        WorkflowAction::make('No settings')->action(NoSettingsAction::class),
    ]);

    expect(FilamentWorkflows::getActions()->pluck('label', 'action')->toArray())->toBe([
        RecordRunAction::class => 'Record run',
        NoSettingsAction::class => 'No settings',
    ]);
});

it('registers a single action', function () {
    FilamentWorkflows::action(WorkflowAction::make('Record run')->action(RecordRunAction::class));

    expect(FilamentWorkflows::getActions())->toHaveCount(1);
});

it('registers a model once', function () {
    FilamentWorkflows::model(WorkflowModel::make('Posts')->model(Post::class));
    FilamentWorkflows::model([WorkflowModel::make('Posts')->model(Post::class)]);

    expect(FilamentWorkflows::getModels()->pluck('label', 'model')->toArray())->toBe([Post::class => 'Posts']);
});
