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
        $baseDomain = strtolower(config('app.event_base_domain', 'doctorly.in'));
        $liveHost = strtolower(config('app.event_live_subdomain', 'live') . '.' . $baseDomain);
        $requestHost = strtolower($request->getHost());

        // On the public domain, events are available only below the fixed
        // live subdomain. Localhost/IP routes remain available for development.
        if (($requestHost === $baseDomain || str_ends_with($requestHost, '.' . $baseDomain))
            && $requestHost !== $liveHost) {
            abort(404);
        }

        if ($slug) {
            $event = Events::with('company')
                ->where('slug', $slug)
                ->when($companySlug, function ($query) use ($companySlug) {
                    $query->whereHas(
                        'company',
                        fn($company) => $company->where('slug', $companySlug)
                    );
                })
                ->firstOrFail();
            app()->instance('event', $event);
        }

        return $next($request);
    }
}
