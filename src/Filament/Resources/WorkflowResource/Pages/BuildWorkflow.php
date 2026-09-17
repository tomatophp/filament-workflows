<?php

namespace TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Throwable;
use TomatoPHP\FilamentWorkflows\Filament\Resources\ActionResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\TriggerResource;
use TomatoPHP\FilamentWorkflows\Filament\Resources\WorkflowResource;
use TomatoPHP\FilamentWorkflows\Models\Action as ActionModel;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\TriggerHasAction;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;
use TomatoPHP\FilamentWorkflows\Services\Abstracts\Action as WorkflowActionHandler;

/**
 * @property Workflow $record
 */
class BuildWorkflow extends ViewRecord
{
    protected static string $resource = WorkflowResource::class;

    protected string $view = 'filament-workflows::builder';

    public function getTitle(): string
    {
        return 'Build: '.$this->getRecord()->name;
    }

    /**
     * Only the logs relation manager is shown under the builder.
     */
    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getRelationManagersContentComponent(),
        ]);
    }

    /**
     * @return Collection<int, array{trigger: Trigger, actions: Collection<int, TriggerHasAction>}>
     */
    public function getBuilderTriggers(): Collection
    {
        return $this->getRecord()->triggers()->get()->map(fn (Trigger $trigger) => [
            'trigger' => $trigger,
            'actions' => $this->getWorkflowTrigger($trigger->id)?->actions()->with('action')->get() ?? collect(),
        ]);
    }

    public function resolveIcon(?string $icon, string $fallback): string
    {
        if (blank($icon)) {
            return $fallback;
        }

        try {
            svg($icon);

            return $icon;
        } catch (Throwable) {
            return $fallback;
        }
    }

    protected function getWorkflowTrigger(int|string $triggerId): ?WorkflowHasTrigger
    {
        return WorkflowHasTrigger::query()
            ->where('workflow_id', $this->getRecord()->getKey())
            ->where('trigger_id', $triggerId)
            ->first();
    }

    protected function findTriggerAction(int|string|null $id): ?TriggerHasAction
    {
        return TriggerHasAction::query()
            ->whereKey($id)
            ->whereHas('trigger', fn ($query) => $query->where('workflow_id', $this->getRecord()->getKey()))
            ->first();
    }

    /**
     * @return class-string<WorkflowActionHandler>|null
     */
    protected function getHandler(?TriggerHasAction $triggerAction): ?string
    {
        $handler = $triggerAction?->action?->action;

        return is_string($handler) && is_subclass_of($handler, WorkflowActionHandler::class) ? $handler : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('trigger')
                ->label('Create Trigger')
                ->icon('heroicon-s-plus')
                ->schema(TriggerResource::getTriggerFormComponents())
                ->action(function (array $data): void {
                    $trigger = Trigger::query()->create($data);

                    $this->getRecord()->triggers()->attach([$trigger->id]);
                }),
            Action::make('attachTrigger')
                ->label('Attach Trigger')
                ->color('gray')
                ->icon('heroicon-s-link')
                ->schema([
                    Select::make('triggers')
                        ->multiple()
                        ->required()
                        ->options(fn () => Trigger::query()
                            ->whereNotIn('id', $this->getRecord()->triggers()->pluck('triggers.id'))
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable(),
                ])
                ->action(function (array $data): void {
                    $this->getRecord()->triggers()->syncWithoutDetaching($data['triggers']);
                }),
        ];
    }

    public function deleteTriggerAction(): Action
    {
        return Action::make('deleteTrigger')
            ->iconButton()
            ->requiresConfirmation()
            ->modalDescription('The trigger is detached from this workflow; it is not deleted.')
            ->icon('heroicon-s-trash')
            ->tooltip('Detach Trigger')
            ->label('Detach Trigger')
            ->color('danger')
            ->action(function (array $arguments): void {
                $this->getRecord()->triggers()->detach($arguments['trigger']);
            });
    }

    public function editTriggerAction(): Action
    {
        return Action::make('editTrigger')
            ->iconButton()
            ->icon('heroicon-s-pencil')
            ->tooltip('Edit Trigger')
            ->label('Edit Trigger')
            ->color('warning')
            ->fillForm(fn (array $arguments) => Trigger::query()->find($arguments['trigger'])?->attributesToArray() ?? [])
            ->schema(TriggerResource::getTriggerFormComponents())
            ->action(function (array $data, array $arguments): void {
                Trigger::query()->findOrFail($arguments['trigger'])->update($data);
            });
    }

    public function createActionForTriggerAction(): Action
    {
        return Action::make('createActionForTrigger')
            ->iconButton()
            ->color('success')
            ->icon('heroicon-s-plus')
            ->label('Create Action')
            ->tooltip('Create Action')
            ->schema(ActionResource::getActionFormComponents())
            ->action(function (array $data, array $arguments): void {
                $workflowTrigger = $this->getWorkflowTrigger($arguments['trigger']);

                if (! $workflowTrigger) {
                    return;
                }

                $action = ActionModel::query()->create($data);

                $workflowTrigger->actions()->firstOrCreate([
                    'action_id' => $action->getKey(),
                ]);
            });
    }

    public function attachActionsToTriggerAction(): Action
    {
        return Action::make('attachActionsToTrigger')
            ->iconButton()
            ->icon('heroicon-s-link')
            ->color('info')
            ->tooltip('Attach Action')
            ->label('Attach Action')
            ->schema([
                Select::make('actions')
                    ->multiple()
                    ->required()
                    ->options(fn () => ActionModel::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
            ])
            ->action(function (array $data, array $arguments): void {
                $workflowTrigger = $this->getWorkflowTrigger($arguments['trigger']);

                if (! $workflowTrigger) {
                    return;
                }

                foreach ($data['actions'] as $actionId) {
                    $workflowTrigger->actions()->firstOrCreate([
                        'action_id' => $actionId,
                    ]);
                }
            });
    }

    public function removeActionFromTriggerAction(): Action
    {
        return Action::make('removeActionFromTrigger')
            ->requiresConfirmation()
            ->modalDescription('The action is removed from this trigger; it is not deleted.')
            ->iconButton()
            ->icon('heroicon-s-trash')
            ->color('danger')
            ->tooltip('Remove Action')
            ->label('Remove Action')
            ->action(function (array $arguments): void {
                $this->findTriggerAction($arguments['action'] ?? null)?->delete();
            });
    }

    /**
     * Configure the action settings (the payload passed to the action class).
     * Only visible when the action class defines a form.
     */
    public function editActionSettingsAction(): Action
    {
        return Action::make('editActionSettings')
            ->visible(function (array $arguments): bool {
                $handler = $this->getHandler($this->findTriggerAction($arguments['action'] ?? null));

                return $handler !== null && count($handler::form()) > 0;
            })
            ->iconButton()
            ->icon('heroicon-s-cog-6-tooth')
            ->color('warning')
            ->tooltip('Action Settings')
            ->label('Action Settings')
            ->fillForm(fn (array $arguments) => $this->findTriggerAction($arguments['action'] ?? null)?->payload ?? [])
            ->schema(function (array $arguments): array {
                $handler = $this->getHandler($this->findTriggerAction($arguments['action'] ?? null));

                return $handler ? $handler::form() : [];
            })
            ->action(function (array $data, array $arguments): void {
                $triggerAction = $this->findTriggerAction($arguments['action'] ?? null);

                if (! $triggerAction) {
                    return;
                }

                $triggerAction->payload = $data;
                $triggerAction->save();
            });
    }
}
