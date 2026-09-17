<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;

class ListWorkflowLogs extends ListRecords
{
    protected static string $resource = WorkflowLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
