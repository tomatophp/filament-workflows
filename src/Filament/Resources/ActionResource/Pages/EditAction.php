<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;

class EditAction extends EditRecord
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
