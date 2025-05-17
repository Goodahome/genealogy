@section('title')
    | {{ __('app.home') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.home') }}
    </x-slot>

    <div class="w-full p-4 space-y-5">
        <div class="dark:text-neutral-200">
            <div class="flex flex-col w-full pt-6 sm:pt-0">
                <!-- 两栏布局 -->
                <div class="w-full mt-2 grid grid-cols-1 lg:grid-cols-3 gap-2">
                    <!-- 左侧：时间线事件 -->
                    <div class="rounded-sm p-10 w-full mx-auto">
                        <!-- 头部区域：更现代的标题和按钮 -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 transform translate-x-5">{{ __('app.events') }}</h2>
                            <!-- <a href="{{ route('events.create') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-300 ease-in-out">
                                添加事件
                            </a> -->
                        </div>

                        <!-- 成功提示：更柔和的颜色和动画 -->
                        @if (session('success'))
                            <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6 animate-fade-in">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- 错误提示：改进的错误样式 -->
                        @if (isset($error))
                            <div class="bg-red-100 text-red-800 p-4 rounded-md mb-6 animate-fade-in">
                                {{ $error }}
                            </div>
                        @endif

                        <!-- 事件列表容器 -->
                        <div class="space-y-6 relative">
                            <!-- 连续的时间线 - 移到左侧 -->
                            <div class="absolute left-1 w-1 bg-gray-600 z-10" style="top: -200px; bottom: -100px;"></div>
                            <!-- 使事件时间按最新的日期排序，并限制显示6个 -->
                            @php $latestEvents = $events->sortByDesc('date')->take(6); @endphp
                            @forelse ($latestEvents as $event)
                                <!-- 单个事件：卡片样式，带悬停效果 -->
                                <div class="relative block items-baseline group duration-300 bg-gray-100 rounded-md p-5 pl-8">
                                    <!-- 时间线节点 - 移到左侧 -->
                                    <div class="absolute left-0 w-5 h-5 bg-gray-600 rounded-full mt-2 transform -translate-x-2"></div>

                                    <!-- 日期部分：更清晰的排版 -->
                                    <div class="w-full pr-8 text-left">
                                        <span class="text-sm font-semibold text-gray-500 break-words">
                                            {{ $event->date->format('Y-m-d') }}
                                        </span>
                                    </div>

                                    <!-- 事件内容：卡片化，带描述截断 -->
                                    <div class="w-full mt-2">
                                        <h3 class="text-base font-bold text-gray-500 group-hover:text-blue-200 transition-colors duration-300 break-words">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-sm tracking-widest text-gray-600 line-clamp-2 break-words whitespace-normal">
                                            {{ $event->description ?? '暂无描述' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <!-- 空状态：更友好的提示 -->
                                <p class="text-center text-gray-500 py-6">暂无事件，请添加新事件！</p>
                            @endforelse
                            
                            <!-- 如果事件数量超过6个，显示"更多事件"按钮 -->
                            @if($events->count() > 6)
                                <div class="relative block items-baseline group">
                                    <!-- 更多事件按钮 -->
                                    <div class="w-full text-center mt-4">
                                        <a href="{{ route('events.index') }}" class="inline-block px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-base font-bold text-gray-500 hover:text-blue-200 transition-colors duration-300">
                                            查看更多事件
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>    
                <!-- 右侧：古籍展示 -->
                    <div class="rounded-lg overflow-hidden col-span-2">
                        <div class="p-2">
                            <h2 class="text-xl font-semibold mb-4">{{ __('app.ancient_texts') }}</h2>
                            @if(isset($homepageText) && $homepageText)
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
                                <div class="flex justify-center items-center mt-4 space-x-4">
                                    <!-- 上一页 -->
                                    <a href="{{ $currentPage > 1 ? route('home', ['page' => ($currentPage - 1)]) : '#' }}" 
                                       class="{{ $currentPage <= 1 ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800' }}">
                                        <x-ts-icon icon="tabler.arrow-left" class="size-5 inline" />
                                        上一页
                                    </a>

                                    <span class="text-gray-700 dark:text-gray-300">第 {{ $currentPage }} 页 / 共 {{ $totalPages }} 页</span>

                                    <!-- 下一页 -->
                                    <a href="{{ $currentPage < $totalPages ? route('home', ['page' => ($currentPage + 1)]) : '#' }}" 
                                       class="{{ $currentPage >= $totalPages ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800' }}">
                                        下一页
                                        <x-ts-icon icon="tabler.arrow-right" class="size-5 inline" />
                                    </a>
                                </div>
                                
                                <!-- <div class="text-center mt-4">
                                    <a href="{{ route('ancient-text.show', $homepageText->id) }}" class="text-blue-600 hover:text-blue-800">
                                        查看完整《{{ $homepageText->title }}》
                                    </a>
                                </div> -->
                            @else
                                <div class="text-center text-gray-500 py-6">
                                    暂无古籍内容，请先添加古籍！
                                </div>
                            @endif
                        </div>
                    </div>

                    
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
        </style>
    @endpush
</x-app-layout>
