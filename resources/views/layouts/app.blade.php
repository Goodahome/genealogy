<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) ?: 'zh_cn' }}" dir="ltr" x-data="tallstackui_darkTheme({ dark: true })" x-bind:class="{'dark bg-[#3d3224]': darkTheme, 'bg-[#3d3224]': !darkTheme}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Genealogy Application - Manage your family tree and discover your ancestry.">

    <title>{{ config('app.name', '族谱网') }} @yield('title')</title>

    <!-- favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon/favicon-16x16.png') }}" sizes="16x16">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon/favicon-96x96.png') }}" sizes="96x96">

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;600;700&family=Noto+Serif+SC:wght@500;600;700&family=ZCOOL+XiaoWei&display=swap" rel="stylesheet">

    <!-- scripts -->
    <tallstackui:script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- styles -->
    @livewireStyles
    @filamentStyles
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <div class="zp-shell">
        <!-- notifications -->
        <x-ts-toast />
        <x-ts-dialog />

        <!-- offcanvas menu -->
        @include('layouts.partials.offcanvas')

        <!-- header -->
        @include('layouts.partials.header')

        <!-- main content -->
        <main>
            {{ $slot }}
        </main>

        @if(config('app.show_footer', false))
        <!-- footer 如果需要显示底栏请改为true-->
            @include('layouts.partials.footer')
        @endif
    </div>

    <!-- scripts -->
    @livewireScripts
    @filamentScripts
    @stack('scripts')
</body>

</html>
