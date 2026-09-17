<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource;

class ListWorkflows extends ListRecords
{
    protected static string $resource = WorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
