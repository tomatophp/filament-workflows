<?php

use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentWorkflows\Events\Webhook\WebhookEvent;
use TomatoPHP\FilamentWorkflows\Models\WorkflowLog;
use TomatoPHP\FilamentWorkflows\Tests\Fixtures\RecordRunAction;

beforeEach(function () {
    RecordRunAction::$runs = [];
});

it('dispatches the webhook event without the token', function () {
    Event::fake([WebhookEvent::class]);

    $this->getJson(route('workflows.webhook', ['token' => 'secret-token', 'invoice_paid' => 'in_1']))
        ->assertOk()
        ->assertJson(['status' => 'ok']);

    Event::assertDispatched(WebhookEvent::class, fn (WebhookEvent $event) => $event->data === ['invoice_paid' => 'in_1']);
});

it('accepts a POST with the token header', function () {
    Event::fake([WebhookEvent::class]);

    $this->postJson(route('workflows.webhook'), ['invoice_paid' => 'in_1'], ['X-Workflow-Token' => 'secret-token'])
        ->assertOk();

    Event::assertDispatched(WebhookEvent::class);
});

it('rejects a missing or wrong token', function () {
    Event::fake([WebhookEvent::class]);

    $this->getJson(route('workflows.webhook'))->assertForbidden();
    $this->getJson(route('workflows.webhook', ['token' => 'wrong']))->assertForbidden();

    Event::assertNotDispatched(WebhookEvent::class);
});

it('stays closed when no webhook token is configured', function () {
    config()->set('filament-workflows.webhook_token', null);
    Event::fake([WebhookEvent::class]);

    $this->get('/workflows/webhook?token=')->assertForbidden();

    Event::assertNotDispatched(WebhookEvent::class);
});

it('runs webhook triggers whose key is in the payload and logs them', function () {
    $records = makeWorkflow(['type' => 'webhook', 'webhook' => 'invoice_paid']);

    $this->getJson(route('workflows.webhook', ['token' => 'secret-token', 'other_key' => 1]))->assertOk();
    expect(RecordRunAction::$runs)->toBeEmpty();

    $this->getJson(route('workflows.webhook', ['token' => 'secret-token', 'invoice_paid' => 'in_1']))->assertOk();

    expect(RecordRunAction::$runs)->toHaveCount(1);

    $log = WorkflowLog::query()->sole();

    expect($log->trigger_id)->toBe($records['trigger']->id)
        ->and($log->payload)->toBe(['invoice_paid' => 'in_1']);
});
