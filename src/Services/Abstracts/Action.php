<?php

namespace TomatoPHP\FilamentWorkflows\Services\Abstracts;

use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;

abstract class Action
{
    public static abstract function run(mixed $event, TriggerHasAction $action): void;
    public static abstract function form(): array;
}
