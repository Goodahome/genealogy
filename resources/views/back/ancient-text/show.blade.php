@section('title')
    &vert; {{ $text->title }} - {{ __('app.volume') }} {{ $text->volume }}, {{ __('app.page') }} {{ $text->page }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ $text->title }} - {{ __('app.volume') }} {{ $text->volume }}, {{ __('app.page') }} {{ $text->page }}
    </x-slot>

    <div class="p-2 overflow-x-auto max-w-7xl grow dark:text-neutral-200">
        <div class="flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
            <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
                <div class="flex flex-wrap items-start justify-center gap-2">
                    <div class="flex-1 grow max-w-full min-w-max">
                        {{ $text->title }}
                    </div>

                    <div class="flex-1 grow max-w-full text-end min-w-max">
                        <x-ts-button href="{{ route('ancient-text.index') }}" color="primary" class="text-sm">
                            <x-ts-icon icon="tabler.arrow-left" class="size-5" />
                            {{ __('app.back') }}
                        </x-ts-button>
                        <x-ts-button href="{{ route('ancient-text.edit', $text->id) }}" color="yellow" class="text-sm">
                            <x-ts-icon icon="tabler.edit" class="size-5" />
                            {{ __('app.edit') }}
                        </x-ts-button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <div class="vertical-text-container">
                    <div class="vertical-text">
                        {{ $text->content }} ——《{{ $text->title }}》，{{ __('app.volume') }} {{ $text->volume }}，{{ __('app.page') }} {{ $text->page }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .vertical-text-container {
            display: flex;
            flex-direction: row-reverse; /* 从右到左排列 */
            justify-content: flex-end;
            width: 100%;
            min-height: 600px;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 10px 0;
            overflow-x: auto; /* 如果内容过多，允许水平滚动 */
            background-color: #f9f5e9; /* 古籍纸张的颜色 */
        }

        .vertical-text {
            writing-mode: vertical-rl; /* 垂直方向，从右到左 */
            text-orientation: upright; /* 字符保持直立 */
            font-family: 'SimSun', 'Noto Serif CJK SC', serif; /* 使用适合古籍的字体 */
            font-size: 18px;
            line-height: 1.8;
            margin-left: 20px; /* 列间距 */
            text-align: right;
            color: #333;
        }

        /* 暗黑模式适配 */
        .dark .vertical-text-container {
            background-color: #2d2d2d;
            border-color: #444;
        }

        .dark .vertical-text {
            color: #ddd;
        }
    </style>
</x-app-layout>