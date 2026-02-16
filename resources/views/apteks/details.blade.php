@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "horizontal", "layout":"fluid" }, "storagePrefix" : "starter-project", "showSettings" : true }'];
    $title = "{$apteka->name}";
    $description= 'An empty page with a boxed horizontal layout.';
    $breadcrumbs = ["/" => "Главная","/" => "Главная" ];
@endphp

@extends('_layout', ['html_tag_data' => $html_tag_data, 'title' => $title, 'description' => $description])

@section('css')
    {{--    @vite(['resources/css/app.css'])--}}
@endsection

@section('js_page')
    <script src="/js/pages/logout.js"></script>
    @vite(['resources/js/app.js'])
    {{--    @vite(['resources/js/pages/apteks/apteks.js'])--}}
@endsection

@section('js_vendor')
    @vite(['resources/js/pages/apteks/apteks.js'])
    <script src="{{ asset('js/vendor/mousetrap.min.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="page-title-container">
            <div class="row">
                <div class="col-12 col-md-7">
                    <h1 class="mb-0 pb-0 display-4" id="title">{{ $title }}</h1>
                    @include('_layout.breadcrumb', ['breadcrumbs' => $breadcrumbs])
                </div>
            </div>
        </div>
    </div>
    {{-- Верхні віджети: зв'язок/бот (WS) --}}
    <div class="container">
        <div class="row row-cols-1 row-cols-sm-1 row-cols-xxl-2 g-3">
            <div class="col">
                <div id="apteksCounterWidget"></div>
            </div>
            <div class="col">
                <div id="apteksCounterChatBotWidget"></div>
            </div>
        </div>
    </div>
    {{-- Основная карточка: отдельный контейнер и отдельный row --}}
    <div class="container">
        <div class="row row-cols-1 row-cols-sm-1 row-cols-xxl-2 g-3">
            {{-- Левая колонка (паспорт): всегда отдельная колонка --}}
            <div class="col-12 col-xl-4 col-xxl-3">
                <div>
                    <div id="apteksPassportBlock" data-apteka-id="{{ $apteka->id }}"></div>
                </div>
            </div>

            {{-- Правая колонка --}}
            <div class="col-12 col-xl-4 col-xxl-9">
                <div class="row">
                    <div class="col-12 col-lg-4 mb-3">
                        <div id="apteksScheduleBlock" data-apteka-id="{{ $apteka->id }}"></div>
                    </div>
                    <div class="col-12 col-lg-8 mb-3">
                        <div id="apteksCardEmployeesBlock" data-apteka-id="{{ $apteka->id }}"></div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-sm-1 row-cols-xxl-12 g-2">
                    {{-- Блок "Зв'язок": провайдери + LTE (збирається всередині Vue) --}}
                    <div class="col">
                        <div id="apteksProvidersRowBlock" data-apteka-id="{{ $apteka->id }}"></div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-sm-1 row-cols-xxl-2 g-2">
                    {{-- Нижние блоки по 12 --}}
                    <div class="col">
                        <div id="apteksTechniqueBlock" data-apteka-id="{{ $apteka->id }}"></div>
                    </div>
                    <div class="col">
                        <div id="apteksVisitsBlock" data-apteka-id="{{ $apteka->id }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Підключення Vite entry саме для цієї сторінки --}}
    @vite(['resources/js/components/apteks/details.js'])
@endsection

