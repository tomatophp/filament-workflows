<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        @forelse ($this->getBuilderTriggers() as $item)
            @php
                $trigger = $item['trigger'];
                $triggerActions = $item['actions'];
            @endphp

            <x-filament::section
                :heading="$trigger->name"
                :description="match ($trigger->type) {
                    'model' => 'When ' . ($trigger->model_type ? class_basename($trigger->model_type) : 'a record') . ' is ' . $trigger->model_action,
                    'event' => 'When ' . ($trigger->event ? class_basename($trigger->event) : 'an event') . ' is dispatched',
                    'webhook' => 'When the webhook payload has [' . $trigger->webhook . ']',
                    default => $trigger->type,
                } . (filled($trigger->where) ? ' and ' . $trigger->where . ' ' . $trigger->is . ' ' . json_encode($trigger->value) : '')"
                :icon="$this->resolveIcon($trigger->icon, 'heroicon-s-bolt')"
                icon-color="primary"
            >
                <x-slot name="afterHeader">
                    {{ ($this->createActionForTriggerAction)(['trigger' => $trigger->getKey()]) }}
                    {{ ($this->attachActionsToTriggerAction)(['trigger' => $trigger->getKey()]) }}
                    {{ ($this->editTriggerAction)(['trigger' => $trigger->getKey()]) }}
                    {{ ($this->deleteTriggerAction)(['trigger' => $trigger->getKey()]) }}
                </x-slot>

                @if ($triggerActions->isNotEmpty())
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr)); gap: 1rem;">
                        @foreach ($triggerActions as $triggerAction)
                            <x-filament::section
                                :heading="$triggerAction->action?->name ?? 'Missing action'"
                                :description="is_string($triggerAction->action?->description) ? $triggerAction->action?->description : null"
                                icon="heroicon-s-arrow-small-right"
                                icon-color="warning"
                                compact
                            >
                                <x-slot name="afterHeader">
                                    {{ ($this->editActionSettingsAction)(['action' => $triggerAction->getKey()]) }}
                                    {{ ($this->removeActionFromTriggerAction)(['action' => $triggerAction->getKey()]) }}
                                </x-slot>

                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="display: flex; padding: 0.75rem; border-radius: 0.5rem; color: #fff; background-color: {{ e($triggerAction->action?->color ?: '#6b7280') }};">
                                        <x-filament::icon
                                            :icon="$this->resolveIcon($triggerAction->action?->icon, 'heroicon-s-play')"
                                            style="width: 1.5rem; height: 1.5rem;"
                                        />
                                    </div>
                                    <code style="font-size: 0.75rem; opacity: 0.7; word-break: break-all;">{{ $triggerAction->action?->action ? class_basename($triggerAction->action->action) : '-' }}</code>
                                </div>
                            </x-filament::section>
                        @endforeach
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; text-align: center; opacity: 0.7;">
                        <x-filament::icon icon="heroicon-s-bolt" style="width: 2rem; height: 2rem;" />
                        <p>There are no actions on this trigger yet.</p>
                    </div>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; text-align: center; opacity: 0.7;">
                    <x-filament::icon icon="heroicon-s-bolt" style="width: 2rem; height: 2rem;" />
                    <p>This workflow has no triggers. Use "Create Trigger" or "Attach Trigger" to start.</p>
                </div>
            </x-filament::section>
        @endforelse
    </div>

    {{ $this->content }}
</x-filament-panels::page>
