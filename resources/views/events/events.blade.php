<x-app-layout>
    <x-slot name="heading">事件列表</x-slot>

    <div class="w-full p-4">
        <div class="bg-white rounded-sm shadow-md p-6 max-w-4xl mx-auto">
            <h2 class="text-xl font-semibold mb-4">所有事件</h2>
            <a href="{{ route('events.create') }}" class="btn btn-sm btn-primary mb-4">添加事件</a>
            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif
            <div class="space-y-4">
                @forelse ($events as $event)
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold">{{ $event->title }}</h3>
                        <p class="text-gray-600">{{ $event->date->format('Y-m-d') }}</p>
                        <p class="text-gray-600">{{ $event->description ?? '暂无描述' }}</p>
                    </div>
                @empty
                    <p class="text-gray-600">暂无事件</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>