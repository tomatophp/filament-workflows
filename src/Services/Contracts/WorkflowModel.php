<?php

namespace TomatoPHP\FilamentWorkflows\Services\Contracts;

use Illuminate\Database\Eloquent\Model;

class WorkflowModel
{
    public string $model;
    public string $label;

    public static function make(string $label): static
    {
        return (new WorkflowModel())->label($label);
    }

    public function model(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'label' => $this->label,
        ];
    }
}
