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

        $companySlug = $request->route('company');

        if ($slug) {
            $event = Events::with('company')
                ->where('slug', $slug)
                ->when($companySlug, function ($query) use ($companySlug) {
                    $query->where(function ($eventQuery) use ($companySlug) {
                        $eventQuery->where('domain', $companySlug)
                            ->orWhereHas('company', fn($company) => $company->where('slug', $companySlug));
                    });
                })
                ->firstOrFail();
            app()->instance('event', $event);
        }

        return $next($request);
    }
}
