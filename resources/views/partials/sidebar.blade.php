<!--begin::Aside menu-->
<div class="aside-menu flex-column-fluid">
    <div class="hover-scroll-overlay-y my-5" id="kt_aside_menu_wrapper">
        <div class="menu menu-column" data-kt-menu="true">

            @foreach(sidebarMenu() as $menu)

                {{-- SINGLE MENU --}}
                @if(empty($menu['child']))
                    <div class="menu-item">
                        <a class="menu-link {{ isMenuActive($menu['all_routes']) }}"
                           href="{{ $menu['route'] }}">

                            <span class="menu-icon">
                                <i class="{{ $menu['icon'] }}"></i>
                            </span>

                            <span class="menu-title">{{ $menu['name'] }}</span>
                        </a>
                    </div>

                    {{-- PARENT MENU --}}
                @else
                    <div data-kt-menu-trigger="click"
                         class="menu-item menu-accordion {{ isMenuOpen($menu['all_routes']) }}">

                        <span class="menu-link">

                            <span class="menu-icon">
                                <i class="{{ $menu['icon'] }}"></i>
                            </span>

                            <span class="menu-title">{{ $menu['name'] }}</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">
                            @foreach($menu['child'] as $child)
                                <div class="menu-item">
                                    <a class="menu-link {{ isMenuActive($child['all_routes']) }}"
                                       href="{{ $child['route'] }}">

                                        <span class="menu-bullet">
                                            <i class="{{ $child['icon'] }}"></i>
                                        </span>

                                        <span class="menu-title">{{ $child['name'] }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @endforeach

        </div>
    </div>
</div>
<!--end::Aside menu-->
