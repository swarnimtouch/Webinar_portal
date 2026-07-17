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

        if ($user?->type === 'sub_admin'
            && str_starts_with((string) $request->route()?->getName(), 'admin.event_setting')
            && !$user->event?->allow_sub_admin_settings) {
            abort(403, 'Settings access is disabled for this event.');
        }

        if ($user?->type === 'sub_admin'
            && str_starts_with((string) $request->route()?->getName(), 'admin.comments.')
            && !$user->event?->enable_comments) {
            abort(403, 'Comments are disabled for this event.');
        }

        if ($user?->type === 'sub_admin'
            && str_starts_with((string) $request->route()?->getName(), 'admin.chat_log')
            && !$user->event?->enable_live_chat) {
            abort(403, 'Live chat is disabled for this event.');
        }

        if ($user?->type === 'sub_admin' && !sub_admin_can_access_route($request->route()?->getName())) {
            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }
}
