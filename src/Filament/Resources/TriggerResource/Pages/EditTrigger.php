<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource;

class EditTrigger extends EditRecord
{
    protected static string $resource = TriggerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
