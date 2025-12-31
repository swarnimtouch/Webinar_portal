<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('sidebarMenu')) {
    function sidebarMenu()
    {
        $userType = auth()->user()->type ?? 'guest';
        $module = [];

        // Admin only menus
        if ($userType === 'admin') {
            $module = [
                [
                    'route' => route('dashboard'),
                    'name' => 'Dashboard',
                    'icon' => 'bi bi-grid fs-3',
                    'child' => [],
                    'all_routes' => ['dashboard']
                ],
                [
                    'route' => route('user.index'),
                    'name' => 'Users',
                    'icon' => 'bi bi-people fs-3',
                    'child' => [],
                    'all_routes' => ['user.index', 'user.create', 'user.edit']
                ],
                [
                    'route' => '#',
                    'name' => 'General Settings',
                    'icon' => 'bi bi-gear',
                    'child' => [
                        [
                            'route' => route('settings'),
                            'name' => 'Site Settings',
                            'icon' => 'bi bi-sliders',
                            'all_routes' => ['settings']
                        ],
                        [
                            'route' => route('banners'),
                            'name' => 'Banners',
                            'icon' => 'bi bi-image',
                            'all_routes' => ['banners', 'banner.create', 'banner.edit']
                        ],
                        [
                            'route' => route('speakers'),
                            'name' => 'Speakers',
                            'icon' => 'bi bi-mic',
                            'all_routes' => ['speakers', 'speaker.create', 'speaker.edit']
                        ],
                        [
                            'route' => route('brand'),
                            'name' => 'Brands',
                            'icon' => 'bi bi-tags',
                            'all_routes' => ['brand', 'brand.create', 'brand.edit']
                        ],
                        [
                            'route' => route('content'),
                            'name' => 'Content',
                            'icon' => 'bi bi-file-text',
                            'all_routes' => ['content', 'content.edit']
                        ],
                    ],
                    'all_routes' => [
                        'settings', 'banners', 'banner.create', 'banner.edit',
                        'speakers', 'speaker.create', 'speaker.edit',
                        'brand', 'brand.create', 'brand.edit',
                        'content', 'content.edit'
                    ]
                ],
                [
                    'route' => route('dynamic-fields'),
                    'name' => 'Dynamic Fields',
                    'icon' => 'bi bi-sliders',
                    'child' => [],
                    'all_routes' => ['dynamic-fields','dynamic-fields.store']
                ],
            ];
        }

        return $module;
    }
}


if (!function_exists('isMenuActive')) {
    function isMenuActive($routes = [])
    {
        if (is_string($routes)) {
            $routes = [$routes];
        }
        return request()->routeIs($routes) ? 'active' : '';
    }
}

if (!function_exists('isMenuOpen')) {
    function isMenuOpen($allRoutes = [])
    {
        if (is_string($allRoutes)) {
            $allRoutes = [$allRoutes];
        }

        return request()->routeIs($allRoutes) ? 'here show' : '';
    }
}

if (!function_exists('breadcrumb')) {
    function breadcrumb(array $custom = [])
    {
        $routeName = request()->route()?->getName();

        // Base breadcrumb
        $breadcrumbs = [
            'Home' => route('dashboard')
        ];

        if ($routeName) {

            // admin.user.create
            $parts  = explode('.', $routeName);
            $action = end($parts);                 // index/create/edit
            $module = $parts[count($parts) - 2] ?? null;

            if ($module) {
                $moduleTitle = ucfirst(str_replace('_', ' ', $module));

                // Users
                $breadcrumbs[$moduleTitle] = null;
            }

            // Add User / Edit User
            if (in_array($action, ['create', 'edit'])) {
                $breadcrumbs[ucfirst($action) . ' ' . $moduleTitle] = null;
            }
        }

        // Custom breadcrumbs override / extend
        if (!empty($custom)) {
            $breadcrumbs = array_merge(['Home' => route('dashboard')], $custom);
        }

        // ---------------- RENDER ----------------
        ob_start();
        ?>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
            <?php
            $total = count($breadcrumbs);
            $i = 1;

            foreach ($breadcrumbs as $title => $link) {
                ?>
                <li class="breadcrumb-item text-muted">
                    <?php if ($link && $i !== $total): ?>
                        <a href="<?= $link ?>" class="text-muted text-hover-primary">
                            <?= ucfirst($title) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-dark"><?= ucfirst($title) ?></span>
                    <?php endif; ?>
                </li>

                <?php if ($i < $total): ?>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-300 w-5px h-2px"></span>
                    </li>
                <?php endif; ?>

                <?php $i++; ?>
            <?php } ?>
        </ul>
        <?php

        return ob_get_clean();
    }
}



if (!function_exists('canAccessRoute')) {
    function canAccessRoute($roles = [])
    {
        $userType = auth()->user()->user_type ?? 'guest';
        return in_array($userType, $roles);
    }
}
if (!function_exists('pageTitle')) {
    function pageTitle()
    {
        $route = request()->route()?->getName();

        if (!$route) {
            return 'Dashboard';
        }

        // admin.user.create → ['admin','user','create']
        // user.edit → ['user','edit']
        $parts = explode('.', $route);

        // words jo title nahi hone chahiye
        $ignore = ['index','create','edit','store','update','destroy','show'];

        foreach ($parts as $part) {
            if (!in_array($part, $ignore)) {
                return ucfirst(str_replace('_', ' ', $part));
            }
        }

        return 'Dashboard';
    }
}

if (!function_exists('siteSetting')) {
    function siteSetting($key, $default = null)
    {
        return DB::table('general_settings')
            ->where('unique_name', $key)
            ->value('value') ?? $default;
    }
}






