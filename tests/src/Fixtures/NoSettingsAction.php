<?php

namespace TomatoPHP\FilamentWorkflows\Tests\Fixtures;

use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Services\Abstracts\Action;

class NoSettingsAction extends Action
{
    public static function run(mixed $event, TriggerHasAction $action): void {}

    public static function form(): array
    {
        return [];
    }
}
