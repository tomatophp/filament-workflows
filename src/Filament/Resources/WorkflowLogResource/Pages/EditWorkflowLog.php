<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;

class EditWorkflowLog extends EditRecord
{
    protected static string $resource = WorkflowLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
