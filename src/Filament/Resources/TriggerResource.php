<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages\CreateTrigger;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages\EditTrigger;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource\Pages\ListTriggers;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use UnitEnum;

class TriggerResource extends Resource
{
    protected static ?string $model = Trigger::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|UnitEnum|null $navigationGroup = 'Workflows';

    protected static ?int $navigationSort = 2;

    /**
     * The trigger fields, shared with the workflow builder page.
     *
     * @return array<int, Grid>
     */
    public static function getTriggerFormComponents(): array
    {
        $hasSource = fn (Get $get): bool => filled($get('model_type')) || filled($get('event')) || filled($get('webhook'));

        return [
            Grid::make([
                'md' => 6,
                'sm' => 1,
            ])
                ->columnSpanFull()
                ->schema([
                    Select::make('type')
                        ->columnSpanFull()
                        ->live()
                        ->required()
                        ->options([
                            'model' => 'Model',
                            'event' => 'Event',
                            'webhook' => 'Webhook',
                        ])
                        ->default('model'),
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255),
                    Select::make('model_type')
                        ->label('Model')
                        ->columnSpan(fn (Get $get) => filled($get('model_type')) ? 2 : 6)
                        ->required(fn (Get $get) => $get('type') === 'model')
                        ->live()
                        ->visible(fn (Get $get) => $get('type') === 'model')
                        ->searchable()
                        ->options(fn () => FilamentWorkflows::getModels()->pluck('label', 'model')->toArray()),
                    Select::make('model_id')
                        ->label('Only this record')
                        ->columnSpan(2)
                        ->searchable()
                        ->visible(fn (Get $get) => $get('type') === 'model' && filled($get('model_type')))
                        ->options(fn (Get $get) => static::getModelRecordOptions($get('model_type'))),
                    Select::make('model_action')
                        ->label('When the record is')
                        ->columnSpan(2)
                        ->required(fn (Get $get) => $get('type') === 'model')
                        ->visible(fn (Get $get) => $get('type') === 'model' && filled($get('model_type')))
                        ->options([
                            'created' => 'Created',
                            'updated' => 'Updated',
                            'deleted' => 'Deleted',
                        ])
                        ->default('created'),
                    Select::make('event')
                        ->required(fn (Get $get) => $get('type') === 'event')
                        ->live()
                        ->columnSpanFull()
                        ->visible(fn (Get $get) => $get('type') === 'event')
                        ->searchable()
                        ->options(fn () => FilamentWorkflows::getEvents()->pluck('label', 'event')->toArray()),
                    TextInput::make('webhook')
                        ->label('Webhook key')
                        ->helperText('The trigger runs when the webhook payload contains this key.')
                        ->required(fn (Get $get) => $get('type') === 'webhook')
                        ->columnSpanFull()
                        ->live(onBlur: true)
                        ->visible(fn (Get $get) => $get('type') === 'webhook')
                        ->maxLength(255),
                    TextInput::make('where')
                        ->label('Where field')
                        ->visible($hasSource)
                        ->columnSpan(2)
                        ->maxLength(255),
                    Select::make('is')
                        ->visible($hasSource)
                        ->columnSpan(2)
                        ->options([
                            '=' => '=',
                            '!=' => '!=',
                            '>' => '>',
                            '<' => '<',
                            '>=' => '>=',
                            '<=' => '<=',
                        ])
                        ->default('='),
                    TextInput::make('value')
                        ->visible($hasSource)
                        ->columnSpan(2),
                    ColorPicker::make('color')
                        ->columnSpan(3)
                        ->default('#3b82f6'),
                    IconPicker::make('icon')
                        ->columnSpan(3)
                        ->default('heroicon-s-code-bracket-square'),
                ]),
        ];
    }

    /**
     * @return array<int|string, string>
     */
    public static function getModelRecordOptions(?string $modelClass): array
    {
        if (blank($modelClass) || ! FilamentWorkflows::getModels()->pluck('model')->contains($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            return [];
        }

        return $modelClass::query()
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Model $record) => [$record->getKey() => (string) ($record->getAttribute('name') ?? $record->getKey())])
            ->toArray();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::getTriggerFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color'),
                TextColumn::make('name')
                    ->icon(fn (Trigger $record) => str_starts_with((string) $record->icon, 'heroicon') ? $record->icon : 'heroicon-s-bolt')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : null)
                    ->description(fn (Trigger $record) => $record->model_action)
                    ->searchable(),
                TextColumn::make('event')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : null)
                    ->toggleable(),
                TextColumn::make('webhook')
                    ->toggleable(),
                TextColumn::make('where')
                    ->formatStateUsing(fn (Trigger $record) => $record->where.' '.$record->is.' '.json_encode($record->value))
                    ->toggleable(),
                TextColumn::make('workflows_count')
                    ->label('Workflows')
                    ->counts('workflows')
                    ->badge(),
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
            'index' => ListTriggers::route('/'),
            'create' => CreateTrigger::route('/create'),
            'edit' => EditTrigger::route('/{record}/edit'),
        ];
    }
}
