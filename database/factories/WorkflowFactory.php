<?php

namespace TomatoPHP\FilamentWorkflows\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use TomatoPHP\FilamentWorkflows\Models\Workflow;

/**
 * @extends Factory<Workflow>
 */
class WorkflowFactory extends Factory
{
    protected $model = Workflow::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));

        return [
            'name' => $name,
            'key' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'description' => fake()->sentence(),
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['is_active' => true]);
    }
}
