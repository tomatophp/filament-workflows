<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages\CreateWorkflowLog;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages\EditWorkflowLog;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages\ListWorkflowLogs;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;
use UnitEnum;

class WorkflowLogResource extends Resource
{
    protected static ?string $model = WorkflowLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Workflows';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('workflow_id')
                    ->relationship('workflow', 'key')
                    ->getOptionLabelFromRecordUsing(fn (Workflow $record) => (string) $record->name)
                    ->required(),
                Select::make('trigger_id')
                    ->relationship('trigger', 'name'),
                Select::make('action_id')
                    ->relationship('action', 'name'),
                TextInput::make('log')
                    ->columnSpanFull(),
                KeyValue::make('payload')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->description(fn (WorkflowLog $record) => $record->created_at?->diffForHumans())
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('workflow.name')
                    ->label('Workflow'),
                TextColumn::make('log')
                    ->wrap(),
                TextColumn::make('trigger.name')
                    ->icon('heroicon-s-arrow-down-circle'),
                TextColumn::make('action.name')
                    ->icon('heroicon-s-arrow-small-right'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('workflow_id')
                    ->label('Workflow')
                    ->relationship('workflow', 'key'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowLogs::route('/'),
            'create' => CreateWorkflowLog::route('/create'),
            'edit' => EditWorkflowLog::route('/{record}/edit'),
        ];
    }
}
