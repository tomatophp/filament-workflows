<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentWorkflows\Facades\FilamentWorkflows;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;

class BuildWorkflow extends ViewRecord
{
    protected static string $resource = WorkflowResource::class;

    protected static string $view = 'filament-workflows::builder';

    protected function getTriggerForm(): array
    {
        return [
            Forms\Components\Grid::make([
                "md" => 6,
                "sm" => 1
            ])
                ->schema([
                Forms\Components\Select::make('type')
                    ->columnSpanFull()
                    ->searchable()
                    ->live()
                    ->options([
                        "model" => "Model",
                        "event" => "Event",
                        "webhook" => "Webhook",
                    ])
                    ->default('model'),
                Forms\Components\TextInput::make('name')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('model_type')
                    ->columnSpan(fn(Forms\Get $get) => !empty($get('model_type')) ? 2 : 6)
                    ->required()
                    ->live()
                    ->visible(fn(Forms\Get $get) => $get('type') === 'model')
                    ->searchable()
                    ->options(FilamentWorkflows::getModels()->pluck('label', 'model')->toArray()),
                Forms\Components\Select::make('model_id')
                    ->columnSpan(2)
                    ->searchable()
                    ->visible(fn(Forms\Get $get) => $get('type') === 'model' && !empty($get('model_type')))
                    ->options(fn(Forms\Get $get) => $get('model_type') ? $get('model_type')::all()->pluck('name', 'id') : []),
                Forms\Components\Select::make('model_action')
                    ->columnSpan(2)
                    ->required()
                    ->visible(fn(Forms\Get $get) => $get('type') === 'model' && !empty($get('model_type')))
                    ->searchable()
                    ->options([
                        "created" => "Created",
                        "updated" => "Updated",
                        "deleted" => "Deleted",
                    ])
                    ->default('created'),
                Forms\Components\Select::make('event')
                    ->required()
                    ->lazy()
                    ->columnSpanFull()
                    ->visible(fn(Forms\Get $get) => $get('type') === 'event')
                    ->searchable()
                    ->options(FilamentWorkflows::getEvents()->pluck('label', 'event')->toArray()),
                Forms\Components\TextInput::make('webhook')
                    ->required()
                    ->columnSpanFull()
                    ->lazy()
                    ->visible(fn(Forms\Get $get) => $get('type') === 'webhook')
                    ->maxLength(255),
                Forms\Components\TextInput::make('where')
                    ->visible(fn(Forms\Get $get) => !empty($get('model_type')) || !empty($get('event')) || !empty($get('webhook')))
                    ->columnSpan(2)
                    ->maxLength(255),
                Forms\Components\Select::make('is')
                    ->visible(fn(Forms\Get $get) => !empty($get('model_type')) || !empty($get('event')) || !empty($get('webhook')))
                    ->columnSpan(2)
                    ->searchable()
                    ->options([
                        "=" => "=",
                        ">" => ">",
                        "<" => "<",
                        ">=" => ">=",
                        "<=" => "<=",
                        "!=" => "!=",
                    ])
                    ->default('='),
                Forms\Components\TextInput::make('value')
                    ->visible(fn(Forms\Get $get) => !empty($get('model_type')) || !empty($get('event')) || !empty($get('webhook')))
                    ->columnSpan(2),
                Forms\Components\ColorPicker::make('color')
                    ->columnSpan(3)
                    ->default('#fefefe'),
                IconPicker::make('icon')
                    ->columnSpan(3)
                    ->default('heroicon-s-code-bracket-square')
        ])
        ];
    }

    protected function getActionForm():array
    {
        return [
            Forms\Components\Grid::make([
                "md" => 6,
                "sm" => 1
            ])->schema([
                Forms\Components\TextInput::make('name')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\Select::make('action')
                    ->columnSpanFull()
                    ->searchable()
                    ->options(FilamentWorkflows::getActions()->pluck('label', 'action')->toArray()),

                Forms\Components\ColorPicker::make('color')
                    ->columnSpan(3)
                    ->default('#fefefe'),
                IconPicker::make('icon')
                    ->columnSpan(3)
                    ->default('heroiocn-s-arrows-up-down'),
            ])
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('trigger')
                ->label('Create Trigger')
                ->form($this->getTriggerForm())
                ->action(function(array $data){
                    $trigger = Trigger::query()->create($data);

                    $this->getRecord()->triggers()->attach([$trigger->id]);

                    $this->js('window.location.reload()');
                }),
            Action::make('attachTrigger')
                ->label('Attach Trigger')
                ->form([
                    Forms\Components\Select::make('triggers')
                        ->multiple()
                        ->options(Trigger::all()->pluck('name', 'id')->toArray())
                        ->searchable()
                ])
                ->action(function(array $data){
                    $this->getRecord()->triggers()->attach($data['triggers']);

                    $this->js('window.location.reload()');
                }),
        ];
    }

