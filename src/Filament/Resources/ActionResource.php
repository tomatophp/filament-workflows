<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Models\Action;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;

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
                Forms\Components\TextInput::make('description'),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255)
                    ->default('action'),
                Forms\Components\TextInput::make('form')
                    ->maxLength(255),
                Forms\Components\TextInput::make('event')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payload'),
                Forms\Components\TextInput::make('color')
                    ->maxLength(255)
                    ->default('gray'),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->default('icon'),
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
                Tables\Columns\TextColumn::make('form')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
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
            'index' => Pages\ListActions::route('/'),
            'create' => Pages\CreateAction::route('/create'),
            'edit' => Pages\EditAction::route('/{record}/edit'),
        ];
    }
}
