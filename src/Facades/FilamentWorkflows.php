<?php

namespace TomatoPHP\FilamentWorkflows\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void model(string|array $model)
 * @method static \Illuminate\Support\Collection getModels()
 * @method static void event(string|array $event)
 * @method static \Illuminate\Support\Collection getEvents()
 * @method static void action(string|array $action)
 * @method static \Illuminate\Support\Collection getActions()
 */
class FilamentWorkflows extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'filament-workflows';
    }
}
