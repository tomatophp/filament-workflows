<?php

use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentWorkflows\Http\Controller\WebhookController;

Route::middleware(['web', \TomatoPHP\FilamentWorkflows\Http\Middlewares\WorkflowWebhookMiddleware::class])->group(function (){
    Route::get('/workflows/webhook', [WebhookController::class, 'index'])->name('workflows.webhook');
});
