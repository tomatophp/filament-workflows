<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages;
use TomatoPHP\FilamentWorkflows\Models\Trigger;

class TriggerResource extends Resource
{
    protected static ?string $model = Trigger::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('team_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255)
                    ->default('model'),
                Forms\Components\TextInput::make('model_action')
                    ->maxLength(255)
                    ->default('created'),
                Forms\Components\TextInput::make('model_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('model_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('event')
                    ->maxLength(255),
                Forms\Components\TextInput::make('webhook')
                    ->maxLength(255),
                Forms\Components\TextInput::make('where')
                    ->maxLength(255),
                Forms\Components\TextInput::make('is')
                    ->maxLength(255)
                    ->default('='),
                Forms\Components\TextInput::make('value'),
                Forms\Components\TextInput::make('color')
                    ->maxLength(255)
                    ->default('gray'),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->default('icon'),
                Forms\Components\TextInput::make('payload'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_action')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->searchable(),
                Tables\Columns\TextColumn::make('webhook')
                    ->searchable(),
                Tables\Columns\TextColumn::make('where')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->searchable(),
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
            'index' => Pages\ListTriggers::route('/'),
            'create' => Pages\CreateTrigger::route('/create'),
            'edit' => Pages\EditTrigger::route('/{record}/edit'),
        ];
    }
}
