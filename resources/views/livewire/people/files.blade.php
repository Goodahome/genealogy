<div class="zp-files">
    @if ($canManage)
        <div class="zp-album-toolbar" x-data>
            <button type="button"
                    class="zp-album-tool"
                    x-on:click="$refs.filesPicker.click()"
                    title="{{ __('person.upload_files') }}">
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
                   x-ref="filesPicker"
                   wire:model="uploads"
                   accept=".pdf,.txt,.doc,.docx,.xls,.xlsx"
                   multiple />
        </div>
    @endif

    @if ($files->isEmpty())
        <div class="px-1 py-6 text-center text-sm text-[#5a3d22]">
            {{ __('app.nothing_recorded') }}
        </div>
    @else
        <div class="zp-files-list">
            @foreach ($files as $file)
                @php
                    $file_type = substr($file['file_name'], strpos($file['file_name'], '.') + 1);
                @endphp

                <div class="zp-files-item" wire:key="file-{{ $file->id }}">
                    <div class="zp-files-main">
                        <img src="{{ url('img/icons/' . $file_type . '.svg') }}"
                             width="20"
                             height="20"
                             alt="{{ $file['name'] }}"
                             class="shrink-0 rounded-sm" />

                        <x-link href="{{ $file->getUrl() }}" target="_blank" title="{{ __('app.show') }}" class="min-w-0 truncate">
                            {{ $file->name }}
                        </x-link>

                        <span class="zp-files-meta shrink-0">{{ $file->human_readable_size }}</span>
                    </div>

                    @if ($canManage && $editing)
                        <div class="zp-files-actions">
                            @if ($file->order_column > 1)
                                <button type="button"
                                        class="zp-files-action"
                                        wire:click="moveFile({{ $file->order_column }}, 'up')"
                                        title="{{ __('app.move_up') }}">
                                    <x-ts-icon icon="tabler.arrow-up" class="size-4" />
                                </button>
                            @endif

                            @if ($file->order_column < $files->count())
                                <button type="button"
                                        class="zp-files-action"
                                        wire:click="moveFile({{ $file->order_column }}, 'down')"
                                        title="{{ __('app.move_down') }}">
                                    <x-ts-icon icon="tabler.arrow-down" class="size-4" />
                                </button>
                            @endif

                            <button type="button"
                                    class="zp-files-action zp-files-action-delete"
                                    wire:click="deleteFile({{ $file->id }})"
                                    wire:confirm="{{ __('app.are_you_sure') }}"
                                    title="{{ __('app.delete') }}">
                                <x-ts-icon icon="tabler.trash" class="size-4" />
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
