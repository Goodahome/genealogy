<form wire:submit="saveChild">
    @csrf

    <x-ts-tab selected="{{ __('person.add_new_person_as_child') }}" class="dark:bg-red-100">
        <x-ts-tab.items tab="{{ __('person.add_new_person_as_child') }}">
            <div class="p-4 rounded-sm md:w-192 bg-neutral-200">
                @if (auth()->user()->currentTeam->personal_team)
                    <div class="mb-4">
                        <x-ts-alert color="cyan" icon="tabler.exclamation-circle" close>
                            <x-slot:title>
                                {{ __('team.personal_team_caution') }}
                            </x-slot:title>

                            <p>{{ __('team.personal_team_avoid') }}</p><br />
                            <p>{{ __('team.personal_team_instead') }}</p><br />
                            <p>{{ __('team.personal_team_action') }}</p>
                        </x-ts-alert>
                    </div>
                @endif

                <x-ts-errors class="mb-2" close />

                <div class="grid grid-cols-6 gap-5">
                    {{-- firstname --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-ts-input wire:model="childForm.firstname" id="firstname" label="{{ __('person.firstname') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="firstname" />
                    </div>

                    {{-- surname --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-ts-input wire:model="childForm.surname" id="surname" label="{{ __('person.surname') }} : *" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="surname" />
                    </div>

                    

                    {{-- nickname --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-ts-input wire:model="childForm.nickname" id="nickname" label="{{ __('person.nickname') }}" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="nickname" />
                    </div>

                    {{-- occupation --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-ts-input wire:model="childForm.occupation" id="occupation" label="{{ __('person.occupation') }}" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="organization-title" />
                    </div>
                    <x-hr.narrow class="col-span-6 my-0!" />

                    {{-- sex --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-label for="sex" class="mr-5" value="{{ __('person.sex') }} : *" />
                        <div class="flex">
                            <div class="mt-3 mb-[0.125rem] mr-4 inline-block min-h-[1.5rem] pl-[1.5rem]">
                                <x-ts-radio color="primary" wire:model="childForm.sex" name="sex" id="sexM" value="m" label="{{ __('app.male') }}" />
                            </div>
                            <div class="mt-3 mb-[0.125rem] mr-4 inline-block min-h-[1.5rem] pl-[1.5rem]">
                                <x-ts-radio color="primary" wire:model="childForm.sex" name="sex" id="sexF" value="f" label="{{ __('app.female') }}" />
                            </div>
                        </div>
                    </div>

                    
                    <x-hr.narrow class="col-span-6 my-0!" />

                    {{-- yob --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-ts-input wire:model="childForm.yob" id="yob" label="{{ __('person.yob') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="yob" />
                    </div>

                    {{-- 替换原来的日期选择部分 --}}
                    
                    {{-- 原来的 dob 字段 --}}
                    <x-ts-date wire:model="childForm.dob" id="dob" label="{{ __('person.dob') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" format="YYYY-MM-DD" :max-date="now()"
                            placeholder="{{ __('app.select') }} ..." />
                    
                    {{-- 新的农历日期选择器 --}}
                    <div class="col-span-6 md:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('person.dob') }} (农历):</label>
                        <div class="flex flex-wrap gap-2">
                            <select wire:model="childForm.dob_year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">年</option>
                                @for($year = 1900; $year <= 2100; $year++)
                                    <option value="{{ $year }}">{{ $year }}年</option>
                                @endfor
                            </select>
                            
                            <select wire:model="childForm.dob_month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">月</option>
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">{{ $month }}月</option>
                                @endfor
                            </select>
                            
                            <select wire:model="childForm.dob_day" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">日</option>
                                @for($day = 1; $day <= 30; $day++)
                                    <option value="{{ $day }}">{{ $day }}日</option>
                                @endfor
                            </select>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="dob_leap" wire:model="childForm.dob_leap" class="w-5 h-5 bg-gray-500 border-gray-500 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="dob_leap" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">闰月</label>
                            </div>
                        </div>
                    </div>

                    {{-- pob --}}
                    <div class="col-span-6">
                        <x-ts-input wire:model="childForm.pob" id="pob" label="{{ __('person.pob') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="pod" />
                    </div>
                    <x-hr.narrow class="col-span-6 my-0!" />

                    {{-- images --}}
                    <div class="col-span-6">
                        <x-ts-upload id="photos" wire:model="photos" label="{{ __('person.photos') }} :" accept=".jpeg, .jpg, .gif, .png, .svg, .webp"
                            hint="Max: 10 MB, Format: jpeg/jpg, gif, png, svg or webp" tip="{{ __('person.update_photos_tip') }} ..." multiple delete>
                        </x-ts-upload>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 text-right">
                <div class="flex-1 grow max-w-full text-left">
                    <x-action-message class="p-3 rounded-sm bg-yellow-200 text-yellow-700" role="alert" on="" wire:dirty>
                        {{ __('app.unsaved_changes') }} ...
                    </x-action-message>

                    <x-action-message class="p-3 rounded-sm bg-emerald-200 text-emerald-600" role="alert" on="saved">
                        {{ __('app.saved') }}
                    </x-action-message>
                </div>

                <div class="flex-1 grow max-w-full text-end">
                    <x-ts-button color="secondary" class="mr-1" wire:click="resetChild()" wire:dirty>
                        {{ __('app.cancel') }}
                    </x-ts-button>

                    <x-ts-button type="submit" color="primary">
                        {{ __('app.save') }}
                    </x-ts-button>
                </div>
            </div>
        </x-ts-tab.items>

        <x-ts-tab.items tab="{{ __('person.add_existing_person_as_child') }}">
            <div class="p-4 rounded-sm md:w-192 bg-neutral-200">
                <x-ts-errors class="mb-2" close />

                {{-- person_id --}}
                <div class="col-span-6">
                    <x-ts-select.styled wire:model="childForm.person_id" id="person_id" label="{{ __('person.person') }} : *" :options="$persons" select="label:name|value:id"
                        placeholder="{{ __('app.select') }} ..." wire:dirty.class="bg-yellow-200 dark:text-black" searchable>
                        <x-slot:after>
                            <div class="w-full px-2 mb-2">
                                <x-ts-alert title="{{ __('app.nothing_available') }}" text="{{ __('person.use_tab') . ' : ' . __('person.add_new_person_as_child') }}" color="cyan" />
                            </div>
                        </x-slot:after>
                    </x-ts-select.styled>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 text-right">
                <div class="flex-1 grow max-w-full text-left">
                    <x-action-message class="p-3 rounded-sm bg-yellow-200 text-yellow-700" role="alert" on="" wire:dirty>
                        {{ __('app.unsaved_changes') }} ...
                    </x-action-message>

                    <x-action-message class="p-3 rounded-sm bg-emerald-200 text-emerald-600" role="alert" on="saved">
                        {{ __('app.saved') }}
                    </x-action-message>
                </div>

                <div class="flex-1 grow max-w-full text-end">
                    <x-ts-button color="secondary" class="mr-1" wire:click="resetChild()" wire:dirty>
                        {{ __('app.cancel') }}
                    </x-ts-button>

                    <x-ts-button type="submit" color="primary">
                        {{ __('app.save') }}
                    </x-ts-button>
                </div>
            </div>
        </x-ts-tab.items>
    </x-ts-tab>
</form>
