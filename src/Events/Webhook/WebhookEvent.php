<?php

namespace TomatoPHP\FilamentWorkflows\Events\Webhook;

use Illuminate\Foundation\Events\Dispatchable;

class WebhookEvent
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $data  the request input, without the token
     */
    public function __construct(
        public array $data,
    ) {}
}
