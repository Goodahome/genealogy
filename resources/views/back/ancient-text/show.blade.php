@section('title')
    | {{ $text->title }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ $text->title }} - 卷 {{ $text->volume }}，第 {{ $text->page }} 页
    </x-slot>

    <div class="w-full p-2">
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
                        
                        @if(auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:update'))
                            <x-ts-button href="{{ route('ancient-text.edit', $text->id) }}" color="yellow" class="text-sm">
                                <x-ts-icon icon="tabler.edit" class="size-5" />
                                {{ __('app.edit') }}
                            </x-ts-button>
                        @endif
                        
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
                    @for ($index = 0; $index < 17; $index++)
                        @php
                            $line = $currentPageContent[$index] ?? ['content' => '', 'isFirstLineOfParagraph' => false];
                        @endphp
                        <div class="vertical-text-wrapper">
                            <div class="vertical-text {{ $line['isFirstLineOfParagraph'] ? 'first-line-indent' : '' }}">
                            {{ $line['content'] ?: html_entity_decode('&nbsp;') }} 
                            </div>
                        </div>
                    @endfor
                    @php
                        $footer = $currentPageContent[16]['footer'] ?? null;
                    @endphp
                    @if ($footer)
                        <div class="vertical-text-wrapper">
                            <div class="volume-page-footer">
                                {{ $footer }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 分页控件 -->
                <div class="flex justify-center items-center mt-4 space-x-4 no-print">
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
            .vertical-text-wrapper {
                position: relative;
                display: inline-block;
                min-height: 30px;
                width: 300px;
            }
            .vertical-text-container {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-start;
                align-items: flex-start;
                width: 100%;
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
                font-size: 24px;
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
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
            .volume-page-footer {
                writing-mode: vertical-rl;
                text-orientation: upright;
                font-family: 'SimSun', 'Noto Serif CJK SC', serif;
                font-size: 24px;
                color: #333;
                /* position: relative; */
                bottom: 0;
                margin-left: 10px;
                transform: translateX(-50%);
                text-align: center;
            }
            
            .dark .vertical-text-container {
                background-color: #2d2d2d;
                border-color: #444;
            }

            .dark .vertical-text {
                color: #ddd;
            }

            .dark .volume-page-footer {
                color: #bbb;
            }

            /* 打印样式 */
            @media print {
                .no-print {
                    display: none !important;
                }

                .volume-page-footer {
                    font-size: 10pt !important;
                    color: #666 !important;
                }

                body > *,
                .p-2,
                .flex,
                .flex-col,
                .shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)],
                .p-4 > div:not(.vertical-text-container) {
                    display: none !important;
                }

                .vertical-text-container {
                    display: flex !important;
                    flex-direction: row-reverse;
                    justify-content: flex-start;
                    align-items: flex-start;
                    width: auto !important;
                    min-width: 924px !important;
                    min-height: auto !important;
                    margin: 0 !important;
                    padding: 10mm !important;
                    border: 1px solid #ccc !important;
                    background-color: #f9f5e9 !important;
                    page-break-inside: avoid;
                    padding-top: 24pt !important;
                }

                .vertical-text-wrapper {
                    position: relative;
                    display: inline-block;
                    min-height: 22pt;
                    width: 16pt;
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

                .vertical-text.first-line-indent::before {
                    content: '';
                    display: block;
                    height: 2em;
                }

                .vertical-text-wrapper:nth-child(17n) {
                    page-break-after: always;
                }

                @page {
                    margin: 10mm;
                }
            }
        </style>
    @endpush
</x-app-layout>