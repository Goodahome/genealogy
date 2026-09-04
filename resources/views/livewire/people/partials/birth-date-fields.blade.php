@php
    $formPrefix = $formPrefix ?? 'profileForm';
    $idPrefix = $idPrefix ?? str_replace('Form', '', $formPrefix);
    $lunarPrefix = $lunarPrefix ?? 'dob';
    $dateLabel = $dateLabel ?? __('person.dob');

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

<div class="col-span-6 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5"
     x-data="{
        syncing: false,
        toInt(v) {
            if (v === null || v === undefined || v === '') return null;
            const n = parseInt(v, 10);
            return Number.isFinite(n) ? n : null;
        },
        async setLocal(path, value) {
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
                await this.setLocal('{{ $formPrefix }}.solar_year', y);
                await this.setLocal('{{ $formPrefix }}.solar_month', m);
                await this.setLocal('{{ $formPrefix }}.solar_day', d);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_year', lunar.year);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_month', lunar.month);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_day', lunar.day);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_leap', !!lunar.isLeap);
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
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_year', y);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_month', m);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_day', d);
                await this.setLocal('{{ $formPrefix }}.{{ $lunarPrefix }}_leap', leap);
                await this.setLocal('{{ $formPrefix }}.solar_year', solar.year);
                await this.setLocal('{{ $formPrefix }}.solar_month', solar.month);
                await this.setLocal('{{ $formPrefix }}.solar_day', solar.day);
            } finally {
                this.syncing = false;
            }
        },
     }">
    <div>
        <label class="mb-2 block text-sm font-bold text-[#2a2118]">{{ $dateLabel }} (阳历):</label>
        <div class="flex flex-wrap items-center gap-2">
            <input type="text" inputmode="numeric" pattern="[0-9]*"
                   wire:model="{{ $formPrefix }}.solar_year"
                   id="{{ $idPrefix }}_solar_year" x-ref="solarYear"
                   x-on:change="syncSolarToLunar()" maxlength="4" placeholder="年"
                   class="zp-date-field" />
            <select wire:model="{{ $formPrefix }}.solar_month"
                    id="{{ $idPrefix }}_solar_month" x-ref="solarMonth"
                    x-on:change="syncSolarToLunar()" class="zp-date-field">
                <option value="">月</option>
                @for ($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}">{{ $month }}月</option>
                @endfor
            </select>
            <select wire:model="{{ $formPrefix }}.solar_day"
                    id="{{ $idPrefix }}_solar_day" x-ref="solarDay"
                    x-on:change="syncSolarToLunar()" class="zp-date-field">
                <option value="">日</option>
                @for ($day = 1; $day <= 31; $day++)
                    <option value="{{ $day }}">{{ $day }}日</option>
                @endfor
            </select>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-[#2a2118]">{{ $dateLabel }} (农历):</label>
        <div class="flex flex-wrap items-center gap-2">
            <input type="text" inputmode="numeric" pattern="[0-9]*"
                   wire:model="{{ $formPrefix }}.{{ $lunarPrefix }}_year"
                   id="{{ $idPrefix }}_{{ $lunarPrefix }}_year" x-ref="lunarYear"
                   x-on:change="syncLunarToSolar()" maxlength="4" placeholder="年"
                   class="zp-date-field" />
            <select wire:model="{{ $formPrefix }}.{{ $lunarPrefix }}_month"
                    id="{{ $idPrefix }}_{{ $lunarPrefix }}_month" x-ref="lunarMonth"
                    x-on:change="syncLunarToSolar()" class="zp-date-field">
                <option value="">月</option>
                @foreach ($lunarMonths as $month => $label)
                    <option value="{{ $month }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model="{{ $formPrefix }}.{{ $lunarPrefix }}_day"
                    id="{{ $idPrefix }}_{{ $lunarPrefix }}_day" x-ref="lunarDay"
                    x-on:change="syncLunarToSolar()" class="zp-date-field">
                <option value="">日</option>
                @foreach ($lunarDays as $day => $label)
                    <option value="{{ $day }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex h-[2.625rem] items-center">
                <input type="checkbox" id="{{ $idPrefix }}_{{ $lunarPrefix }}_leap"
                       x-ref="lunarLeap" wire:model="{{ $formPrefix }}.{{ $lunarPrefix }}_leap"
                       x-on:change="syncLunarToSolar()"
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="{{ $idPrefix }}_{{ $lunarPrefix }}_leap" class="ml-2 text-sm font-medium text-[#2a2118]">闰月</label>
            </div>
        </div>
    </div>
</div>
