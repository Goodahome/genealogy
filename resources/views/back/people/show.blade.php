@section('title')
    &vert; {{ __('person.profile') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('person.profile') }}
    </x-slot>

    <div class="w-full space-y-5 overflow-visible p-2"
         x-data="{
            panelHeight: null,
            syncPanelHeight() {
                const el = this.$refs.profileCard;
                if (! el) return;
                this.panelHeight = Math.round(el.getBoundingClientRect().height);
            },
            initPanels() {
                if (!Alpine.store('personPanels')) {
                    Alpine.store('personPanels', {
                        active: 'files',
                        select(key) { this.active = key; },
                        is(key) { return this.active === key; },
                    });
                }
                const allowed = ['files','photos','ancestors','descendants','history','datasheet','chart'];
                const panel = new URLSearchParams(window.location.search).get('panel');
                if (panel && allowed.includes(panel)) {
                    Alpine.store('personPanels').active = panel;
                } else if (! Alpine.store('personPanels').active) {
                    Alpine.store('personPanels').active = 'files';
                }
                this.$nextTick(() => {
                    this.syncPanelHeight();
                    if (this.$refs.profileCard && window.ResizeObserver) {
                        new ResizeObserver(() => this.syncPanelHeight()).observe(this.$refs.profileCard);
                    }
                });
            },
         }"
         x-init="initPanels()">
        @if (in_array(request('edit'), ['profile', 'contact', 'death'], true))
            <div class="hidden" x-data x-init="$nextTick(() => $modalOpen('edit-person-{{ request('edit') }}'))"></div>
        @endif
        <div class="flex flex-wrap items-start gap-5 lg:flex-nowrap">
            <div class="flex min-w-0 grow flex-col gap-5 md:max-w-max" x-ref="profileCard">
                <livewire:people.profile :person="$person" />
            </div>

            <div class="flex min-w-0 grow flex-col gap-5 md:max-w-max">
                <livewire:people.family :person="$person" />
                <livewire:people.partners :person="$person" />
                <livewire:people.children :person="$person" />
                <livewire:people.siblings :person="$person" />
            </div>

            {{-- 右侧固定卡：顶栏切换内容（含文件） --}}
            <aside class="zp-side-panel"
                   x-bind:style="panelHeight ? `height:${panelHeight}px` : null"
                   x-effect="$nextTick(() => syncPanelHeight())">
                <div class="zp-side-panel-shell zp-paper-card">
                    <div class="zp-side-panel-tabs" role="tablist" aria-label="{{ __('person.profile') }}">
                        <div class="zp-side-panel-tablist">
                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('files')"
                                x-bind:aria-selected="($store.personPanels.active === 'files').toString()"
                                x-bind:class="$store.personPanels.active === 'files' ? 'is-on' : ''"
                                title="{{ __('person.files') }}">
                            <x-ts-icon icon="tabler.files" class="size-3.5 shrink-0" />
                            <span>{{ __('person.files') }}</span>
                        </button>

                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('photos')"
                                x-bind:aria-selected="($store.personPanels.active === 'photos').toString()"
                                x-bind:class="$store.personPanels.active === 'photos' ? 'is-on' : ''"
                                title="{{ __('person.photos') }}">
                            <x-ts-icon icon="tabler.library-photo" class="size-3.5 shrink-0" />
                            <span>{{ __('person.photos') }}</span>
                        </button>

                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('ancestors')"
                                x-bind:aria-selected="($store.personPanels.active === 'ancestors').toString()"
                                x-bind:class="$store.personPanels.active === 'ancestors' ? 'is-on' : ''"
                                title="{{ __('person.ancestors') }}">
                            <x-ts-icon icon="tabler.binary-tree" class="size-3.5 shrink-0 rotate-180" />
                            <span>{{ __('person.ancestors') }}</span>
                        </button>

                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('descendants')"
                                x-bind:aria-selected="($store.personPanels.active === 'descendants').toString()"
                                x-bind:class="$store.personPanels.active === 'descendants' ? 'is-on' : ''"
                                title="{{ __('person.descendants') }}">
                            <x-ts-icon icon="tabler.binary-tree" class="size-3.5 shrink-0" />
                            <span>{{ __('person.descendants') }}</span>
                        </button>

                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('history')"
                                x-bind:aria-selected="($store.personPanels.active === 'history').toString()"
                                x-bind:class="$store.personPanels.active === 'history' ? 'is-on' : ''"
                                title="{{ __('app.history') }}">
                            <x-ts-icon icon="tabler.history" class="size-3.5 shrink-0" />
                            <span>{{ __('app.history') }}</span>
                        </button>

                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('datasheet')"
                                x-bind:aria-selected="($store.personPanels.active === 'datasheet').toString()"
                                x-bind:class="$store.personPanels.active === 'datasheet' ? 'is-on' : ''"
                                title="{{ __('app.datasheet') }}">
                            <x-ts-icon icon="tabler.pdf" class="size-3.5 shrink-0" />
                            <span>{{ __('app.datasheet') }}</span>
                        </button>

                        <button type="button"
                                class="zp-side-tab"
                                role="tab"
                                x-on:click="$store.personPanels.select('chart')"
                                x-bind:aria-selected="($store.personPanels.active === 'chart').toString()"
                                x-bind:class="$store.personPanels.active === 'chart' ? 'is-on' : ''"
                                title="{{ __('app.family_chart') }}">
                            <x-ts-icon icon="tabler.social" class="size-3.5 shrink-0" />
                            <span>{{ __('app.family_chart') }}</span>
                        </button>
                        </div>
                    </div>

                    <div class="zp-side-panel-body">
                        <div class="zp-embed-flat" x-show="$store.personPanels.active === 'files'" x-cloak>
                            <livewire:people.files :person="$person" />
                        </div>

                        <div class="zp-embed-flat zp-embed-photos" x-show="$store.personPanels.active === 'photos'" x-cloak>
                            <livewire:people.album :person="$person" />
                        </div>

                        <div class="zp-embed-flat" x-show="$store.personPanels.active === 'ancestors'" x-cloak>
                            <livewire:people.ancestors :person="$person" />
                        </div>

                        <div class="zp-embed-flat" x-show="$store.personPanels.active === 'descendants'" x-cloak>
                            <livewire:people.descendants :person="$person" />
                        </div>

                        <div class="zp-embed-flat" x-show="$store.personPanels.active === 'history'" x-cloak>
                            <livewire:people.history :person="$person" />
                        </div>

                        <div class="zp-embed-table" x-show="$store.personPanels.active === 'datasheet'" x-cloak>
                            <livewire:people.datasheet :person="$person" />
                        </div>

                        <div class="zp-embed-table" x-show="$store.personPanels.active === 'chart'" x-cloak>
                            <livewire:people.family-chart :person="$person" :compact="true" />
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    @if (auth()->user()->hasPermission('person:update'))
        <x-ts-modal id="edit-person-profile" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.id" class="inline-block mr-1" />{{ __('person.edit_profile') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.edit.profile :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>

        <x-ts-modal id="edit-person-contact" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.address-book" class="inline-block mr-1" />{{ __('person.edit_contact') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.edit.contact :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>

        <x-ts-modal id="edit-person-death" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.grave-2" class="inline-block mr-1" />{{ __('person.edit_death') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.edit.death :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>

        <x-ts-modal id="add-person-father" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1" />{{ __('person.add_father') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.add.father :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>

        <x-ts-modal id="add-person-mother" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1" />{{ __('person.add_mother') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.add.mother :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>

        <x-ts-modal id="edit-person-family" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.edit" class="inline-block mr-1" />{{ __('person.edit_family') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.edit.family :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>
    @endif

    @if (auth()->user()->hasPermission('person:create'))
        <x-ts-modal id="add-person-child" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1" />{{ __('person.add_child') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.add.child :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>
    @endif

    @if (auth()->user()->hasPermission('couple:create'))
        <x-ts-modal id="add-person-partner" size="4xl" blur center persistent z-index="z-[100]">
            <x-slot:title>
                <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1" />{{ __('person.add_relationship') }}
            </x-slot:title>
            <div class="zp-edit-modal-panel zp-edit-dialog-content">
                <livewire:people.add.partner :person="$person" :as-modal="true" />
            </div>
        </x-ts-modal>
    @endif

    @if (auth()->user()->hasPermission('couple:update'))
        @foreach ($person->couples as $couple)
            <x-ts-modal id="edit-person-partner-{{ $couple->id }}" size="4xl" blur center persistent z-index="z-[100]">
                <x-slot:title>
                    <x-ts-icon icon="tabler.user-edit" class="inline-block mr-1" />{{ __('person.edit_relationship') }}
                </x-slot:title>
                <div class="zp-edit-modal-panel zp-edit-dialog-content">
                    <livewire:people.edit.partner :person="$person" :couple="$couple" :as-modal="true" :key="'edit-person-partner-' . $couple->id" />
                </div>
            </x-ts-modal>
        @endforeach
    @endif
</x-app-layout>
