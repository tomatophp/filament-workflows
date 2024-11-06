<?php

namespace TomatoPHP\FilamentWorkflows\Http\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;
use TomatoPHP\FilamentWorkflows\Models\Trigger;

class WebhookController
{

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        Event::dispatch(new WebhookEvent($request->toArray()));

        return 'ok';
    }
}
