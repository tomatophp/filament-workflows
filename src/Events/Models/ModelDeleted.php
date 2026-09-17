<?php

namespace TomatoPHP\FilamentWorkflows\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use TomatoPHP\FilamentWorkflows\Models\Trigger;

class ModelDeleted
{
    use Dispatchable;

    public function __construct(
        public Model $data,
        public Trigger $trigger
    ) {}
}
