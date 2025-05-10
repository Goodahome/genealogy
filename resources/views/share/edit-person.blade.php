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
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4">
                <h1 class="text-2xl font-bold mb-6">{{ __('person.edit_person') }}: {{ $person->name }}</h1>
                
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            
            <form action="{{ route('share.update', ['token' => $token]) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700">{{ __('person.firstname') }}</label>
                        <input type="text" name="firstname" id="firstname" value="{{ old('firstname', $person->firstname) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('firstname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="surname" class="block text-sm font-medium text-gray-700">{{ __('person.surname') }}</label>
                        <input type="text" name="surname" id="surname" value="{{ old('surname', $person->surname) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="birthname" class="block text-sm font-medium text-gray-700">{{ __('person.birthname') }}</label>
                        <input type="text" name="birthname" id="birthname" value="{{ old('birthname', $person->birthname) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="nickname" class="block text-sm font-medium text-gray-700">{{ __('person.nickname') }}</label>
                        <input type="text" name="nickname" id="nickname" value="{{ old('nickname', $person->nickname) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700">{{ __('person.sex') }}</label>
                        <select name="sex" id="sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="m" {{ old('sex', $person->sex) == 'm' ? 'selected' : '' }}>{{ __('app.male') }}</option>
                            <option value="f" {{ old('sex', $person->sex) == 'f' ? 'selected' : '' }}>{{ __('app.female') }}</option>
                            <option value="u" {{ old('sex', $person->sex) == 'u' ? 'selected' : '' }}>{{ __('app.unknown') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="yob" class="block text-sm font-medium text-gray-700">{{ __('person.yob') }}</label>
                        <input type="number" name="yob" id="yob" value="{{ old('yob', $person->yob) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700">{{ __('person.dob') }}</label>
                        <input type="date" name="dob" id="dob" value="{{ old('dob', $person->dob) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="pob" class="block text-sm font-medium text-gray-700">{{ __('person.pob') }}</label>
                        <input type="text" name="pob" id="pob" value="{{ old('pob', $person->pob) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('app.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>