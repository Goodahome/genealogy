@section('title')
    &vert; {{ __('app.ancient_texts') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('app.ancient_texts') }}
    </x-slot>

    <div class="p-2 overflow-x-auto max-w-7xl grow dark:text-neutral-200">
        <div class="flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
            <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
                <div class="flex flex-wrap items-start justify-center gap-2">
                    <div class="flex-1 grow max-w-full min-w-max">
                        {{ __('app.ancient_texts') }}
                    </div>

                    <div class="flex-1 grow max-w-full text-end min-w-max">
                        <x-ts-button href="{{ route('ancient-text.create') }}" color="emerald" class="text-sm text-white">
                            <x-ts-icon icon="tabler.circle-plus" class="size-5" />
                            {{ __('app.create') }}
                        </x-ts-button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                @if($texts->isEmpty())
                    <p class="text-center py-4">{{ __('app.no_records_found') }}</p>
                @else
                    <table class="min-w-full text-left">
                        <thead class="border-b dark:border-neutral-500">
                            <tr>
                                <th scope="col" class="p-2">{{ __('app.title') }}</th>
                                <th scope="col" class="p-2">{{ __('app.volume') }}</th>
                                <th scope="col" class="p-2">{{ __('app.page') }}</th>
                                <th scope="col" class="p-2 text-end">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($texts as $text)
                                <tr class="border-b dark:border-neutral-500">
                                    <td class="p-2">{{ $text->title }}</td>
                                    <td class="p-2">{{ $text->volume }}</td>
                                    <td class="p-2">{{ $text->page }}</td>
                                    <td class="p-2 text-end">
                                        <x-ts-button href="{{ route('ancient-text.show', $text->id) }}" color="primary" class="text-sm">
                                            <x-ts-icon icon="tabler.eye" class="size-5" />
                                        </x-ts-button>
                                        <x-ts-button href="{{ route('ancient-text.edit', $text->id) }}" color="yellow" class="text-sm">
                                            <x-ts-icon icon="tabler.edit" class="size-5" />
                                        </x-ts-button>
                                        <form action="{{ route('ancient-text.destroy', $text->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-ts-button type="submit" color="red" class="text-sm" onclick="return confirm('{{ __('app.confirm_delete') }}')">
                                                <x-ts-icon icon="tabler.trash" class="size-5" />
                                            </x-ts-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $texts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>