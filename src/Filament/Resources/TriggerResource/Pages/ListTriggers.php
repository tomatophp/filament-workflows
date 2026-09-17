<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource;

class ListTriggers extends ListRecords
{
    protected static string $resource = TriggerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
