<?php

namespace TomatoPHP\FilamentWorkflows\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
* @property string $team_id
* @property string $user_type
* @property string $user_id
* @property Workflow $workflow_id
* @property Trigger $trigger_id
* @property Action $action_id
* @property string $log
* @property string $payload
 */
class WorkflowLog extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'team_id',
        'user_type',
        'user_id',
        'workflow_id',
        'trigger_id',
        'action_id',
        'log',
        'payload'
    ];

    protected $casts = [
        'log' => 'json',
        'payload' => 'json'
    ];

    /**
     * @return MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * @return BelongsTo
     */
    public function trigger(): BelongsTo
    {
        return $this->belongsTo(Trigger::class);
    }

    /**
     * @return BelongsTo
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }
}
