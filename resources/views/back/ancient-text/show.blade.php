@section('title')
    | {{ $text->title }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ $text->title }} - 卷 {{ $text->volume }}，第 {{ $text->page }} 页
    </x-slot>

    <div class="w-full p-4">
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
                        <x-ts-button onclick="window.print()" color="green" class="text-sm">
                            <x-ts-icon icon="tabler.printer" class="size-5" />
                            打印
                        </x-ts-button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <!-- 垂直文本容器 -->
                <div class="vertical-text-container">
                    @if (empty($currentPageContent))
                        <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                            {{ __('app.no_content') }}
                        </div>
                    @else
                        @foreach ($currentPageContent as $line)
                            <div class="vertical-text {{ $line['isFirstLineOfParagraph'] ? 'first-line-indent' : '' }}">
                                {{ $line['content'] }}
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- 分页控件 -->
                <div class="flex justify-center items-center mt-4 space-x-4">
                    <!-- 上一页 -->
                    <a href="{{ $currentPage > 1 ? route('ancient-text.show', ['ancient_text' => $text->id, 'page' => ($currentPage - 1)]) : '#' }}" 
                       class="{{ $currentPage <= 1 ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800' }}">
                        <x-ts-icon icon="tabler.arrow-left" class="size-5 inline" />
                        上一页
                    </a>

                    <span class="text-gray-700 dark:text-gray-300">第 {{ $currentPage }} 页 / 共 {{ $totalPages }} 页</span>

                    <!-- 下一页 -->
                    <a href="{{ $currentPage < $totalPages ? route('ancient-text.show', ['ancient_text' => $text->id, 'page' => ($currentPage + 1)]) : '#' }}" 
                       class="{{ $currentPage >= $totalPages ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800' }}">
                        下一页
                        <x-ts-icon icon="tabler.arrow-right" class="size-5 inline" />
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .vertical-text-container {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-start;
                align-items: flex-start;
                width: 1000px;
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

            /* 打印样式 */
            @media print {
                /* 隐藏不需要打印的元素 */
                .no-print {
                    display: none !important;
                }

                /* 隐藏整个页面除内容外的部分 */
                body > *,
                .p-2,
                .flex,
                .flex-col,
                .shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)],
                .p-4 > div:not(.vertical-text-container) {
                    display: none !important;
                }

                /* 确保内容容器可见 */
                .vertical-text-container {
                    display: flex !important;
                    width: auto !important;
                    min-height: auto !important;
                    margin: 0 !important;
                    padding: 10mm !important;
                    border: 1px solid #ccc !important;
                    background-color: #f9f5e9 !important;
                    page-break-inside: avoid;
                }

                .vertical-text {
                    writing-mode: vertical-rl;
                    text-orientation: upright;
                    font-family: 'SimSun', 'Noto Serif CJK SC', serif;
                    font-size: 12pt !important;
                    line-height: 1.8;
                    margin-left: 15mm;
                    color: #333;
                    transform: scaleY(0.9);
                    transform-origin: top;
                    display: inline-block;
                }

                .vertical-text.first-line-indent {
                    padding-top: 2em;
                }

                .vertical-text.first-line-indent::before {
                    content: '';
                    display: block;
                    height: 2em;
                }

                /* 强制分页 */
                .vertical-text:nth-child(10n) {
                    page-break-after: always;
                }

                /* 页面设置 */
                @page {
                    margin: 10mm;
                }
            }
        </style>
    @endpush
</x-app-layout>