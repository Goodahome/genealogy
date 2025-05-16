@section('title')
    | {{ __('app.edit_ancient_text') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.edit_ancient_text') }}
    </x-slot>

    <div class="w-full p-4">
        <div class="flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
            <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
                <div class="flex flex-wrap items-start justify-center gap-2">
                    <div class="flex-1 grow max-w-full min-w-max">
                        {{ __('app.edit_ancient_text') }}: {{ $text->title }}
                    </div>

                    <div class="flex-1 grow max-w-full text-end min-w-max">
                        <x-ts-button href="{{ route('ancient-text.show', $text->id) }}" color="primary" class="text-sm">
                            <x-ts-icon icon="tabler.arrow-left" class="size-5" />
                            {{ __('app.back') }}
                        </x-ts-button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <form method="POST" action="{{ route('ancient-text.update', $text->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <x-ts-input name="title" label="{{ __('app.title') }}" value="{{ $text->title }}" required />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-ts-input type="number" name="volume" label="{{ __('app.volume') }}" value="{{ $text->volume }}" min="1" required />
                        </div>
                        <div>
                            <x-ts-input type="number" name="page" label="{{ __('app.page') }}" value="{{ $text->page }}" min="1" required />
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <x-ts-checkbox name="show_on_homepage" label="在首页展示此古籍" :checked="$text->show_on_homepage" />
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('app.content') }}</label>
                        <textarea id="content" name="content" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>{{ $text->content }}</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <div class="preview-container">
                            <h3 class="text-lg font-medium mb-2">{{ __('app.preview') }}</h3>
                            <div class="vertical-text-container" id="preview-text">
                                <!-- 预览内容将由 JavaScript 动态填充 -->
                            </div>
                            <!-- 分页控件 -->
                            <div class="flex justify-center items-center mt-4 space-x-4">
                                <button type="button" id="preview-prev-page" class="text-gray-400 pointer-events-none">
                                    <x-ts-icon icon="tabler.arrow-left" class="size-5 inline" />
                                    上一页
                                </button>
                                <span id="preview-page-info" class="text-gray-700 dark:text-gray-300">第 1 页 / 共 1 页</span>
                                <button type="button" id="preview-next-page" class="text-gray-400 pointer-events-none">
                                    下一页
                                    <x-ts-icon icon="tabler.arrow-right" class="size-5 inline" />
                                </button>
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

    @push('styles')
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
                font-size: 18px;
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
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const contentTextarea = document.getElementById('content');
                const previewContainer = document.getElementById('preview-text');
                const prevPageButton = document.getElementById('preview-prev-page');
                const nextPageButton = document.getElementById('preview-next-page');
                const pageInfo = document.getElementById('preview-page-info');

                let paginatedContent = [];
                let currentPage = 1;
                let totalPages = 1;
                let debounceTimer;

                // 初始预览
                updatePreview();

                // 监听内容变化（使用防抖）
                contentTextarea.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updatePreview, 500); // 500ms 延迟
                });

                // 上一页按钮
                prevPageButton.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderPage();
                    }
                });

                // 下一页按钮
                nextPageButton.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderPage();
                    }
                });

                function updatePreview() {
                    const content = contentTextarea.value;

                    // 显示加载状态
                    previewContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">加载中...</div>';

                    // 发送 AJAX 请求到后端预览端点
                    fetch('{{ route('ancient-text.preview') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ content: content }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        paginatedContent = data.paginatedContent || [];
                        totalPages = paginatedContent.length || 1;
                        currentPage = 1; // 重置到第一页
                        renderPage();
                    })
                    .catch(error => {
                        console.error('Error fetching preview:', error);
                        previewContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">预览失败</div>';
                    });
                }

                function renderPage() {
                    // 清空预览容器
                    previewContainer.innerHTML = '';

                    // 获取当前页内容
                    const pageContent = paginatedContent[currentPage - 1] || [];
                    if (pageContent.length === 0) {
                        previewContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">{{ __('app.no_content') }}</div>';
                    } else {
                        pageContent.forEach(line => {
                            const textDiv = document.createElement('div');
                            textDiv.className = 'vertical-text' + (line.isFirstLineOfParagraph ? ' first-line-indent' : '');
                            textDiv.textContent = line.content;
                            previewContainer.appendChild(textDiv);
                        });
                    }

                    // 更新分页信息
                    pageInfo.textContent = `第 ${currentPage} 页 / 共 ${totalPages} 页`;

                    // 更新按钮状态
                    prevPageButton.className = currentPage <= 1 ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800';
                    nextPageButton.className = currentPage >= totalPages ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800';
                }
            });
        </script>
    @endpush
</x-app-layout>