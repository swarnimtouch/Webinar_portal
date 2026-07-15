<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubAdminMenuAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();

        if ($user?->type === 'sub_admin' && !sub_admin_can_access_route($request->route()?->getName())) {
            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }
}
