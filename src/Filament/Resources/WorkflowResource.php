<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Models\Workflow;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->columnSpanFull()
                    ->searchable()
                    ->options([
                        "workflow" => "Workflow",
                        "trigger" => "Trigger",
                    ])
                    ->default('workflow'),
                Forms\Components\TextInput::make('name')
                    ->lazy()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set){
                        $set('key', str($get('name'))->lower()->slug()->toString());
                    })
                    ->required(),
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn($record) => $record->description)
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('build')
                    ->url(fn($record) => Pages\BuildWorkflow::getUrl(['record' => $record])),
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
            RelationManagers\WorkflowLogsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflows::route('/'),
            'edit' => Pages\EditWorkflow::route('/{record}/edit'),
            'build' => Pages\BuildWorkflow::route('/{record}/build'),
        ];
    }
}
