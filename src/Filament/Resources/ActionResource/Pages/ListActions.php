<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages;

use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActions extends ListRecords
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
