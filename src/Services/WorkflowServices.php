<?php

namespace TomatoPHP\FilamentWorkflows\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Listeners\EventTriggerListener;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowAction;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowEvent;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowModel;

class WorkflowServices
{
    /**
     * @var array<string, WorkflowModel>
     */
    protected array $models = [];

    /**
     * @var array<string, WorkflowEvent>
     */
    protected array $events = [];

    /**
     * @var array<string, WorkflowAction>
     */
    protected array $actions = [];

    /**
     * @param  WorkflowAction|array<int, WorkflowAction>  $action
     */
    public function action(WorkflowAction|array $action): void
    {
        if (is_array($action)) {
            foreach ($action as $item) {
                if ($item instanceof WorkflowAction) {
                    $this->action($item);
                }
            }

            return;
        }

        $this->actions[$action->action] = $action;
    }

    /**
     * @param  WorkflowModel|array<int, WorkflowModel>  $model
     */
    public function model(WorkflowModel|array $model): void
    {
        if (is_array($model)) {
            foreach ($model as $item) {
                if ($item instanceof WorkflowModel) {
                    $this->model($item);
                }
            }

            return;
        }

        $this->models[$model->model] = $model;
    }

    /**
     * Register an application event as a workflow trigger source. Every time the
     * event is dispatched, triggers of type "event" pointing to it are run.
     *
     * @param  WorkflowEvent|array<int, WorkflowEvent>  $event
     */
    public function event(WorkflowEvent|array $event): void
    {
        if (is_array($event)) {
            foreach ($event as $item) {
                if ($item instanceof WorkflowEvent) {
                    $this->event($item);
                }
            }

            return;
        }

        if (! isset($this->events[$event->event])) {
            Event::listen($event->event, EventTriggerListener::class);
        }

        $this->events[$event->event] = $event;
    }

    public function getModels(): Collection
    {
        return collect(array_values($this->models));
    }

    public function getEvents(): Collection
    {
        return collect(array_values($this->events));
    }

    public function getActions(): Collection
    {
        return collect(array_values($this->actions));
    }
}
