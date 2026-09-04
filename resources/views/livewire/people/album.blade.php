<div class="zp-album">
    @if ($canManage)
        <div class="zp-album-toolbar" x-data>
            <button type="button"
                    class="zp-album-tool"
                    x-on:click="$refs.albumPicker.click()"
                    title="{{ __('person.upload_photos') }}">
                <x-ts-icon icon="tabler.plus" class="size-4" />
            </button>

            <button type="button"
                    class="zp-album-tool {{ $editing ? 'is-on' : '' }}"
                    wire:click="toggleEditing"
                    title="{{ __('app.edit') }}"
                    aria-pressed="{{ $editing ? 'true' : 'false' }}">
                <x-ts-icon icon="tabler.pencil" class="size-4" />
            </button>

            <input type="file"
                   class="hidden"
                   x-ref="albumPicker"
                   wire:model="uploads"
                   accept=".jpeg,.jpg,.gif,.png,.svg,.webp,image/*"
                   multiple />
        </div>
    @endif

    @if ($photos->isEmpty())
        <div class="px-1 py-6 text-center text-sm text-[#5a3d22]">
            {{ __('app.nothing_recorded') }}
        </div>
    @else
        <div class="zp-album-masonry">
            @foreach ($photos as $photo)
                <figure class="zp-album-item" wire:key="album-{{ $photo['name'] }}">
                    <a href="{{ $photo['url_original'] }}" target="_blank" rel="noopener" title="{{ __('app.show') }}">
                        <img src="{{ $photo['url'] }}"
                             alt="{{ $photo['name'] }}"
                             loading="lazy"
                             decoding="async" />
                    </a>

                    @if ($photo['is_primary'] && ! $editing)
                        <span class="zp-album-badge-primary" title="{{ __('person.set_primary') }}">
                            <x-ts-icon icon="tabler.star-filled" class="size-3.5" />
                        </span>
                    @endif

                    @if ($canManage && $editing)
                        <button type="button"
                                class="zp-album-action zp-album-action-star {{ $photo['is_primary'] ? 'is-primary' : '' }}"
                                wire:click="setPrimary('{{ $photo['name'] }}')"
                                title="{{ __('person.set_primary') }}">
                            <x-ts-icon icon="{{ $photo['is_primary'] ? 'tabler.star-filled' : 'tabler.star' }}" class="size-4" />
                        </button>

                        <button type="button"
                                class="zp-album-action zp-album-action-delete"
                                wire:click="deletePhoto('{{ $photo['name'] }}')"
                                wire:confirm="{{ __('app.are_you_sure') }}"
                                title="{{ __('app.delete') }}">
                            <x-ts-icon icon="tabler.trash" class="size-4" />
                        </button>
                    @endif
                </figure>
            @endforeach
        </div>
    @endif
</div>
