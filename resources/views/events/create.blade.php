<x-app-layout>
    <x-slot name="heading">创建事件</x-slot>

    <div class="w _

full p-4">
        <div class="bg-white rounded-sm shadow-md p-6 max-w-2xl mx-auto">
            <h2 class="text-xl font-semibold mb-4">添加新事件</h2>
            <form action="{{ route('events.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">日期</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">标题</label>
                    <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                    <textarea name="description" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        </div>
    </div>
</x-app-layout>