    public function deleteTrigger(): Action
    {
        return Action::make('deleteTrigger')
            ->iconButton()
            ->requiresConfirmation()
            ->hiddenLabel()
            ->icon('heroicon-s-trash')
            ->tooltip('Delete Trigger')
            ->label('Delete Trigger')
            ->color('danger')
            ->action(function(array $arguments){
                $this->getRecord()->triggers()->detach($arguments['trigger']['id']);

                $this->js('window.location.reload()');
            });
    }

    public function editTrigger(): Action
    {
        return Action::make('editTrigger')
            ->iconButton()
            ->icon('heroicon-s-pencil')
            ->tooltip('Edit Trigger')
            ->label('Edit Trigger')
            ->color('warning')
            ->fillForm(fn(array $arguments) => $arguments['trigger'])
            ->form($this->getTriggerForm())
            ->action(function(array $data, array $arguments){
                unset($arguments['trigger']['pivot']);
                $data['id'] = $arguments['trigger']['id'];
                $this->getRecord()->triggers()
                    ->updateExistingPivot($arguments['trigger']['id'], [
                        "payload" => json_encode($data)
                    ]);

                $this->js('window.location.reload()');
            });
    }


    public function createAction(): Action
    {
        return Action::make('createAction')
            ->iconButton()
            ->color('success')
            ->icon('heroicon-s-plus')
            ->label('Create Action')
            ->tooltip('Create Action')
            ->form($this->getActionForm())
            ->action(function(array $data, array $arguments){
                $action = \TomatoPHP\FilamentWorkflows\Models\Action::query()->create($data);

                $trigger = WorkflowHasTrigger::query()->where('workflow_id', $this->getRecord()->id)
                    ->where('trigger_id', $arguments['trigger']['id'])
                    ->first();

                $checkExists = $trigger->actions()->where('action_id', $action['id'])->first();
                if(!$checkExists){
                    $trigger->actions()->create([
                        "action_id" => $action['id']
                    ]);
                }

                $this->js('window.location.reload()');
            });
    }

    public function attachAction(): Action
    {
        return Action::make('attachAction')
            ->iconButton()
            ->icon('heroicon-s-adjustments-horizontal')
            ->color('info')
            ->tooltip('Attach Action')
            ->label('Attach Action')
            ->form([
                Forms\Components\Select::make('actions')
                    ->multiple()
                    ->options(\TomatoPHP\FilamentWorkflows\Models\Action::all()->pluck('name', 'id')->toArray())
                    ->searchable()
            ])
            ->action(function(array $data, array $arguments){
                $trigger = WorkflowHasTrigger::query()->where('workflow_id', $this->getRecord()->id)
                    ->where('trigger_id', $arguments['trigger']['id'])
                    ->first();

                foreach ($data['actions'] as $action){
                    $exists = $trigger->actions()->where('action_id', $action)->first();
                    if(!$exists){
                        $trigger->actions()->create([
                            "action_id" => $action
                        ]);
                    }
                }

                $this->js('window.location.reload()');
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('deleteAction')
            ->requiresConfirmation()
            ->iconButton()
            ->icon('heroicon-s-trash')
            ->color('danger')
            ->tooltip('Delete Action')
            ->label('Delete Action')
            ->action(function(array $arguments){
                TriggerHasAction::query()->find($arguments['action']['id'])->delete();

                $this->js('window.location.reload()');
            });
    }

    public function editAction(): Action
    {
        return Action::make('editAction')
            ->visible(fn(array $arguments) =>  count(app($arguments['action']['action']['action'])::form()))
            ->iconButton()
            ->icon('heroicon-s-pencil')
            ->color('warning')
            ->tooltip('Edit Action')
            ->fillForm(fn(array $arguments) => !empty($arguments['action']['payload']) ? (array)json_decode($arguments['action']['payload']): [])
            ->form(function(array $arguments){
                $action = TriggerHasAction::query()->find($arguments['action']['id']);
                return app($action->action->action)::form();
            })
            ->label('Edit Action')
            ->action(function(array $data, array $arguments){
                $action = TriggerHasAction::query()->find($arguments['action']['id']);
                $action->payload = json_encode($data);
                $action->save();

                $this->js('window.location.reload()');
            });
    }
}
