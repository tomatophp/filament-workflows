<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\Pages;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowLogResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;

class WorkflowLogResource extends Resource
{
    protected static ?string $model = WorkflowLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('team_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_id')
                    ->maxLength(255),
                Forms\Components\Select::make('workflow_id')
                    ->relationship('workflow', 'name')
                    ->required(),
                Forms\Components\Select::make('trigger_id')
                    ->relationship('trigger', 'name'),
                Forms\Components\Select::make('action_id')
                    ->relationship('action', 'name'),
                Forms\Components\TextInput::make('log'),
                Forms\Components\TextInput::make('payload'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->description(fn($record) => $record->created_at->diffForHumans())
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('log'),
                Tables\Columns\TextColumn::make('trigger.name')
                    ->icon('heroicon-s-arrow-down-circle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action.name')
                    ->icon('heroicon-s-arrow-small-right')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflowLogs::route('/'),
            'create' => Pages\CreateWorkflowLog::route('/create'),
            'edit' => Pages\EditWorkflowLog::route('/{record}/edit'),
        ];
    }
}
