<?php

use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentWorkflows\Http\Controller\WebhookController;
use TomatoPHP\FilamentWorkflows\Http\Middlewares\WorkflowWebhookMiddleware;

Route::middleware([WorkflowWebhookMiddleware::class, 'throttle:60,1'])
    ->match(['get', 'post'], '/workflows/webhook', [WebhookController::class, 'index'])
    ->name('workflows.webhook');
