<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;

class WorkflowLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';


    public function table(Table $table): Table
    {
        return WorkflowLogResource::table($table);
    }

    public function form(Form $form): Form
    {
        return WorkflowLogResource::form($form);
    }
}
