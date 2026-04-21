<?php

namespace App\Http\Middleware;

use App\Models\Events;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetEvent
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug')
            ?? $request->header('X-Event-Slug');

        if ($slug) {
            $event = Events::where('slug', $slug)->firstOrFail();
            app()->instance('event', $event);
        }

        return $next($request);
    }
}
