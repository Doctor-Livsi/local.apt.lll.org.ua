@php
    $html_tag_data = ["override"=>'{ "attributes" : { "placement" : "vertical", "layout":"fluid" }, "storagePrefix" : "starter-project", "showSettings" : false }'];
    $title = 'Vertical Starter Page';
    $description= 'An empty page with a fluid vertical layout.';
    $breadcrumbs = ["/"=>"Home"]
@endphp
@extends('layout',['html_tag_data'=>$html_tag_data, 'title'=>$title, 'description'=>$description])

@section('css')
{{-- Tailwind (через Vite) --}}
{{--    @vite(['resources/css/app.css'])--}}
@endsection

@section('js_vendor')
    @vite(['resources/js/app.js'])
    <script src="/js/pages/logout.js"></script>
@endsection

@section('js_page')
    <script src="/js/pages/vertical.js"></script>
    <script>
        console.log('DOM:', document.querySelector('#menu'))
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoutButton = document.getElementById('logoutButton');
            if (logoutButton) {
                logoutButton.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    try {
                        const response = await fetch('/logout', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                            },
                            credentials: 'include',
                        });
                        if (response.ok) {
                            window.location.href = '/login';
                        } else {
                            console.error('Помилка виходу:', await response.text());
                        }
                    } catch (error) {
                        console.error('Помилка:', error.message);
                    }
                });
            }
        });
    </script>
@endsection

@section('content')
        <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
                <!-- Title Start -->
                <div class="col-12 col-md-7">
                    <h1 class="mb-0 pb-0 display-4" id="title">{{ $title }}</h1>
                    @include('_layout.breadcrumb',['breadcrumbs'=>$breadcrumbs])
                </div>
                <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->

        <!-- Content Start -->
        <div class="card mb-2">
            <div class="card-body h-100">{{ $description }}</div>
        </div>
        <!-- Content End -->
    </div>
@endsection
