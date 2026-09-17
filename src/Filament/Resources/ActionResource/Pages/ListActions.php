<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;

class ListActions extends ListRecords
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
