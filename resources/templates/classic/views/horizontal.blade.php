@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "horizontal", "layout":"boxed" }, "storagePrefix" : "starter-project", "showSettings" : false }'];
    $title = 'Horizontal Starter Page';
    $description= 'An empty page with a boxed horizontal layout.';
    $breadcrumbs = ["/" => "Home"];
@endphp

@extends('layout', ['html_tag_data' => $html_tag_data, 'title' => $title, 'description' => $description])

@section('css')
    {{-- Tailwind (через Vite) --}}
    {{--    @vite(['resources/css/app.css'])--}}
@endsection

@section('js_vendor')
    {{-- Vue компонент (через Vite) --}}

@endsection

@section('js_page')
    <script src="/js/pages/logout.js"></script>
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

        <!-- Content Start -->
        <div class="card mb-2">
            <div class="card-body h-100">
                {{ $description }}
            </div>
        </div>

    </div>
@endsection
