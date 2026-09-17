<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource;

class WorkflowLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function table(Table $table): Table
    {
        return WorkflowLogResource::table($table);
    }

    public function form(Schema $schema): Schema
    {
        return WorkflowLogResource::form($schema);
    }
}
