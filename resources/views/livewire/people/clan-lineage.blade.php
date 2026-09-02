<div>
    <div
        class="min-w-80 print:break-before-page flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
        <div class="p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
            <div class="flex flex-wrap items-start justify-center gap-2">
                <div class="flex-1 grow max-w-full min-w-max">
                    {{ __('app.clan_lineage') }}

                    <div class="inline-flex ml-2 border rounded-sm" role="group">
                        @if ($count === $count_min)
                            <x-ts-button square xs color="red" class="border-0 rounded-l" wire:click="decrement" disabled>
                                <x-ts-icon icon="tabler.minus" class="size-5" />
                            </x-ts-button>
                        @else
                            <x-ts-button square xs color="secondary" class="border-0 rounded-l" wire:click="decrement">
                                <x-ts-icon icon="tabler.minus" class="size-5" />
                            </x-ts-button>
                        @endif

                        <div class="w-16 text-center">{{ $count }}</div>

                        @if ($count === $count_max)
                            <x-ts-button square xs color="red" class="border-0 rounded-r" wire:click="increment" disabled>
                                <x-ts-icon icon="tabler.plus" class="size-5" />
                            </x-ts-button>
                        @else
                            <x-ts-button square xs color="secondary" class="border-0 rounded-r" wire:click="increment">
                                <x-ts-icon icon="tabler.plus" class="size-5" />
                            </x-ts-button>
                        @endif
                    </div>
                </div>

                <div class="flex-1 grow max-w-full min-w-max text-end">
                    <x-ts-icon icon="tabler.binary-tree" class="inline-block" />
                </div>
            </div>
        </div>

        @if ($roots->isEmpty())
            <p class="p-4 text-center">{{ __('app.no_records_found') }}</p>
        @else
            <div
                x-data="{
                    panning: false,
                    startX: 0,
                    startY: 0,
                    scrollLeft: 0,
                    scrollTop: 0,
                    shouldPan(event) {
                        return ! event.target.closest('a, button, [role=button], .tree-affinal-trigger, .tree-affinal-popover__close');
                    },
                    startPan(event) {
                        if (! this.shouldPan(event)) {
                            return;
                        }

                        this.panning = true;
                        this.startX = event.clientX ?? event.touches?.[0]?.clientX ?? 0;
                        this.startY = event.clientY ?? event.touches?.[0]?.clientY ?? 0;
                        this.scrollLeft = this.$refs.viewport.scrollLeft;
                        this.scrollTop = this.$refs.viewport.scrollTop;
                        this.$refs.viewport.classList.add('is-panning');
                    },
                    movePan(event) {
                        if (! this.panning) {
                            return;
                        }

                        event.preventDefault();

                        const clientX = event.clientX ?? event.touches?.[0]?.clientX ?? 0;
                        const clientY = event.clientY ?? event.touches?.[0]?.clientY ?? 0;

                        this.$refs.viewport.scrollLeft = this.scrollLeft - (clientX - this.startX);
                        this.$refs.viewport.scrollTop = this.scrollTop - (clientY - this.startY);
                    },
                    endPan() {
                        this.panning = false;

                        if (this.$refs.viewport) {
                            this.$refs.viewport.classList.remove('is-panning');
                        }
                    },
                }"
                @mouseup.window="endPan()"
                @mousemove.window="movePan($event)"
                @touchend.window="endPan()"
                @touchmove.window="movePan($event)"
            >
                <div
                    x-ref="viewport"
                    class="clan-lineage-viewport p-4"
                    @mousedown="startPan($event)"
                    @touchstart="startPan($event)"
                >
                    <div class="tree-clan-forest">
                        @foreach ($roots as $root)
                            <div class="tree-clan-forest__tree">
                                <ul class="tree-ltr tree-clan">
                                    <x-tree-node.clan-descendants
                                        :person="$root"
                                        :descendants="$descendants"
                                        :spouses-map="$spousesMap"
                                        :main-clan-ids="$mainClanIds"
                                        :affinal-relatives-by-person="$affinalRelativesByPerson"
                                        :level_max="$count"
                                    />
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <link href="{{ asset('css/tree-ltr.css') }}" rel="stylesheet">
    <link href="{{ asset('css/clan-lineage.css') }}" rel="stylesheet">

    <script>
        window.clanAffinalPopover = function () {
            return {
                open: false,
                spacerW: 0,
                spacerH: 0,
                toggle() {
                    if (this.open) {
                        this.close();
                    } else {
                        this.open = true;
                        this.$nextTick(() => {
                            requestAnimationFrame(() => this.syncSpacer());
                        });
                    }
                },
                close() {
                    this.open = false;
                    this.spacerW = 0;
                    this.spacerH = 0;
                },
                syncSpacer() {
                    const popover = this.$refs.popover;
                    const main = this.$el.querySelector('.tree-couple-wrap__main');

                    if (!popover || !main) {
                        return;
                    }

                    const gap = 12;
                    const popW = Math.ceil(popover.offsetWidth || popover.scrollWidth || 0);
                    const popH = Math.ceil(popover.offsetHeight || popover.scrollHeight || 0);
                    const mainH = Math.ceil(main.offsetHeight || 0);

                    this.spacerW = popW + gap;
                    this.spacerH = Math.max(mainH, popH);

                    this.$nextTick(() => {
                        const viewport = this.$el.closest('.clan-lineage-viewport');
                        if (!viewport) {
                            return;
                        }

                        const popRect = popover.getBoundingClientRect();
                        const viewRect = viewport.getBoundingClientRect();
                        const pad = 24;

                        if (popRect.right > viewRect.right - pad) {
                            viewport.scrollLeft += popRect.right - viewRect.right + pad;
                        }

                        if (popRect.bottom > viewRect.bottom - pad) {
                            viewport.scrollTop += popRect.bottom - viewRect.bottom + pad;
                        }

                        if (popRect.top < viewRect.top + pad) {
                            viewport.scrollTop -= viewRect.top + pad - popRect.top;
                        }
                    });
                },
            };
        };
    </script>
</div>
