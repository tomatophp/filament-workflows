<?php

namespace TomatoPHP\FilamentWorkflows\Services;

use Illuminate\Support\Collection;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowAction;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowEvent;
use TomatoPHP\FilamentWorkflows\Services\Contracts\WorkflowModel;

class WorkflowServices
{
    protected array $models = [];

    protected array $events = [];

    protected array $actions = [];

    public function action(WorkflowAction|array $action): void
    {
        if (is_array($action)) {
            foreach ($action as $item) {
                if ($item instanceof WorkflowAction) {
                    $this->form($item);
                }
            }
        } else {
            $this->actions[] = $action;
        }
    }

    public function model(WorkflowModel|array $model): void
    {
        if (is_array($model)) {
            foreach ($model as $item) {
                if ($item instanceof WorkflowModel) {
                    $this->model($item);
                }
            }
        } else {
            $this->models[] = $model;
        }
    }

    public function event(WorkflowEvent|array $event): void
    {
        if (is_array($event)) {
            foreach ($event as $item) {
                if ($item instanceof WorkflowEvent) {
                    $this->event($item);
                }
            }
        } else {
            $this->events[] = $event;
        }
    }

    public function getModels(): Collection
    {
        return collect($this->models);
    }

    public function getEvents(): Collection
    {
        return collect($this->events);
    }

    public function getActions(): Collection
    {
        return collect($this->actions);
    }
}
