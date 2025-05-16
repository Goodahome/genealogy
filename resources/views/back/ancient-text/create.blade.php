@section('title')
    | {{ __('app.create_ancient_text') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.create_ancient_text') }}
    </x-slot>

    <div class="w-full p-4">
        <div class="flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
            <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
                <div class="flex flex-wrap items-start justify-center gap-2">
                    <div class="flex-1 grow max-w-full min-w-max">
                        {{ __('app.create_ancient_text') }}
                    </div>

                    <div class="flex-1 grow max-w-full text-end min-w-max">
                        <x-ts-button href="{{ route('ancient-text.index') }}" color="primary" class="text-sm">
                            <x-ts-icon icon="tabler.arrow-left" class="size-5" />
                            {{ __('app.back') }}
                        </x-ts-button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <form method="POST" action="{{ route('ancient-text.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <x-ts-input name="title" label="{{ __('app.title') }}" required />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-ts-input type="number" name="volume" label="{{ __('app.volume') }}" value="{{ old('volume', 1) }}" min="1" required />
                        </div>
                        <div>
                            <x-ts-input type="number" name="page" label="{{ __('app.page') }}" value="{{ old('page', 1) }}" min="1" required />
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <x-ts-checkbox name="show_on_homepage" label="在首页展示此古籍" :checked="old('show_on_homepage')" />
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('app.content') }}</label>
                        <textarea id="content" name="content" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <div class="preview-container">
                            <h3 class="text-lg font-medium mb-2">{{ __('app.preview') }}</h3>
                            <div class="vertical-text-container" id="preview-text">
                                <!-- 预览内容将由 JavaScript 动态填充 -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <x-ts-button type="submit" color="emerald" class="text-sm text-white">
                            {{ __('app.save') }}
                        </x-ts-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .vertical-text-container {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
            align-items: flex-start;
            width: 800px;
            min-height: 400px;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 10px auto;
            overflow-x: auto;
            background-color: #f9f5e9;
        }

        .vertical-text {
            writing-mode: vertical-rl;
            text-orientation: upright;
            font-family: 'SimSun', 'Noto Serif CJK SC', serif;
            font-size: 30px;
            line-height: 1.8;
            margin-left: 20px;
            text-align: start;
            color: #333;
            transform: scaleY(0.9);
            transform-origin: top;
            display: inline-block;
        }

        /* 仅段落首行应用缩进 */
        .vertical-text.first-line-indent {
            padding-top: 2em; /* 首行缩进 */
        }

        .vertical-text.first-line-indent::before {
            content: '';
            display: block;
            height: 2em;
        }

        .dark .vertical-text-container {
            background-color: #2d2d2d;
            border-color: #444;
        }

        .dark .vertical-text {
            color: #ddd;
        }
    </style>
</x-app-layout>