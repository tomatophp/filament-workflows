<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource;

class EditWorkflow extends EditRecord
{
    protected static string $resource = WorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
