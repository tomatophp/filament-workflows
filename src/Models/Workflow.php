<?php

namespace TomatoPHP\FilamentWorkflows\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use TomatoPHP\FilamentWorkflows\Database\Factories\WorkflowFactory;

/**
 * @property string $id
 * @property string $team_id
 * @property string $user_id
 * @property string $user_type
 * @property string $model_id
 * @property string $model_type
 * @property string $name
 * @property string $key
 * @property string $description
 * @property string $type
 * @property string $status
 * @property string $payload
 * @property string $is_active
 */
class Workflow extends Model
{
    use HasFactory;

    protected static function newFactory(): WorkflowFactory
    {
        return WorkflowFactory::new();
    }

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'team_id',
        'user_id',
        'user_type',
        'model_id',
        'model_type',
        'name',
        'key',
        'description',
        'type',
        'status',
        'payload',
        'is_active',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'payload' => 'json',
        'is_active' => 'boolean',
    ];

    public function triggers(): BelongsToMany
    {
        return $this->belongsToMany(Trigger::class, 'workflows_has_triggers')->withPivot('payload');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class);
    }
}
