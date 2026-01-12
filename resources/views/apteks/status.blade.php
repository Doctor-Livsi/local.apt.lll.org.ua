@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "horizontal", "layout":"fluid" }, "storagePrefix" : "starter-project", "showSettings" : true }'];
    $title = 'Аптеки ' . $status;
    $description= 'An empty page with a boxed horizontal layout.';
    $breadcrumbs = ["/" => "Home"];
@endphp

@extends('_layout', ['html_tag_data' => $html_tag_data, 'title' => $title, 'description' => $description])

@section('css')
    {{-- Tailwind (через Vite) --}}
    {{--    @vite(['resources/css/app.css'])--}}
@endsection

@section('js_page')
    <script src="/js/pages/logout.js"></script>
    @vite(['resources/js/app.js'])
    @vite(['resources/js/pages/Apteks/apteks.js'])
@endsection
@section('js_vendor')
    @vite(['resources/js/pages/Apteks/apteks.js'])
    <script src="{{ asset('js/vendor/mousetrap.min.js') }}"></script>
@endsection

@section('js_page')
{{--    <script src="/js/pages/horizontal.js"></script>--}}
<script src="/js/pages/logout.js"></script>
    @vite(['resources/js/app.js'])
    @vite(['resources/js/pages/Apteks/apteks.js'])
@endsection

@section('content')
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
                <!-- Title Start -->
                <div class="col-12 col-md-7">
                    <h1 class="mb-0 pb-0 display-4" id="title">{{ $title }}</h1>
                    @include('_layout.breadcrumb', ['breadcrumbs' => $breadcrumbs])
                </div>
                <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->


        {{-- Vue компонент з виводом аптек --}}
{{--                {{ $status }}--}}
        <div id="apteks-table-wrapper" data-status="{{ $status }}"></div>

    </div>
@endsection
