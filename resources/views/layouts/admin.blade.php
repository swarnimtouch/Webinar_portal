@extends('layouts.master')

@section('body')
    <div class="app-root d-flex flex-column flex-row-fluid" id="kt_app_root">

        {{-- Header --}}
        @include('partials.header')

        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

            {{-- Sidebar --}}
            @include('partials.sidebar')

            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <div class="d-flex flex-column flex-column-fluid">

                    {{-- Toolbar --}}
                    @include('partials.toolbar')

                    {{-- Content --}}
                    <div id="kt_app_content" class="app-content flex-column-fluid">
                        <div class="container-fluid">
                            @yield('content')
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                @include('partials.footer')
            </div>
        </div>
    </div>
@endsection
