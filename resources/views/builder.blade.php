<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    <div class="flex flex-col justify-center items-center gap-4">
        @foreach($this->getRecord()->triggers as $trigger)
            @php $getTrigger = !empty($trigger->pivot->payload)? (array)json_decode($trigger->pivot->payload): $trigger->toArray()  @endphp
            <x-filament::section
                class="w-full"
                :heading="$getTrigger['name']"
                :description="$getTrigger['description']??null"
                :icon="$getTrigger['icon']"
                :headerActions="[
                    ($this->createAction())(['trigger' => $getTrigger]),
                    ($this->attachAction())(['trigger' => $getTrigger]),
                    ($this->editTrigger())(['trigger' => $getTrigger]),
                    ($this->deleteTrigger())(['trigger' => $getTrigger]),
                ]"
            >

                    @php $actions = \TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger::query()
                        ->where('trigger_id', $trigger->id)
                        ->where('workflow_id', $this->getRecord()->id)
                        ->first();
                    @endphp
                    @if($actions && count($actions->actions) > 0)
                        <div class="flex flex-wrap gap-4">
                            @foreach($actions->actions as $action)
                                <x-filament::section
                                    :heading="$action->action?->name"
                                    icon="heroicon-s-bolt"
                                    icon-color="warning"
                                    :headerActions="[
                                    ($this->editAction())(['action' => $action]),
                                    ($this->deleteAction())(['action' => $action]),
                                ]"
                                >
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="p-3 rounded-lg" style="background-color: {{ $action->action?->color }}">
                                            <x-icon name="{{ $action->action?->icon }}" class="w-8 h-8" />
                                        </div>

                                        <div class="mt-3 text-center">
                                            <p class="text-sm text-gray-400">{{ $action->action?->description }}</p>
                                        </div>
                                    </div>

                                </x-filament::section>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col justify-center items-center">
                            <div class="text-center flex flex-col justify-center items-center gap-2">
                                <x-icon name="heroicon-s-bolt" class="w-8 h-8 text-custom-400" style="--c-400:var(--warning-400);--c-500:var(--warning-500);--c-600:var(--warning-600);" />
                                <h1>There is no actions here</h1>
                            </div>
                        </div>
                    @endif
            </x-filament::section>
        @endforeach
    </div>

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
            :content-tab-label="$this->getContentTabLabel()"
            :content-tab-icon="$this->getContentTabIcon()"
            :content-tab-position="$this->getContentTabPosition()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($hasCombinedRelationManagerTabsWithContent)
                <x-slot name="content">
                    @if ($this->hasInfolist())
                        {{ $this->infolist }}
                    @else
                        {{ $this->form }}
                    @endif
                </x-slot>
            @endif
        </x-filament-panels::resources.relation-managers>
    @endif
</x-filament-panels::page>
