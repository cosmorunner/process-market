<?php

/* @var \App\Models\User $user */

$user = \Illuminate\Support\Facades\Auth::user();

?>

        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/img/logo.png"/>
    <link rel="shortcut icon" type="image/png" href="/img/logo.png"/>

    <title>{{ $title ?? config('app.name') }}</title>
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- In den Views wird das viewspezifische JS gepusht -->
    @stack('header_js')

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    @include('layouts.partials.navigation')
    @include('partials.flash-messages')

    <main>
        @yield('content')
    </main>
    @include('partials.footer')
</div>
</body>
</html>
