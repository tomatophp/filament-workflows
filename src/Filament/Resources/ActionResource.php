<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages\CreateAction;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages\EditAction as EditActionPage;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource\Pages\ListActions;
use TomatoPHP\FilamentWorkflows\Models\Action;
use UnitEnum;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';

    protected static string|UnitEnum|null $navigationGroup = 'Workflows';

    protected static ?int $navigationSort = 3;

    /**
     * The action fields, shared with the workflow builder page.
     *
     * @return array<int, Grid>
     */
    public static function getActionFormComponents(): array
    {
        return [
            Grid::make([
                'md' => 6,
                'sm' => 1,
            ])
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    Select::make('action')
                        ->label('Runs')
                        ->helperText('Actions are registered with FilamentWorkflows::action().')
                        ->columnSpanFull()
                        ->required()
                        ->searchable()
                        ->options(fn () => FilamentWorkflows::getActions()->pluck('label', 'action')->toArray()),
                    ColorPicker::make('color')
                        ->columnSpan(3)
                        ->default('#f59e0b'),
                    IconPicker::make('icon')
                        ->columnSpan(3)
                        ->default('heroicon-s-arrows-up-down'),
                ]),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::getActionFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color'),
                TextColumn::make('name')
                    ->icon(fn (Action $record) => str_starts_with((string) $record->icon, 'heroicon') ? $record->icon : 'heroicon-s-play')
                    ->description(fn (Action $record) => is_string($record->description) ? $record->description : null)
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Runs')
                    ->formatStateUsing(fn (?string $state) => FilamentWorkflows::getActions()->firstWhere('action', $state)?->label ?? ($state ? class_basename($state) : null))
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => ListActions::route('/'),
            'create' => CreateAction::route('/create'),
            'edit' => EditActionPage::route('/{record}/edit'),
        ];
    }
}
