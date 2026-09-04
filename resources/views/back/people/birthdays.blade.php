@section('title')
    &vert; {{ __('birthday.birthdays') }}
@endsection

@push('styles')
    <link href="{{ asset('css/birthday-calendar.css') }}" rel="stylesheet">
@endpush

<x-app-layout>
    <x-slot name="heading">
        {{ __('birthday.birthdays') }}
    </x-slot>

    <script src="{{ asset('js/lunar.js') }}"></script>
    <script src="{{ asset('js/chinese-lunar.js') }}"></script>
    <script>
        window.birthdayCalendar = function (people) {
            const turnsTemplate = @js(__('birthday.turns'));
            const deceasedLabel = @js(__('person.deceased'));
            const moreLabel = @js(__('birthday.more'));
            const yearUnit = @js(__('birthday.year_unit'));
            const monthUnit = @js(__('birthday.month_unit'));

            return {
                people: people || [],
                year: new Date().getFullYear(),
                month: new Date().getMonth() + 1,
                weekdays: @js([
                    __('birthday.weekday_mon'),
                    __('birthday.weekday_tue'),
                    __('birthday.weekday_wed'),
                    __('birthday.weekday_thu'),
                    __('birthday.weekday_fri'),
                    __('birthday.weekday_sat'),
                    __('birthday.weekday_sun'),
                ]),
                get titleText() {
                    return this.year + yearUnit + this.month + monthUnit;
                },
                get cells() {
                    return this.buildCells(this.year, this.month);
                },
                prevMonth() {
                    if (this.month === 1) {
                        this.year -= 1;
                        this.month = 12;
                    } else {
                        this.month -= 1;
                    }
                },
                nextMonth() {
                    if (this.month === 12) {
                        this.year += 1;
                        this.month = 1;
                    } else {
                        this.month += 1;
                    }
                },
                goToday() {
                    const now = new Date();
                    this.year = now.getFullYear();
                    this.month = now.getMonth() + 1;
                },
                peopleOn(month, day) {
                    return this.people
                        .filter((person) => person.month === month && person.day === day)
                        .map((person) => {
                            const age = Math.max(0, this.year - person.birth_year);
                            const cake = turnsTemplate.replace(':age', age);
                            return Object.assign({}, person, {
                                label: person.name + ' · ' + cake,
                                tooltip: person.name + ' · ' + cake + (person.deceased ? ' · ' + deceasedLabel : ''),
                            });
                        });
                },
                buildCells(year, month) {
                    const first = new Date(year, month - 1, 1);
                    const startOffset = (first.getDay() + 6) % 7;
                    const start = new Date(year, month - 1, 1 - startOffset);
                    const today = new Date();
                    const todayKey = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
                    const cells = [];

                    for (let i = 0; i < 42; i++) {
                        const date = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
                        const y = date.getFullYear();
                        const m = date.getMonth() + 1;
                        const d = date.getDate();
                        const weekday = (date.getDay() + 6) % 7;
                        const info = window.ChineseLunar
                            ? window.ChineseLunar.dayInfo(y, m, d)
                            : { label: '', festivals: [], solarTerm: null, isHoliday: false, lunarText: '', lunar: null };
                        const festiveLabel = (info.festivals && info.festivals[0]) || info.solarTerm || null;
                        const meta = festiveLabel || info.lunarText || '';
                        const lunarFull = info.lunar
                            ? ('农历' + (info.lunar.isLeap ? '闰' : '') + ((window.ChineseLunar.months || {})[info.lunar.month] || '') + ((window.ChineseLunar.days || {})[info.lunar.day] || ''))
                            : '';
                        const metaTitle = [lunarFull].concat(info.festivals || []).concat(info.solarTerm ? [info.solarTerm] : []).filter(Boolean).join(' · ');
                        const dayBirthdays = this.peopleOn(m, d);

                        cells.push({
                            key: y + '-' + m + '-' + d,
                            year: y,
                            month: m,
                            day: d,
                            inMonth: m === month,
                            isToday: (y + '-' + m + '-' + d) === todayKey,
                            isWeekend: weekday >= 5,
                            isHoliday: Boolean(info.isHoliday),
                            festive: Boolean(festiveLabel),
                            meta: meta,
                            metaTitle: metaTitle,
                            birthdays: dayBirthdays,
                            moreText: '+' + Math.max(0, dayBirthdays.length - 3) + ' ' + moreLabel,
                        });
                    }

                    return cells;
                },
            };
        };
    </script>

    <div
        class="birthday-calendar flex min-h-[calc(100dvh-6.5rem)] w-full flex-col p-2 sm:p-4"
        x-data="birthdayCalendar(@js($birthdays))"
        x-cloak
    >
        <div class="flex min-h-0 flex-1 w-full flex-col overflow-hidden rounded-sm bg-white text-neutral-800 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50">
            <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b border-neutral-200 px-3 py-3 dark:border-neutral-600">
                <div class="flex items-center gap-2">
                    <button type="button" class="inline-flex size-8 items-center justify-center rounded-sm border border-neutral-300 hover:bg-neutral-100 dark:border-neutral-500 dark:hover:bg-neutral-600" @click="prevMonth" title="{{ __('birthday.prev_month') }}">
                        <x-ts-icon icon="tabler.chevron-left" class="size-5" />
                    </button>
                    <button type="button" class="inline-flex size-8 items-center justify-center rounded-sm border border-neutral-300 hover:bg-neutral-100 dark:border-neutral-500 dark:hover:bg-neutral-600" @click="nextMonth" title="{{ __('birthday.next_month') }}">
                        <x-ts-icon icon="tabler.chevron-right" class="size-5" />
                    </button>
                    <h2 class="ml-1 text-lg font-semibold tracking-wide" x-text="titleText"></h2>
                </div>

                <div class="flex items-center gap-2">
                    <span class="hidden text-xs text-neutral-500 dark:text-neutral-300 lg:inline">{{ __('birthday.calendar_legend') }}</span>
                    <button type="button" class="rounded-sm border border-neutral-300 px-3 py-1.5 text-sm hover:bg-neutral-100 dark:border-neutral-500 dark:hover:bg-neutral-600" @click="goToday">
                        {{ __('birthday.today') }}
                    </button>
                </div>
            </div>

            <div class="birthday-calendar__grid shrink-0">
                <template x-for="(weekday, index) in weekdays" :key="weekday">
                    <div class="birthday-calendar__weekday" :class="{ 'is-weekend': index >= 5 }" x-text="weekday"></div>
                </template>
            </div>

            <div class="min-h-0 flex-1 overflow-auto">
                <div class="birthday-calendar__grid">
                    <template x-for="cell in cells" :key="cell.key">
                        <div
                            class="birthday-calendar__cell"
                            :class="{
                                'is-outside': !cell.inMonth,
                                'is-today': cell.isToday,
                                'is-weekend': cell.isWeekend,
                                'is-holiday': cell.isHoliday,
                            }"
                        >
                            <div class="flex items-start justify-between gap-1">
                                <div
                                    class="birthday-calendar__meta"
                                    :class="{ 'is-festive': cell.festive }"
                                    x-text="cell.meta"
                                    :title="cell.metaTitle"
                                ></div>
                                <div class="birthday-calendar__daynum" x-text="cell.day"></div>
                            </div>

                            <div class="birthday-calendar__chips" x-show="cell.birthdays.length > 0">
                                <template x-for="person in cell.birthdays.slice(0, 3)" :key="person.id">
                                    <a
                                        class="birthday-calendar__chip"
                                        :class="{ 'is-deceased': person.deceased }"
                                        :href="'/people/' + person.id"
                                        :title="person.tooltip"
                                        x-text="person.label"
                                    ></a>
                                </template>
                                <div class="birthday-calendar__more" x-show="cell.birthdays.length > 3" x-text="cell.moreText"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
