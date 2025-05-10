@section('title')
    | {{ __('app.home') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.home') }}
    </x-slot>

    <div class="w-full p-4 space-y-5">
        <div class="pb-60 dark:text-neutral-200">
            <div class="flex flex-col w-full pt-6 sm:pt-0">
                <!-- 两栏布局 -->
                <div class="w-full mt-2 grid grid-cols-1 lg:grid-cols-2 gap-2">
                    <!-- 左侧：时间线事件 -->
                    <div class="rounded-lg p-2 max-w-3xl mx-auto">
                        <!-- 头部区域：更现代的标题和按钮 -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">{{ __('app.events') }}</h2>
                            <a href="{{ route('events.create') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-300 ease-in-out">
                                添加事件
                            </a>
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
                            <!-- 连续的时间线 -->
                            <div class="absolute left-[25%] w-1 h-full bg-gray-600 z-10"></div>
                            <!-- 使事件时间按最新的日期排序 -->
                            @forelse ($events->sortByDesc('date') as $event)
                                <!-- 单个事件：卡片样式，带悬停效果 -->
                                <div class="relative block items-baseline group hover:shadow-xl transition-shadow duration-300 bg-gray-100 rounded-md p-4">
                                    <!-- 日期部分：更清晰的排版 -->
                                    <div class="w-1/3 pr-8 text-left">
                                        <span class="text-sm font-semibold text-gray-500">
                                            {{ $event->date->format('Y-m-d') }}
                                        </span>
                                    </div>

                                    <!-- 时间线节点 -->
                                    <div class="absolute left-[25%] w-4 h-4 bg-gray-600 rounded-full mt-0 transform translate-x-0    z-20"></div>

                                    <!-- 事件内容：卡片化，带描述截断 -->
                                    <div class="w-2/3 pl-10">
                                        <h3 class="text-base font-bold text-gray-500 group-hover:text-blue-200 transition-colors duration-300">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-sm tracking-widest text-gray-600 line-clamp-2">
                                            {{ $event->description ?? '暂无描述' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <!-- 空状态：更友好的提示 -->
                                <p class="text-center text-gray-500 py-6">暂无事件，请添加新事件！</p>
                            @endforelse
                        </div>
                    </div>    
                <!-- 右侧：PDF 查看器 -->
                    <div class="rounded-sm overflow-hidden">
                        <div class="p-2">
                            <!-- <h2 class="text-xl font-semibold mb-4">{{ __('app.pdf_viewer') }}</h2> -->
                            <div class="pdf-container">
                                <canvas id="pdf-canvas"></canvas>
                                <div class="pdf-controls">
                                <button id="prev" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">上一页</button>
                                <span id="page-num" class="text-gray-600"></span> / <span id="page-count" class="text-gray-600"></span>
                                <button id="next" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">下一页</button>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .pdf-container {
                width: 100%;
                margin: 0 auto;
                text-align: center;
            }
            .pdf-controls {
                margin: 10px 0;
                padding: 5px;
                background-color: #f5f5f5;
                border-radius: 5px;
                display: flex;
                justify-content: center;
                gap: 10px;
                align-items: center;
            }
            #pdf-canvas {
                border: 1px solid #ddd;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin: 0 auto;
                display: block;
                max-width: 100%;
                height: 80vh;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js';
            const pdfUrl = '{{ $pdfUrl }}';
            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');
            const pageNum = document.getElementById('page-num');
            const pageCount = document.getElementById('page-count');
            const prevBtn = document.getElementById('prev');
            const nextBtn = document.getElementById('next');
            let currentPage = 1;
            let scale = 1.5;
            let pdfDoc = null;
            function renderPage(pageNum) {
                pdfDoc.getPage(pageNum).then(function(page) {
                    const viewport = page.getViewport({ scale: scale });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    page.render(renderContext);
                });
                pageNum.textContent = currentPage;
            }
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                pdfDoc = pdf;
                pageCount.textContent = pdf.numPages;
                renderPage(currentPage);
                prevBtn.addEventListener('click', function() {
                    if (currentPage <= 1) return;
                    currentPage--;
                    renderPage(currentPage);
                });
                nextBtn.addEventListener('click', function() {
                    if (currentPage >= pdfDoc.numPages) return;
                    currentPage++;
                    renderPage(currentPage);
                });
            }).catch(function(error) {
                console.error('PDF加载失败:', error);
                canvas.insertAdjacentHTML('afterend', '<div class="alert alert-danger">PDF加载失败，请检查文件路径是否正确。</div>');
            });
        </script>
    @endpush
</x-app-layout>