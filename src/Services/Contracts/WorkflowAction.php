<?php

namespace TomatoPHP\FilamentWorkflows\Services\Contracts;

class WorkflowAction
{
    public string $action;
    public string $label;

    public static function make(string $label): static
    {
        return (new WorkflowAction())->label($label);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function action(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'label' => $this->label,
        ];
    }
}
