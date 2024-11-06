<?php

namespace TomatoPHP\FilamentWorkflows\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
* @property string $team_id
* @property string $name
* @property string $type
* @property string $model_action
* @property string $model_type
* @property string $model_id
* @property string $event
* @property string $webhook
* @property string $where
* @property string $is
* @property string $value
* @property string $color
* @property string $icon
* @property string $payload
 */
class Trigger extends Model
{
    use HasFactory;
    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'team_id',
        'name',
        'type',
        'model_action',
        'model_type',
        'model_id',
        'event',
        'webhook',
        'where',
        'is',
        'value',
        'color',
        'icon',
        'payload'
    ];

    protected $casts = [
        'value' => 'json',
        'payload' => 'json'
    ];

    /**
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsToMany
     */
    public function workflows(): BelongsToMany
    {
        return $this->belongsToMany(Workflow::class, 'workflows_has_triggers');
    }

    /**
     * @return BelongsToMany
     */
    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'triggers_has_actions');
    }
}
