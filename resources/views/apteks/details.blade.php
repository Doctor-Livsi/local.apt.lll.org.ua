@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "horizontal", "layout":"fluid" }, "storagePrefix" : "starter-project", "showSettings" : true }'];
    $title = "$id";
    $description= 'An empty page with a boxed horizontal layout.';
    $breadcrumbs = ["/" => "Главная","/" => "Главная" ];
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
                    <h2>{{ $description }}/{{ $id }}</h2>
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
    <h2>{{ $description }}/{{ $id }}</h2>
    <h1>Главная</h1>

    {{-- Vue компонент з виводом лічильників --}}
    <div class="row mb-5">

        <div class="col-12 col-lg-6">
            <div id="apteksCounterWidget"></div>
        </div>
        <div class="col-12 col-lg-6">
            <div id="apteksCounterChatBotWidget"></div>
        </div>
    </div>
@endsection
