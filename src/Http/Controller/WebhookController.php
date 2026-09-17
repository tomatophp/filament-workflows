<?php

namespace TomatoPHP\FilamentWorkflows\Http\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;

class WebhookController
{
    public function index(Request $request): JsonResponse
    {
        Event::dispatch(new WebhookEvent($request->except('token')));

        return response()->json(['status' => 'ok']);
    }
}
