<form wire:submit="saveDeath">
    @csrf

    <div class="md:w-192 flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
        <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
            <div class="flex flex-wrap items-start justify-center gap-2">
                <div class="flex-1 grow max-w-full min-w-max">
                    {{ __('person.edit_death') }}
                </div>

                <div class="flex-1 grow max-w-full min-w-max text-end">
                    <x-ts-icon icon="tabler.grave-2" class="inline-block" />
                </div>
            </div>
        </div>

        <div class="p-4 bg-neutral-200">
            <x-ts-errors class="mb-2" close />

            <div class="grid grid-cols-6 gap-5">
                {{-- yod --}}
                <div class="col-span-3">
                    <x-ts-input wire:model="deathForm.yod" id="yod" label="{{ __('person.yod') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autofocus />
                </div>

                {{-- 替换原来的日期选择部分 --}}
                
                {{-- 原来的 dod 字段 --}}
                <x-ts-date wire:model="deathForm.dod" id="dod" label="{{ __('person.dod') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" format="YYYY-MM-DD" :max-date="now()"
                        placeholder="{{ __('app.select') }} ..." />
                
                {{-- 新的农历死亡日期选择器 --}}
                <div class="col-span-3">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('person.dod') }} (农历):</label>
                    <div class="flex flex-wrap gap-2">
                        <select wire:model="deathForm.dod_year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">年</option>
                            @for($year = 1900; $year <= 2100; $year++)
                                <option value="{{ $year }}">{{ $year }}年</option>
                            @endfor
                        </select>
                        
                        <select wire:model="deathForm.dod_month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">月</option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ $month }}">{{ $month }}月</option>
                            @endfor
                        </select>
                        
                        <select wire:model="deathForm.dod_day" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">日</option>
                            @for($day = 1; $day <= 30; $day++)
                                <option value="{{ $day }}">{{ $day }}日</option>
                            @endfor
                        </select>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="dod_leap" wire:model="deathForm.dod_leap" class="w-5 h-5 bg-gray-500 border-gray-500 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="dod_leap" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">闰月</label>
                        </div>
                    </div>
                </div>

                {{-- pod --}}
                <div class="col-span-6">
                    <x-ts-input wire:model="deathForm.pod" id="pod" label="{{ __('person.pod') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" />
                </div>
                <x-hr.narrow class="col-span-6 my-0!" />

                <div class="h-4 col-span-5">
                    <h4 class="text-lg font-medium text-neutral-800">{{ __('person.cemetery_location') }}</h4>
                </div>

                {{-- show on google maps button --}}
                <div class="h-4 col-span-1 text-end">
                    @if ($person->cemetery_google)
                        <a target="_blank" href="{{ $person->cemetery_google }}">
                            <x-ts-button color="cyan" class="p-2! mb-2 text-white" title="{{ __('app.show_on_google_maps') }}">
                                <x-ts-icon icon="tabler.brand-google-maps" class="size-5" />
                            </x-ts-button>
                        </a>
                    @endif
                </div>

                {{-- cemetery_location_name --}}
                <div class="col-span-6">
                    <x-ts-input wire:model="deathForm.cemetery_location_name" id="cemetery_location_name" label="{{ __('metadata.location_name') }} :"
                        wire:dirty.class="bg-yellow-200 dark:text-black" />
                </div>

                {{-- cemetery_location_address --}}
                <div class="col-span-6">
                    <x-ts-textarea wire:model="deathForm.cemetery_location_address" id="cemetery_location_address" label="{{ __('metadata.address') }} :"
                        wire:dirty.class="bg-yellow-200 dark:text-black" resize-auto />
                </div>

                {{-- cemetery_location_latitude --}}
                <div class="col-span-3">
                    <x-ts-input wire:model="deathForm.cemetery_location_latitude" id="cemetery_location_latitude" label="{{ __('metadata.latitude') }} :"
                        wire:dirty.class="bg-yellow-200 dark:text-black" />
                </div>

                {{-- cemetery_location_longitude --}}
                <div class="col-span-3">
                    <x-ts-input wire:model="deathForm.cemetery_location_longitude" id="cemetery_location_longitude" label="{{ __('metadata.longitude') }} :"
                        wire:dirty.class="bg-yellow-200 dark:text-black" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end p-4 text-right rounded-b sm:px-6">
            <div class="flex-1 grow max-w-full text-left">
                <x-action-message class="p-3 rounded-sm bg-yellow-200 text-yellow-700" role="alert" on="" wire:dirty>
                    {{ __('app.unsaved_changes') }} ...
                </x-action-message>

                <x-action-message class="p-3 rounded-sm bg-emerald-200 text-emerald-600" role="alert" on="saved">
                    {{ __('app.saved') }}
                </x-action-message>
            </div>

            <div class="flex-1 grow max-w-full text-end">
                <x-ts-button color="secondary" class="mr-1" wire:click="resetDeath()" wire:dirty>
                    {{ __('app.cancel') }}
                </x-ts-button>

                <x-ts-button type="submit" color="primary">
                    {{ __('app.save') }}
                </x-ts-button>
            </div>
        </div>
    </div>
</form>
