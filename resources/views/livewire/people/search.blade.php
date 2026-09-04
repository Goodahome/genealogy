<div class="w-full"
     x-data="{
        barH: 0,
        headerBottom: 92,
        sync() {
            const header = document.querySelector('header.sticky, header');
            // 用视口底部坐标贴齐面包屑，再上叠 2px 封死缝隙
            const bottom = header ? header.getBoundingClientRect().bottom : 92;
            this.headerBottom = Math.max(0, Math.round(bottom) - 2);
            this.$nextTick(() => {
                this.barH = this.$refs.searchBar ? this.$refs.searchBar.offsetHeight : 0;
            });
        },
     }"
     x-init="
        if (!Alpine.store('peopleSearch')) {
            Alpine.store('peopleSearch', { expanded: window.innerWidth >= 768 });
        }
        sync();
        if ($refs.searchBar) {
            new ResizeObserver(() => sync()).observe($refs.searchBar);
        }
        const header = document.querySelector('header.sticky, header');
        if (header) {
            new ResizeObserver(() => sync()).observe(header);
        }
        $watch(() => $store.peopleSearch.expanded, () => setTimeout(() => sync(), 220));
     "
     x-on:resize.window="sync()">
    {{-- 固定搜索条：贴紧顶栏下沿，上叠 2px 避免透出列表 --}}
    <div x-ref="searchBar"
         class="zp-search-fixed zp-sticky-surface fixed left-0 right-0 z-20 w-full p-2 pt-2"
         x-bind:style="'top:' + headerBottom + 'px'">
        <div class="flex w-full max-w-none flex-col rounded-sm p-2 dark:text-neutral-200 bg-white dark:bg-neutral-700 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)]">
            {{-- 点击此行展开/折叠搜索区 --}}
            <div class="mb-2 flex w-full cursor-pointer flex-wrap items-center gap-2 text-lg"
                 role="button"
                 tabindex="0"
                 title="{{ __('app.search') }}"
                 x-on:click="$store.peopleSearch.expanded = !$store.peopleSearch.expanded"
                 x-on:keydown.enter.prevent="$store.peopleSearch.expanded = !$store.peopleSearch.expanded">
                <div class="min-w-0 flex-1 grow text-end text-base sm:text-lg">
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
                <span class="inline-flex shrink-0 transition-transform duration-200"
                      x-bind:class="$store.peopleSearch.expanded ? 'rotate-180' : ''">
                    <x-ts-icon icon="tabler.chevron-down" class="size-5" />
                </span>
            </div>

            {{-- 可折叠的搜索区域 --}}
            <div class="w-full"
                 x-show="$store.peopleSearch.expanded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1">
                {{-- search + perpage + add + help --}}
                <div class="flex w-full flex-wrap items-end gap-2">
                    <div class="min-w-0 w-full flex-1 grow">
                        <x-ts-input wire:model.live.debounce.500ms="search" icon="tabler.search" placeholder="{{ __('app.people_search_tip') }}" autofocus clearable />
                    </div>

                    @if (count($people) > 0)
                        <div class="w-[5.5rem] max-w-full shrink-0 self-start">
                            <x-ts-select.styled wire:model.live="perpage" name="perpage" id="perpage" :options="$options" select="label:label|value:value" required />
                        </div>
                    @endif

                    @if (auth()->user()->hasPermission('person:create'))
                        <button type="button"
                                x-on:click="$modalOpen('add-person')"
                                title="{{ __('person.add_person') }}"
                                aria-label="{{ __('person.add_person') }}"
                                class="zp-search-action shrink-0 self-start">
                            <x-ts-icon icon="tabler.user-plus" class="size-5" />
                        </button>
                    @endif

                    <button type="button"
                            title="{{ __('app.help') }}"
                            aria-label="{{ __('app.help') }}"
                            x-on:click="$modalOpen('search-help')"
                            class="zp-search-action shrink-0 self-start">
                        <x-ts-icon icon="tabler.help" class="size-5" />
                    </button>
                </div>

                {{-- pagination --}}
                @if (count($people) > 0)
                    <div class="mt-2 flex w-full flex-wrap items-center justify-end gap-2">
                        {{ $people->links('livewire/pagination/tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 占位：高度与固定搜索条一致，避免列表被挡住 --}}
    <div class="w-full shrink-0" x-bind:style="'height:' + barH + 'px'" aria-hidden="true"></div>

    @if (count($people) > 0)
        {{-- 墙面：独立墙面背景 + 散落 A4 纸张（非整齐 Card Grid） --}}
        <div class="zp-paper-wall">
            @foreach ($people as $person)
                <div class="zp-paper-wall__cell" wire:key="person-cell-{{ $person->id }}">
                    {{-- 纯 Blade 卡片，避免每张再挂一个 Livewire 实例 --}}
                    @include('livewire.people.person', ['person' => $person])
                </div>
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

    {{-- search help modal：居中 + 提高层级，避免被 sticky 顶栏盖住 --}}
    <x-ts-modal id="search-help" size="6xl" blur center z-index="z-[100]">
        <x-slot:title>
            <x-ts-icon icon="tabler.help" class="inline-block" />{{ __('app.help') }}
        </x-slot:title>

        <p>{!! __('app.people_search_help_1') !!}</p><br />
        <p>{!! __('app.people_search_help_2') !!}</p><br />
        <p>{!! __('app.people_search_help_3') !!}</p>
    </x-ts-modal>

    @if (auth()->user()->hasPermission('person:create'))
        <x-ts-modal id="add-person" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1" />{{ __('person.add_person') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.add.person :as-modal="true" />
            </div>
        </x-ts-modal>
    @endif
</div>
