<?php

namespace TomatoPHP\FilamentWorkflows\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentWorkflows\Models\Trigger;

/**
 * @extends Factory<Trigger>
 */
class TriggerFactory extends Factory
{
    protected $model = Trigger::class;

    /**
     * A webhook trigger by default, because it needs no host model or event class.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'type' => 'webhook',
            'webhook' => fake()->unique()->slug(2),
            'is' => '=',
            'color' => '#D64524',
            'icon' => 'heroicon-s-bolt',
        ];
    }

    public function forModel(string $modelClass, string $modelAction = 'created'): static
    {
        return $this->state(fn (): array => [
            'type' => 'model',
            'webhook' => null,
            'model_type' => $modelClass,
            'model_action' => $modelAction,
        ]);
    }

    public function forEvent(string $eventClass): static
    {
        return $this->state(fn (): array => [
            'type' => 'event',
            'webhook' => null,
            'event' => $eventClass,
        ]);
    }
}
