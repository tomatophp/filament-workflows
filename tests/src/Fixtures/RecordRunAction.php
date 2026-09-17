<?php

namespace TomatoPHP\FilamentWorkflows\Tests\Fixtures;

use Filament\Forms\Components\TextInput;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Services\Abstracts\Action;

class RecordRunAction extends Action
{
    /**
     * @var array<int, array{event: mixed, payload: mixed}>
     */
    public static array $runs = [];

    public static function run(mixed $event, TriggerHasAction $action): void
    {
        static::$runs[] = [
            'event' => $event,
            'payload' => $action->payload,
        ];
    }

    public static function form(): array
    {
        return [
            TextInput::make('message'),
        ];
    }
}
