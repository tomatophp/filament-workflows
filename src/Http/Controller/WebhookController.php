<?php

namespace TomatoPHP\FilamentWorkflows\Http\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;

class WebhookController
{
    public function index(Request $request): string
    {
        Event::dispatch(new WebhookEvent($request->toArray()));

        return 'ok';
    }
}
