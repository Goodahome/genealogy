<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | {{ __('person.edit_person') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ __('person.edit_person') }}: {{ $person->name }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ __('app.shared_edit_notice') }}
                </p>
            </div>
        </header>

        <main>
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- 添加导航标签 -->
                        <div class="mb-6">
                            <x-ts-tab selected="Profile">
                                <x-ts-tab.items tab="Profile">
                                    <!-- 个人资料编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        <livewire:people.edit.profile :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                                
                                <x-ts-tab.items tab="Family">
                                    <!-- 家庭关系编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        <livewire:people.edit.family :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                                
                                <x-ts-tab.items tab="Partners">
                                    <!-- 伴侣编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        
                                        <livewire:people.edit.partner :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                                
                                <x-ts-tab.items tab="Children">
                                    <!-- 子女编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        <livewire:people.children :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                                
                                <x-ts-tab.items tab="Siblings">
                                    <!-- 兄弟姐妹编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        <livewire:people.siblings :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                                
                                <x-ts-tab.items tab="Photos">
                                    <!-- 照片编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        <livewire:people.edit.photos :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                                
                                <x-ts-tab.items tab="Files">
                                    <!-- 文件编辑 -->
                                    <div class="w-full p-2 space-y-5">
                                        <livewire:people.edit.files :person="$person" :isSharedEdit="true" :token="$token" />
                                    </div>
                                </x-ts-tab.items>
                            </x-ts-tab>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>