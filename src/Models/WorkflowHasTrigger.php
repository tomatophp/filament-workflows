<?php

namespace TomatoPHP\FilamentWorkflows\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowHasTrigger extends Model
{
    protected $table = "workflows_has_triggers";

    protected $fillable = [
        'workflow_id',
        'trigger_id',
        'payload',
    ];

    /**
     * @return BelongsTo
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * @return BelongsTo
     */
    public function trigger(): BelongsTo
    {
        return $this->belongsTo(Trigger::class, 'trigger_id');
    }

    /**
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(TriggerHasAction::class,  'workflows_has_triggers_id', 'id');
    }
}
