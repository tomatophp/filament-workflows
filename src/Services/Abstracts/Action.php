<?php

namespace TomatoPHP\FilamentWorkflows\Services\Abstracts;

use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;

abstract class Action
{
    abstract public static function run(mixed $event, TriggerHasAction $action): void;

    abstract public static function form(): array;
}
