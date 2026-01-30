@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "horizontal", "layout":"fluid" }, "storagePrefix" : "starter-project", "showSettings" : true }'];
    $title = 'Главная';
    $description= 'An empty page with a boxed horizontal layout.';
    $breadcrumbs = ["/" => "Главная"];
@endphp

@extends('_layout', ['html_tag_data' => $html_tag_data, 'title' => $title, 'description' => $description])

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
    </div>
@endsection

@section('content')
    <h1>Главная</h1>
    <p>Новости/объявления будут здесь.</p>
@endsection
