<?php

namespace TomatoPHP\FilamentWorkflows\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void model(string|array $model)
 * @method static Collection getModels()
 * @method static void event(string|array $event)
 * @method static Collection getEvents()
 * @method static void action(string|array $action)
 * @method static Collection getActions()
 */
class FilamentWorkflows extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-workflows';
    }
}
