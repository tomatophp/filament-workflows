<?php

namespace TomatoPHP\FilamentWorkflows\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;
use TomatoPHP\FilamentWorkflows\Models\Trigger;
use TomatoPHP\FilamentWorkflows\Models\Workflow;
use TomatoPHP\FilamentWorkflows\Models\WorkflowHasTrigger;
use TomatoPHP\FilamentWorkflows\Services\Abstracts\Action as WorkflowActionHandler;

class WorkflowRunner
{
    protected static ?bool $tablesExist = null;

    /**
     * The workflow tables may not exist yet (fresh install, running migrations,
     * package discovery in the console). Every lookup is skipped until they do.
     */
    public static function tablesExist(): bool
    {
        if (static::$tablesExist) {
            return true;
        }

        try {
            return static::$tablesExist = Schema::hasTable('triggers') && Schema::hasTable('workflows_has_triggers');
        } catch (Throwable) {
            return false;
        }
    }

    public static function flushTableCache(): void
    {
        static::$tablesExist = null;
    }

    /**
     * @return Collection<int, Trigger>
     */
    public static function modelTriggers(Model $model, string $modelAction): Collection
    {
        if (! static::tablesExist()) {
            return collect();
        }

        return Trigger::query()
            ->where('type', 'model')
            ->where('model_type', $model::class)
            ->where('model_action', $modelAction)
            ->get();
    }

    /**
     * Run every action attached to the trigger on every active workflow using it,
     * and write a WorkflowLog row for each run.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function run(Trigger $trigger, mixed $event, array $payload, ?Model $model = null): int
    {
        if (! static::matches($trigger, $payload, $model)) {
            return 0;
        }

        $executed = 0;

        $workflows = Workflow::query()
            ->where('is_active', true)
            ->whereHas('triggers', fn ($query) => $query->where('triggers.id', $trigger->id))
            ->get();

        foreach ($workflows as $workflow) {
            $workflowTrigger = WorkflowHasTrigger::query()
                ->where('workflow_id', $workflow->id)
                ->where('trigger_id', $trigger->id)
                ->first();

            if (! $workflowTrigger) {
                continue;
            }

            foreach ($workflowTrigger->actions()->with('action')->get() as $triggerAction) {
                $handler = $triggerAction->action?->action;

                if (! is_string($handler) || ! is_subclass_of($handler, WorkflowActionHandler::class)) {
                    $workflow->logs()->create([
                        'action_id' => $triggerAction->action_id,
                        'trigger_id' => $trigger->id,
                        'log' => 'Action skipped: ['.($handler ?? 'none').'] is not a workflow action class',
                        'payload' => $payload,
                    ]);

                    continue;
                }

                try {
                    $handler::run($event, $triggerAction);

                    $log = 'Action: '.$handler.' executed';
                    $executed++;
                } catch (Throwable $exception) {
                    report($exception);

                    $log = 'Action: '.$handler.' failed: '.$exception->getMessage();
                }

                $workflow->logs()->create([
                    'action_id' => $triggerAction->action_id,
                    'trigger_id' => $trigger->id,
                    'log' => $log,
                    'payload' => $payload,
                ]);
            }
        }

        return $executed;
    }

    /**
     * Check the trigger filters: a specific record (model_id) and the
     * optional "where <field> <is> <value>" condition on the payload.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function matches(Trigger $trigger, array $payload, ?Model $model = null): bool
    {
        if ($model && filled($trigger->model_id) && (string) $model->getKey() !== (string) $trigger->model_id) {
            return false;
        }

        if (blank($trigger->where)) {
            return true;
        }

        $actual = data_get($payload, $trigger->where);
        $expected = $trigger->value;

        return match ($trigger->is ?: '=') {
            '!=' => $actual != $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            default => $actual == $expected,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPayload(mixed $data): array
    {
        if ($data instanceof Model) {
            return $data->toArray();
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        try {
            $decoded = json_decode(json_encode($data, JSON_THROW_ON_ERROR | JSON_PARTIAL_OUTPUT_ON_ERROR), true);
        } catch (Throwable) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }
}
