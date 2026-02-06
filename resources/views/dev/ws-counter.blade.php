@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "horizontal", "layout":"fluid" }, "storagePrefix" : "starter-project", "showSettings" : true }'];
    $title = 'Діючі аптеки';
    $description= 'An empty page with a boxed horizontal layout.';
    $breadcrumbs = ["/" => "Home","/apteks" =>"Аптеки" ];
@endphp

@extends('_layout', ['html_tag_data' => $html_tag_data, 'title' => $title, 'description' => $description])

@section('content')
    <div class="container py-4">
        <h3 class="mb-3">WS Counter (dev)</h3>
        <div id="wsCounterApp"></div>
    </div>
@endsection

@vite(['resources/js/ws-counter-app.js'])
