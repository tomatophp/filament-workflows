<?php

namespace TomatoPHP\FilamentWorkflows\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriggerHasAction extends Model
{
    protected $table = "triggers_has_actions";

    protected $fillable = [
        'workflows_has_triggers_id',
        'action_id',
        'payload',
    ];

    /**
     * @return BelongsTo
     */
    public function trigger(): BelongsTo
    {
        return $this->belongsTo(WorkflowHasTrigger::class, 'workflows_has_triggers_id');
    }

    /**
     * @return BelongsTo
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }
}
