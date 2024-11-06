<?php

namespace TomatoPHP\FilamentWorkflows\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkflowWebhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->has('token')){
            if($request->get('token') === config('filament-workflows.webhook_token')){
                return $next($request);
            }
            else {
                abort(403);
            }
        }
        else {
            abort(403);
        }

    }
}
