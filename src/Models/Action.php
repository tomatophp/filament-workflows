<?php

namespace TomatoPHP\FilamentWorkflows\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
* @property string $team_id
* @property string $name
* @property string $description
* @property string $type
* @property string $action
* @property string $payload
* @property string $color
* @property string $icon
 */
class Action extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'team_id',
        'name',
        'description',
        'type',
        'action',
        'payload',
        'color',
        'icon'
    ];

    protected $casts = [
        'description' => 'json',
        'payload' => 'json'
    ];


    /**
     * @return BelongsToMany
     */
    public function triggers(): BelongsToMany
    {
        return $this->belongsToMany(Trigger::class, 'triggers_has_actions');
    }
}
