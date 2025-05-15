<div>
    {{-- search box section --}}
    <div class="p-2 sticky top-[6.5rem] z-20 bg-gray-100 dark:bg-gray-900">
        <div class="p-2 flex flex-col rounded-sm dark:text-neutral-200 bg-white dark:bg-neutral-700 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)]">
            {{-- header with toggle button for mobile --}}
            <div class="flex flex-wrap mb-2 text-lg items-center md:cursor-default cursor-pointer" 
                 x-data="{}" 
                 x-on:click="$dispatch('toggle-search')"
                 x-bind:class="{'hover:bg-gray-100 dark:hover:bg-gray-600': window.innerWidth < 768}">
                
                {{-- 添加折叠/展开按钮指示器 --}}
                <div class="flex-1 grow max-w-full text-left" x-on:click.stop>
                    @if (auth()->user()->hasPermission('person:create'))
                        {{-- add button --}}
                        <x-ts-button href="/people/add" color="emerald" class="text-sm">
                            <x-ts-icon icon="tabler.user-plus" class="size-5" />
                            {{ __('person.add_person') }}
                        </x-ts-button>
                    @endif
                </div>

                <div class="flex-1 grow max-w-full text-end">
                    @if ($search)
                        {!! __('app.people_found', [
                            'found' => $people->total(),
                            'total' => $people_db,
                            'scope' => auth()->user()->is_developer ? strtoupper(__('team.all_teams')) : auth()->user()->currentTeam->name,
                            'keyword' => $search,
                        ]) !!}
                    @else
                        {!! __('app.people_available', [
                            'total' => $people_db,
                            'scope' => auth()->user()->is_developer ? strtoupper(__('team.all_teams')) : auth()->user()->currentTeam->name,
                        ]) !!}
                    @endif
                </div>
            </div>

            {{-- 可折叠的搜索区域 --}}
            <div x-data="{}" 
                 x-init="$store.searchExpanded = window.innerWidth >= 768" 
                 x-on:toggle-search.window="$store.searchExpanded = !$store.searchExpanded"
                 x-show="$store.searchExpanded"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="md:block">
                 <div x-data="{}" x-init="
                        if (!window.Alpine.store('search')) {
                            window.Alpine.store('search', {
                                expanded: window.innerWidth >= 768
                            });
                        }
                        $store.searchExpanded = window.Alpine.store('search').expanded;
                    ">
                
                {{-- search box --}}
                <div class="flex flex-wrap items-start gap-2">
                    <div class="flex-1 grow w-full">
                        <x-ts-input wire:model.live.debounce.500ms="search" icon="tabler.search" hint="{{ __('app.people_search_tip') }}" placeholder="{{ __('app.people_search_placeholder') }}" autofocus clearable />
                    </div>

                    <div class="flex-1 max-w-max">
                        <x-ts-button color="cyan" title="{{ __('app.help') }}" x-on:click="$modalOpen('search-help')" class="pb-1! p-1.5! mt-1! text-sm text-white">
                            <x-ts-icon icon="tabler.help" />
                        </x-ts-button>
                    </div>
                </div>

                {{-- footer : perpage and pagination --}}
                @if (count($people) > 0)
                    <div class="flex flex-wrap items-center justify-center gap-2 mt-2">
                        <div class="flex-1 grow min-w-max max-w-36">
                            <x-ts-select.styled wire:model.live="perpage" name="perpage" id="perpage" :options="$options" select="label:label|value:value" required />
                        </div>

                        <div class="flex-1 grow max-w-full min-w-max text-end">
                            {{ $people->links('livewire/pagination/tailwind') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (count($people) > 0)
        {{-- people grid --}}
        <div class="p-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2">
            @foreach ($people as $person)
                <livewire:people.person :person="$person" :key="$person->id" />
            @endforeach
        </div>
    @else
        {{-- carousel --}}
        <div class="p-5 mx-auto text-center max-w-6xl">
            <x-ts-carousel :images="[
                ['src' => url('img/carousel/genealogy-research-001.webp'), 'alt' => '1'],
                ['src' => url('img/carousel/genealogy-research-002.webp'), 'alt' => '2'],
                ['src' => url('img/carousel/genealogy-research-003.webp'), 'alt' => '3'],
                ['src' => url('img/carousel/genealogy-research-004.webp'), 'alt' => '4'],
            ]" autoplay stop-on-hover interval="10" />
        </div>
    @endif

    {{-- search help modal --}}
    <x-ts-modal id="search-help" size="6xl" blur>
        <x-slot:title>
            <x-ts-icon icon="tabler.help" class="inline-block" />{{ __('app.help') }}
        </x-slot:title>

        <p>{!! __('app.people_search_help_1') !!}</p><br />
        <p>{!! __('app.people_search_help_2') !!}</p><br />
        <p>{!! __('app.people_search_help_3') !!}</p>
    </x-ts-modal>
</div>
