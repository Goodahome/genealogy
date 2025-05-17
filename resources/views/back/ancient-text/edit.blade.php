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
                            <x-ts-input type="text" name="volume" label="{{ __('app.volume') }}" value="{{ $text->volume }}" required placeholder="请输入卷号（例如：壹、贰）" />
                        </div>
                        <!-- 移除 page 输入框 -->
                    </div>
                    
                    <div class="mb-4">
                    <x-ts-checkbox name="show_on_homepage" label="在首页展示此古籍" :checked="$text->show_on_homepage ? true : false" value="1" />
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
            .vertical-text-wrapper {
                position: relative;
                display: inline-block;
                min-height: 32px; /* 确保空行有高度 */
                width: 32px; /* 固定宽度，确保垂直对齐 */
            }

            .vertical-text-container {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-start;
                align-items: flex-start;
                width: 600px;
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

            .vertical-text.first-line-indent {
                padding-top: 2em;
            }

            .vertical-text.first-line-indent::before {
                content: '';
                display: block;
                height: 2em;
            }

            .volume-page-footer {
                writing-mode: vertical-rl;
                text-orientation: upright;
                font-family: 'SimSun', 'Noto Serif CJK SC', serif;
                font-size: 14px;
                color: #666;
                position: absolute;
                bottom: 0;
                left: 50%;
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

                updatePreview();

                contentTextarea.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updatePreview, 500);
                });

                prevPageButton.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderPage();
                    }
                });

                nextPageButton.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderPage();
                    }
                });

                function updatePreview() {
                    const content = contentTextarea.value;
                    previewContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">加载中...</div>';

                    const volumeInput = document.querySelector('input[name="volume"]').value;

                    fetch('{{ route('ancient-text.preview') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ content: content, volume: volumeInput }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        paginatedContent = data.paginatedContent || [];
                        totalPages = paginatedContent.length || 1;
                        currentPage = 1;
                        renderPage();
                    })
                    .catch(error => {
                        console.error('Error fetching preview:', error);
                        previewContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">预览失败</div>';
                    });
                }

                function renderPage() {
                    previewContainer.innerHTML = '';

                    const pageContent = paginatedContent[currentPage - 1] || [];
                    if (pageContent.length === 0) {
                        previewContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">{{ __('app.no_content') }}</div>';
                    } else {
                        // 渲染所有 17 行
                        for (let index = 0; index < 17; index++) {
                            const line = pageContent[index] || { content: '', isFirstLineOfParagraph: false };
                            const wrapperDiv = document.createElement('div');
                            wrapperDiv.className = 'vertical-text-wrapper';

                            const textDiv = document.createElement('div');
                            textDiv.className = 'vertical-text' + (line.isFirstLineOfParagraph ? ' first-line-indent' : '');
                            textDiv.textContent = line.content || html_entity_decode('&nbsp;'); // 使用不间断空格占位
                            wrapperDiv.appendChild(textDiv);

                            if (line.footer) {
                                const footerDiv = document.createElement('div');
                                footerDiv.className = 'volume-page-footer';
                                footerDiv.textContent = line.footer;
                                wrapperDiv.appendChild(footerDiv);
                            }

                            previewContainer.appendChild(wrapperDiv);
                        }

                        console.log('Page content length:', pageContent.length);
                    }

                    pageInfo.textContent = `第 ${currentPage} 页 / 共 ${totalPages} 页`;

                    prevPageButton.className = currentPage <= 1 ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800';
                    nextPageButton.className = currentPage >= totalPages ? 'text-gray-400 pointer-events-none' : 'text-blue-600 hover:text-blue-800';
                }
            });
        </script>
    @endpush
</x-app-layout>