<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use \App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware(['web'])->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::prefix('{company}/{slug}')
                ->name('company.local.')
                ->where([
                    'company' => '[a-z0-9\-]+',
                    'slug' => '^(?!admin$|admin/|admin$|admin-)[a-z0-9\-]+$',
                ])
                ->middleware(['event','web'])
                ->group(base_path('routes/event.php'));

            Route::prefix('{slug}')
                ->where(['slug' => '^(?!admin$|admin/|admin$|admin-)[a-z0-9\-]+$'])
                ->middleware(['event','web'])
                ->group(base_path('routes/event.php'));

            Route::domain('{company}.' . config('app.event_base_domain', 'doctorly.in'))
                ->prefix('{slug}')
                ->name('company.live.')
                ->where([
                    'company' => '[a-z0-9\-]+',
                    'slug' => '^(?!admin$|admin/|admin$|admin-)[a-z0-9\-]+$',
                ])
                ->middleware(['event','web'])
                ->group(base_path('routes/event.php'));


        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([
            \App\Http\Middleware\DefineConstants::class,
        ]);
        $middleware->alias([
            'event' => \App\Http\Middleware\SetEvent::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {

            if (
                $request->routeIs('admin.*') ||
                $request->is('admin/*')
            ) {
                return route('admin.login');
            }

            return event_route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
