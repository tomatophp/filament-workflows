<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages;

use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowLog extends EditRecord
{
    protected static string $resource = WorkflowLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
