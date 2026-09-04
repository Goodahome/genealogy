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

                {{-- 阳历 / 农历：同一行；填齐年月日后互相同步（6tail lunar-javascript，约 1–9999） --}}
                <div class="col-span-6 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5"
                     x-data="{
                        syncing: false,
                        toInt(v) {
                            if (v === null || v === undefined || v === '') return null;
                            const n = parseInt(v, 10);
                            return Number.isFinite(n) ? n : null;
                        },
                        async setLocal(path, value) {
                            // 第三个参数 false：只改前端状态，不发请求，避免重置 wire:dirty
                            await $wire.set(path, value, false);
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
                                await this.setLocal('profileForm.solar_year', y);
                                await this.setLocal('profileForm.solar_month', m);
                                await this.setLocal('profileForm.solar_day', d);
                                await this.setLocal('profileForm.dob_year', lunar.year);
                                await this.setLocal('profileForm.dob_month', lunar.month);
                                await this.setLocal('profileForm.dob_day', lunar.day);
                                await this.setLocal('profileForm.dob_leap', !!lunar.isLeap);
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
                                await this.setLocal('profileForm.dob_year', y);
                                await this.setLocal('profileForm.dob_month', m);
                                await this.setLocal('profileForm.dob_day', d);
                                await this.setLocal('profileForm.dob_leap', leap);
                                await this.setLocal('profileForm.solar_year', solar.year);
                                await this.setLocal('profileForm.solar_month', solar.month);
                                await this.setLocal('profileForm.solar_day', solar.day);
                            } finally {
                                this.syncing = false;
                            }
                        },
                     }">
                    {{-- 阳历出生：年 / 月 / 日 --}}
                    <div>
                        <label class="mb-2 block text-sm font-bold text-[#2a2118]">{{ __('person.dob') }} (阳历):</label>
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="text"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   wire:model="profileForm.solar_year"
                                   id="solar_year"
                                   x-ref="solarYear"
                                   x-on:change="syncSolarToLunar()"
                                   maxlength="4"
                                   placeholder="年"
                                   class="zp-date-field" />

                            <select wire:model="profileForm.solar_month"
                                    id="solar_month"
                                    x-ref="solarMonth"
                                    x-on:change="syncSolarToLunar()"
                                    class="zp-date-field">
                                <option value="">月</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">{{ $month }}月</option>
                                @endfor
                            </select>

                            <select wire:model="profileForm.solar_day"
                                    id="solar_day"
                                    x-ref="solarDay"
                                    x-on:change="syncSolarToLunar()"
                                    class="zp-date-field">
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
                        <div class="flex flex-wrap items-center gap-2">
                            @php
                                $lunarMonths = [
                                    1 => '正月', 2 => '二月', 3 => '三月', 4 => '四月',
                                    5 => '五月', 6 => '六月', 7 => '七月', 8 => '八月',
                                    9 => '九月', 10 => '十月', 11 => '冬月', 12 => '腊月',
                                ];
                                $lunarDays = [
                                    1 => '初一', 2 => '初二', 3 => '初三', 4 => '初四', 5 => '初五',
                                    6 => '初六', 7 => '初七', 8 => '初八', 9 => '初九', 10 => '初十',
                                    11 => '十一', 12 => '十二', 13 => '十三', 14 => '十四', 15 => '十五',
                                    16 => '十六', 17 => '十七', 18 => '十八', 19 => '十九', 20 => '二十',
                                    21 => '廿一', 22 => '廿二', 23 => '廿三', 24 => '廿四', 25 => '廿五',
                                    26 => '廿六', 27 => '廿七', 28 => '廿八', 29 => '廿九', 30 => '三十',
                                ];
                            @endphp
                            <input type="text"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   wire:model="profileForm.dob_year"
                                   x-ref="lunarYear"
                                   x-on:change="syncLunarToSolar()"
                                   maxlength="4"
                                   placeholder="年"
                                   class="zp-date-field" />

                            <select wire:model="profileForm.dob_month"
                                    x-ref="lunarMonth"
                                    x-on:change="syncLunarToSolar()"
                                    class="zp-date-field">
                                <option value="">月</option>
                                @foreach ($lunarMonths as $month => $label)
                                    <option value="{{ $month }}">{{ $label }}</option>
                                @endforeach
                            </select>

                            <select wire:model="profileForm.dob_day"
                                    x-ref="lunarDay"
                                    x-on:change="syncLunarToSolar()"
                                    class="zp-date-field">
                                <option value="">日</option>
                                @foreach ($lunarDays as $day => $label)
                                    <option value="{{ $day }}">{{ $label }}</option>
                                @endforeach
                            </select>

                            <div class="flex h-[2.625rem] items-center">
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
