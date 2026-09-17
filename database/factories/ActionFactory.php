<?php

namespace TomatoPHP\FilamentWorkflows\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentWorkflows\Models\Action;

/**
 * @extends Factory<Action>
 */
class ActionFactory extends Factory
{
    protected $model = Action::class;

    /**
     * No action class by default: a record without one never runs anything.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(),
            'color' => '#4E9A3E',
            'icon' => 'heroicon-s-play',
        ];
    }

    /**
     * @param  class-string  $actionClass
     */
    public function runs(string $actionClass): static
    {
        return $this->state(fn (): array => ['action' => $actionClass]);
    }
}
