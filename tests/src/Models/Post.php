<?php

namespace TomatoPHP\FilamentWorkflows\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentWorkflows\Traits\InteractsWithWorkflow;

class Post extends Model
{
    use InteractsWithWorkflow;

    protected $guarded = [];
}
