<x-app-layout>
    <x-slot name="heading">事件列表</x-slot>

    <div class="w-full p-4">
        <div class="rounded-lg p-6 max-w-4xl mx-auto">
            <!-- 头部区域：更现代的标题和按钮 -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">所有事件</h2>
                @if (auth()->user()->hasPermission('event:create'))
                    <a href="{{ route('events.create') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-300 ease-in-out">
                        添加事件
                    </a>
                @endif
            </div>

            <!-- 成功提示：更柔和的颜色和动画 -->
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6 animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 事件列表容器 -->
            <div class="space-y-6 relative">
                <!-- 连续的时间线 -->
                <div class="absolute left-[30%] w-1 h-full bg-gray-600 z-10"></div>
                <!-- 使事件时间按最新的日期排序 -->
                @forelse ($events->sortByDesc('date') as $event)
                    <!-- 单个事件：卡片样式，带悬停效果 -->
                    <div class="relative block items-baseline group hover:shadow-xl transition-shadow duration-300 bg-gray-100 rounded-md p-5">
                        <!-- 日期部分：更清晰的排版 -->
                        <div class="w-1/3 pr-8 text-left">
                            <span class="text-sm font-semibold text-gray-500 p-10">
                                {{ $event->date->format('Y-m-d') }}
                            </span>
                        </div>

                        <!-- 时间线节点 -->
                        <div class="absolute left-[25%] w-4 h-4 bg-gray-600 rounded-full mt-0 transform translate-x-2"></div>

                        <!-- 事件内容：卡片化，带描述截断 -->
                        <div class="w-2/3 pl-10">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-bold text-gray-500 group-hover:text-blue-200 transition-colors duration-300">
                                        {{ $event->title }}
                                    </h3>
                                    <p class="text-sm tracking-widest text-gray-600 line-clamp-2">
                                        {{ $event->description ?? '暂无描述' }}
                                    </p>
                                </div>
                                
                                @if (auth()->user()->hasPermission('event:delete'))
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('确定要删除此事件吗？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors duration-300">
                                            <x-ts-icon icon="tabler.trash" class="size-5" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- 空状态：更友好的提示 -->
                    <p class="text-center text-gray-500 py-6">暂无事件，请添加新事件！</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
        </style>
    @endpush
</x-app-layout>