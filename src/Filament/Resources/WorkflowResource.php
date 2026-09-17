<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\BuildWorkflow;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\CreateWorkflow;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\EditWorkflow;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages\ListWorkflows;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\RelationManagers\WorkflowLogsRelationManager;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use UnitEnum;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|UnitEnum|null $navigationGroup = 'Workflows';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->columnSpanFull()
                    ->options([
                        'workflow' => 'Workflow',
                        'trigger' => 'Trigger',
                    ])
                    ->default('workflow'),
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, string $operation) {
                        if ($operation === 'create') {
                            $set('key', str($get('name'))->lower()->slug()->toString());
                        }
                    })
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->helperText('Only active workflows run their actions.')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->description(fn (Workflow $record) => is_string($record->description) ? $record->description : null)
                    ->searchable(),
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('triggers_count')
                    ->label('Triggers')
                    ->counts('triggers')
                    ->badge(),
                TextColumn::make('logs_count')
                    ->label('Runs')
                    ->counts('logs')
                    ->badge()
                    ->color('success'),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('type')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Action::make('build')
                    ->icon('heroicon-s-squares-plus')
                    ->url(fn (Workflow $record) => BuildWorkflow::getUrl(['record' => $record])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            WorkflowLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflows::route('/'),
            'create' => CreateWorkflow::route('/create'),
            'edit' => EditWorkflow::route('/{record}/edit'),
            'build' => BuildWorkflow::route('/{record}/build'),
        ];
    }
}
