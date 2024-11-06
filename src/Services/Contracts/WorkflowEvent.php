<?php

namespace TomatoPHP\FilamentWorkflows\Services\Contracts;

use Illuminate\Database\Eloquent\Model;

class WorkflowEvent
{
    public string $event;
    public string $label;

    public static function make(string $label): static
    {
        return (new WorkflowEvent())->label($label);
    }

    public function event(string $event): static
    {
        $this->event = $event;
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
            'event' => $this->event,
            'label' => $this->label,
        ];
    }
}
