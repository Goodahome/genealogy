<form wire:submit="saveProfile">
    @csrf

    <div @class([
        'flex flex-col rounded-sm text-neutral-800 dark:text-neutral-50',
        'md:w-192 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700' => ! $asModal,
        'w-full' => $asModal,
    ])>
        @unless ($asModal)
            <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
                <div class="flex flex-wrap items-start justify-center gap-2">
                    <div class="flex-1 grow max-w-full min-w-max">
                        {{ __('person.edit_profile') }}
                    </div>

                    <div class="flex-1 grow max-w-full min-w-max text-end">
                        <x-ts-icon icon="tabler.id" class="inline-block" />
                    </div>
                </div>
            </div>
        @endunless

        <div @class(['p-4', 'bg-neutral-200' => ! $asModal, 'pt-1' => $asModal])>
            <x-ts-errors class="mb-2" close />

            <div class="grid grid-cols-6 gap-5">
                {{-- firstname --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="profileForm.firstname" id="firstname" label="{{ __('person.firstname') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" required autofocus />
                </div>

                {{-- surname --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="profileForm.surname" id="surname" label="{{ __('person.surname') }} : *" wire:dirty.class="bg-yellow-200 dark:text-black" required />
                </div>

                

                {{-- nickname --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="profileForm.nickname" id="nickname" label="{{ __('person.nickname') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" />
                </div>

                {{-- occupation --}}
                <div class="col-span-6 md:col-span-3">
                    <x-ts-input wire:model="profileForm.occupation" id="occupation" label="{{ __('person.occupation') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" />
                </div>
                <x-hr.narrow class="col-span-6 my-0!" />

                {{-- sex --}}
                <div class="col-span-6">
                    <x-label for="sex" class="mr-5" value="{{ __('person.sex') }}: *" />
                    <div class="zp-sex-picker mt-2" role="radiogroup" aria-label="{{ __('person.sex') }}">
                        <label class="zp-sex-option">
                            <input type="radio"
                                   class="sr-only"
                                   wire:model="profileForm.sex"
                                   name="profileForm_sex"
                                   id="sexM"
                                   value="m" />
                            <x-ts-icon icon="tabler.gender-male" class="size-4 shrink-0" />
                            <span>{{ __('app.male') }}</span>
                        </label>

                        <label class="zp-sex-option">
                            <input type="radio"
                                   class="sr-only"
                                   wire:model="profileForm.sex"
                                   name="profileForm_sex"
                                   id="sexF"
                                   value="f" />
                            <x-ts-icon icon="tabler.gender-female" class="size-4 shrink-0" />
                            <span>{{ __('app.female') }}</span>
                        </label>
                    </div>
                </div>

                
                <x-hr.narrow class="col-span-6 my-0!" />

                {{-- 阳历 / 农历：同一行；填齐年月日后互相同步（约 1900–2100） --}}
                <div class="col-span-6 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5"
                     x-data="{
                        syncing: false,
                        toInt(v) {
                            if (v === null || v === undefined || v === '') return null;
                            const n = parseInt(v, 10);
                            return Number.isFinite(n) ? n : null;
                        },
                        async syncSolarToLunar() {
                            if (this.syncing || !window.ChineseLunar?.solarToLunar) return;
                            const y = this.toInt(this.$refs.solarYear?.value);
                            const m = this.toInt(this.$refs.solarMonth?.value);
                            const d = this.toInt(this.$refs.solarDay?.value);
                            if (!y || !m || !d) return;
                            const lunar = window.ChineseLunar.solarToLunar(y, m, d);
                            if (!lunar) return;
                            this.syncing = true;
                            try {
                                await $wire.syncBirthFromSolar(y, m, d, lunar.year, lunar.month, lunar.day, !!lunar.isLeap);
                            } finally {
                                this.syncing = false;
                            }
                        },
                        async syncLunarToSolar() {
                            if (this.syncing || !window.ChineseLunar?.lunarToSolar) return;
                            const y = this.toInt(this.$refs.lunarYear?.value);
                            const m = this.toInt(this.$refs.lunarMonth?.value);
                            const d = this.toInt(this.$refs.lunarDay?.value);
                            const leap = !!this.$refs.lunarLeap?.checked;
                            if (!y || !m || !d) return;
                            const solar = window.ChineseLunar.lunarToSolar(y, m, d, leap);
                            if (!solar) return;
                            this.syncing = true;
                            try {
                                await $wire.syncBirthFromLunar(y, m, d, leap, solar.year, solar.month, solar.day);
                            } finally {
                                this.syncing = false;
                            }
                        },
                     }">
                    {{-- 阳历出生：年 / 月 / 日 --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-[#2a2118]">{{ __('person.dob') }} (阳历):</label>
                        <div class="flex flex-wrap gap-2">
                            <select wire:model="profileForm.solar_year"
                                    id="solar_year"
                                    x-ref="solarYear"
                                    x-on:change="syncSolarToLunar()"
                                    class="block rounded-sm border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">年</option>
                                @php
                                    $solarYearMax = (int) date('Y');
                                    $solarYearMin = 1000;
                                    $currentSolarYear = (int) ($profileForm->solar_year ?? 0);
                                    if ($currentSolarYear > 0 && $currentSolarYear < $solarYearMin) {
                                        $solarYearMin = $currentSolarYear;
                                    }
                                @endphp
                                @for ($year = $solarYearMax; $year >= $solarYearMin; $year--)
                                    <option value="{{ $year }}">{{ $year }}年</option>
                                @endfor
                            </select>

                            <select wire:model="profileForm.solar_month"
                                    id="solar_month"
                                    x-ref="solarMonth"
                                    x-on:change="syncSolarToLunar()"
                                    class="block rounded-sm border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">月</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">{{ $month }}月</option>
                                @endfor
                            </select>

                            <select wire:model="profileForm.solar_day"
                                    id="solar_day"
                                    x-ref="solarDay"
                                    x-on:change="syncSolarToLunar()"
                                    class="block rounded-sm border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">日</option>
                                @for ($day = 1; $day <= 31; $day++)
                                    <option value="{{ $day }}">{{ $day }}日</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- 农历出生：年 / 月 / 日 / 闰月 --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-[#2a2118]">{{ __('person.dob') }} (农历):</label>
                        <div class="flex flex-wrap gap-2">
                            <select wire:model="profileForm.dob_year"
                                    x-ref="lunarYear"
                                    x-on:change="syncLunarToSolar()"
                                    class="block rounded-sm border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">年</option>
                                @for ($year = 1900; $year <= 2100; $year++)
                                    <option value="{{ $year }}">{{ $year }}年</option>
                                @endfor
                            </select>

                            <select wire:model="profileForm.dob_month"
                                    x-ref="lunarMonth"
                                    x-on:change="syncLunarToSolar()"
                                    class="block rounded-sm border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">月</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">{{ $month }}月</option>
                                @endfor
                            </select>

                            <select wire:model="profileForm.dob_day"
                                    x-ref="lunarDay"
                                    x-on:change="syncLunarToSolar()"
                                    class="block rounded-sm border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">日</option>
                                @for ($day = 1; $day <= 30; $day++)
                                    <option value="{{ $day }}">{{ $day }}日</option>
                                @endfor
                            </select>

                            <div class="flex items-center">
                                <input type="checkbox"
                                       id="dob_leap"
                                       x-ref="lunarLeap"
                                       wire:model="profileForm.dob_leap"
                                       x-on:change="syncLunarToSolar()"
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="dob_leap" class="ml-2 text-sm font-medium text-[#2a2118]">闰月</label>
                            </div>
                        </div>
                    </div>

                    <p class="col-span-full text-xs text-[#6b542e]">可只填阳历年份；填齐任一侧年月日会自动换算另一侧（约 1900–2100）。</p>
                </div>

                {{-- pob --}}
                <div class="col-span-6">
                    <x-ts-input wire:model="profileForm.pob" id="pob" label="{{ __('person.pob') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="pob" />
                </div>
                <x-hr.narrow class="col-span-6 my-0!" />

                {{-- summary --}}
                <div class="col-span-6">
                    <x-ts-textarea wire:model="profileForm.summary" id="summary" label="{{ __('person.summary') }} :" wire:dirty.class="bg-yellow-200 dark:text-black" autocomplete="summary"
                        maxlength="65535" count />
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
                <x-ts-button color="secondary" class="mr-1" wire:click="resetProfile()" wire:dirty>
                    {{ __('app.cancel') }}
                </x-ts-button>

                <x-ts-button type="submit" color="primary">
                    {{ __('app.save') }}
                </x-ts-button>
            </div>
        </div>
    </div>
</form>
