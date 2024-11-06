<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages;

use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAction extends EditRecord
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
