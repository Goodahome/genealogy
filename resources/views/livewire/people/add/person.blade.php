<div class="zp-edit-modal-panel">
<form wire:submit="savePerson">
    @csrf

    <div class="md:w-192 flex flex-col rounded-sm bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 text-neutral-800 dark:text-neutral-50">
        <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
            <div class="flex flex-wrap items-start justify-center gap-2">
                <div class="flex-1 grow max-w-full min-w-max">
                    {{ __('person.add_person') }}
                </div>

                <div class="flex-1 grow max-w-full min-w-max text-end">
                    <x-ts-icon icon="tabler.user-plus" class="inline-block" />
                </div>
            </div>
        </div>

        <div class="p-4 bg-neutral-200">
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
                    <x-ts-input wire:model="personForm.firstname" id="firstname" label="{{ __('person.firstname') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="firstname"
                        autofocus />
                </div>

                {{-- surname --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="personForm.surname" id="surname" label="{{ __('person.surname') }} : *" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="surname"
                        required />
                </div>

                

                {{-- nickname --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="personForm.nickname" id="nickname" label="{{ __('person.nickname') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="nickname" />
                </div>

                {{-- occupation --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="personForm.occupation" id="occupation" label="{{ __('person.occupation') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="organization-title" />
                </div>
                <x-hr.narrow class="col-span-6 my-0!" />

                {{-- sex --}}
                <div class="col-span-6 md:col-span-3">
                    <x-label for="sex" class="mr-5" value="{{ __('person.sex') }}: *" />
                    <div class="zp-sex-picker mt-2" role="radiogroup" aria-label="{{ __('person.sex') }}">
                        <label class="zp-sex-option">
                            <input type="radio" class="sr-only" wire:model="personForm.sex" name="personForm_sex" id="person_sex_m" value="m" />
                            <x-ts-icon icon="tabler.gender-male" class="size-4 shrink-0" />
                            <span>{{ __('app.male') }}</span>
                        </label>
                        <label class="zp-sex-option">
                            <input type="radio" class="sr-only" wire:model="personForm.sex" name="personForm_sex" id="person_sex_f" value="f" />
                            <x-ts-icon icon="tabler.gender-female" class="size-4 shrink-0" />
                            <span>{{ __('app.female') }}</span>
                        </label>
                    </div>
                </div>

                
                <x-hr.narrow class="col-span-6 my-0!" />

                @include('livewire.people.partials.birth-date-fields', ['formPrefix' => 'personForm', 'idPrefix' => 'person'])
                @if (false)
                {{-- dob --}}
                <div class="col-span-6 md:col-span-3">
                    {{-- 阳历日期选择器 --}}
                    <div class="col-span-6 md:col-span-3">
                        <x-ts-date wire:model="personForm.dob" id="dob" label="{{ __('person.dob') }} (阳历):" wire:dirty.class="bg-yellow-200 dark:text-black" format="YYYY-MM-DD" :max-date="now()"
                            placeholder="{{ __('app.select') }} ..." />
                    </div>
                    
                    {{-- 农历日期选择器 --}}
                    <div class="col-span-6 md:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('person.dob') }} (农历):</label>
                        <div class="flex flex-wrap gap-2">
                            <select wire:model="personForm.dob_year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">年</option>
                                @for($year = 1900; $year <= 2100; $year++)
                                    <option value="{{ $year }}">{{ $year }}年</option>
                                @endfor
                            </select>
                            
                            <!-- 其他选择器保持不变 -->
                            <select wire:model="personForm.dob_month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">月</option>
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">{{ $month }}月</option>
                                @endfor
                            </select>
                            
                            <select wire:model="personForm.dob_day" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">日</option>
                                @for($day = 1; $day <= 30; $day++)
                                    <option value="{{ $day }}">{{ $day }}日</option>
                                @endfor
                            </select>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="dob_leap" wire:model="personForm.dob_leap" class="w-5 h-5 bg-gray-500 border-gray-500 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="dob_leap" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">闰月</label>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 同样修改 dod 部分 --}}
                </div>

                @endif
                {{-- pob --}}
                <div class="col-span-6">
                    <x-ts-input wire:model="personForm.pob" id="pob" label="{{ __('person.pob') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="pod" />
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
                <x-ts-button color="secondary" class="mr-1" wire:click="resetPerson()" wire:dirty>
                    {{ __('app.cancel') }}
                </x-ts-button>

                <x-ts-button type="submit" color="primary">
                    {{ __('app.save') }}
                </x-ts-button>
            </div>
        </div>
    </div>
</form>
</div>
