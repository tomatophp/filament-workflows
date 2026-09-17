<?php

namespace TomatoPHP\FilamentWorkflows\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The webhook endpoint is closed unless `filament-workflows.webhook_token`
 * (FILAMENT_WORKFLOWS_WEBHOOK_TOKEN) is set. Callers must send the same token
 * as the `token` query/body parameter or the `X-Workflow-Token` header.
 */
class WorkflowWebhookMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('filament-workflows.webhook_token');
        $given = $request->header('X-Workflow-Token') ?? $request->input('token');

        if (! is_string($expected) || $expected === '' || ! is_string($given) || ! hash_equals($expected, $given)) {
            abort(403);
        }

        return $next($request);
    }
}
