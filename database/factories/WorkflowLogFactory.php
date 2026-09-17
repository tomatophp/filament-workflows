<?php

namespace TomatoPHP\FilamentWorkflows\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomatoPHP\FilamentWorkflows\Models\Action;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;

/**
 * @extends Factory<WorkflowLog>
 */
class WorkflowLogFactory extends Factory
{
    protected $model = WorkflowLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workflow_id' => Workflow::factory(),
            'trigger_id' => Trigger::factory(),
            'action_id' => Action::factory(),
            'log' => 'Action executed',
            'payload' => ['id' => fake()->numberBetween(1, 500)],
        ];
    }
}
