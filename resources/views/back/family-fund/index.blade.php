@php
    use App\Models\FamilyFundEntry;
@endphp

@section('title')
    &vert; {{ __('fund.family_fund') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('fund.family_fund') }}
        <span class="text-sm font-normal text-neutral-500 dark:text-neutral-400">
            — {{ auth()->user()->currentTeam->name }}
        </span>
    </x-slot>

    <div class="w-full space-y-4 p-2 sm:p-4">
        {{-- overview --}}
        <div class="rounded-sm bg-white p-4 text-neutral-800 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50">
            <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 class="text-lg font-medium">{{ __('fund.overview') }}</h2>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-300">{{ __('fund.intro') }}</p>
                </div>

                @if (auth()->user()->hasPermission('fund:create'))
                    <x-ts-button href="{{ route('family-fund.create') }}" color="emerald" class="text-sm text-white">
                        <x-ts-icon icon="tabler.circle-plus" class="size-5" />
                        {{ __('fund.add_entry') }}
                    </x-ts-button>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div class="rounded-sm border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-600 dark:bg-neutral-800">
                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('fund.balance') }}</div>
                    <div @class([
                        'mt-1 text-xl font-semibold',
                        'text-emerald-600 dark:text-emerald-400' => $balance >= 0,
                        'text-red-600 dark:text-red-400' => $balance < 0,
                    ])>
                        ¥{{ number_format($balance, 2) }}
                    </div>
                </div>

                <div class="rounded-sm border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-600 dark:bg-neutral-800">
                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('fund.total_income') }}</div>
                    <div class="mt-1 text-xl font-semibold text-emerald-600 dark:text-emerald-400">
                        ¥{{ number_format($totalIncome, 2) }}
                    </div>
                </div>

                <div class="rounded-sm border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-600 dark:bg-neutral-800">
                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('fund.total_expense') }}</div>
                    <div class="mt-1 text-xl font-semibold text-red-600 dark:text-red-400">
                        ¥{{ number_format($totalExpense, 2) }}
                    </div>
                </div>

                <div class="rounded-sm border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-600 dark:bg-neutral-800">
                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('fund.year_net') }}</div>
                    @php $yearNet = $yearIncome - $yearExpense; @endphp
                    <div @class([
                        'mt-1 text-xl font-semibold',
                        'text-emerald-600 dark:text-emerald-400' => $yearNet >= 0,
                        'text-red-600 dark:text-red-400' => $yearNet < 0,
                    ])>
                        ¥{{ number_format($yearNet, 2) }}
                    </div>
                    <div class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                        +¥{{ number_format($yearIncome, 2) }} / -¥{{ number_format($yearExpense, 2) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- donation ranking --}}
        <div class="rounded-sm bg-white text-neutral-800 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50">
            <div class="border-b-2 border-neutral-100 p-3 dark:border-neutral-600">
                <h2 class="text-lg font-medium">{{ __('fund.donation_ranking') }}</h2>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-300">
                    {{ __('fund.donation_ranking_tip') }}
                    @if ($year)
                        （{{ $year }}{{ __('fund.year_suffix') }}）
                    @else
                        （{{ __('fund.all_years') }}）
                    @endif
                </p>
                @if ($unlinkedDonationCount > 0)
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                        {{ __('fund.unlinked_donations', ['count' => $unlinkedDonationCount]) }}
                    </p>
                @endif
            </div>

            <div class="p-2 sm:p-4">
                @if ($donationRanking->isEmpty())
                    <p class="py-6 text-center text-neutral-500 dark:text-neutral-300">{{ __('fund.no_ranking') }}</p>
                @else
                    <div class="space-y-2">
                        @foreach ($donationRanking as $index => $row)
                            <div class="flex items-center gap-3 rounded-sm border border-neutral-200 px-3 py-2 dark:border-neutral-600">
                                <div @class([
                                    'flex size-8 shrink-0 items-center justify-center rounded-sm text-sm font-semibold',
                                    'bg-amber-400 text-white' => $index === 0,
                                    'bg-neutral-300 text-neutral-700 dark:bg-neutral-500 dark:text-neutral-100' => $index === 1,
                                    'bg-orange-300 text-orange-900' => $index === 2,
                                    'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300' => $index > 2,
                                ])>
                                    {{ $index + 1 }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    @if ($row->person)
                                        <x-link href="/people/{{ $row->person->id }}" class="font-medium">
                                            {{ $row->person->name }}
                                        </x-link>
                                        <div class="truncate text-xs text-neutral-500 dark:text-neutral-300">
                                            {{ $row->person->lifetime ?: __('fund.lifetime_unknown') }}
                                            · #{{ $row->person->id }}
                                        </div>
                                    @else
                                        <div class="font-medium text-neutral-500">{{ __('fund.unnamed') }} · #{{ $row->person_id }}</div>
                                    @endif
                                </div>

                                <div class="shrink-0 text-end">
                                    <div class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        ¥{{ number_format((float) $row->total_amount, 2) }}
                                    </div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-300">
                                        {{ __('fund.donation_count', ['count' => $row->donation_count]) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- entries --}}
        <div class="rounded-sm bg-white text-neutral-800 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50">
            <div class="flex flex-col gap-3 border-b-2 border-neutral-100 p-3 dark:border-neutral-600 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-medium">{{ __('fund.entries') }}</h2>

                <form method="GET" action="{{ route('family-fund.index') }}" class="flex flex-wrap items-center gap-2">
                    <select name="type" class="rounded-sm border-neutral-300 text-sm dark:border-neutral-500 dark:bg-neutral-800" onchange="this.form.submit()">
                        <option value="" @selected($type === '')>{{ __('fund.all') }}</option>
                        <option value="income" @selected($type === 'income')>{{ __('fund.income') }}</option>
                        <option value="expense" @selected($type === 'expense')>{{ __('fund.expense') }}</option>
                    </select>

                    <select name="year" class="rounded-sm border-neutral-300 text-sm dark:border-neutral-500 dark:bg-neutral-800" onchange="this.form.submit()">
                        <option value="" @selected(! $year)>{{ __('fund.all_years') }}</option>
                        @foreach ($availableYears as $availableYear)
                            <option value="{{ $availableYear }}" @selected((int) $year === (int) $availableYear)>{{ $availableYear }}</option>
                        @endforeach
                        @if ($availableYears->isEmpty())
                            <option value="{{ now()->year }}" @selected((int) $year === (int) now()->year)>{{ now()->year }}</option>
                        @endif
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto p-2 sm:p-4">
                @if ($entries->isEmpty())
                    <p class="py-8 text-center text-neutral-500 dark:text-neutral-300">{{ __('fund.no_entries') }}</p>
                @else
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b dark:border-neutral-500">
                            <tr>
                                <th class="p-2">{{ __('fund.occurred_on') }}</th>
                                <th class="p-2">{{ __('fund.type') }}</th>
                                <th class="p-2">{{ __('fund.category') }}</th>
                                <th class="p-2">{{ __('fund.title') }}</th>
                                <th class="p-2">{{ __('fund.contributor') }}</th>
                                <th class="p-2 text-end">{{ __('fund.amount') }}</th>
                                <th class="p-2 text-end">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entries as $entry)
                                <tr class="border-b align-top dark:border-neutral-500">
                                    <td class="whitespace-nowrap p-2">{{ $entry->occurred_on->format('Y-m-d') }}</td>
                                    <td class="p-2">
                                        <span @class([
                                            'inline-flex rounded-sm px-2 py-0.5 text-xs font-medium',
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => $entry->isIncome(),
                                            'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' => $entry->isExpense(),
                                        ])>
                                            {{ $entry->isIncome() ? __('fund.income') : __('fund.expense') }}
                                        </span>
                                    </td>
                                    <td class="p-2">{{ $entry->categoryLabel() }}</td>
                                    <td class="p-2">
                                        <div class="font-medium">{{ $entry->title }}</div>
                                        @if ($entry->description)
                                            <div class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-300">{{ \Illuminate\Support\Str::limit($entry->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        {{ $entry->contributor ?: '—' }}
                                        @if ($entry->person)
                                            <div class="text-xs text-neutral-500">
                                                <x-link href="/people/{{ $entry->person->id }}">{{ $entry->person->name }}</x-link>
                                            </div>
                                        @endif
                                    </td>
                                    <td @class([
                                        'whitespace-nowrap p-2 text-end font-semibold',
                                        'text-emerald-600 dark:text-emerald-400' => $entry->isIncome(),
                                        'text-red-600 dark:text-red-400' => $entry->isExpense(),
                                    ])>
                                        {{ $entry->isIncome() ? '+' : '-' }}¥{{ number_format((float) $entry->amount, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap p-2 text-end">
                                        @if (auth()->user()->hasPermission('fund:update'))
                                            <x-ts-button href="{{ route('family-fund.edit', $entry) }}" color="yellow" xs title="{{ __('app.edit') }}">
                                                <x-ts-icon icon="tabler.edit" class="size-3.5" />
                                            </x-ts-button>
                                        @endif

                                        @if (auth()->user()->hasPermission('fund:delete'))
                                            <form action="{{ route('family-fund.destroy', $entry) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-ts-button type="submit" color="red" xs title="{{ __('app.confirm_delete') }}" onclick="return confirm('{{ __('app.confirm_delete') }}')">
                                                    <x-ts-icon icon="tabler.trash" class="size-3.5" />
                                                </x-ts-button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4 flex flex-col items-center justify-between gap-2 sm:flex-row">
                        <div class="text-sm text-neutral-500 dark:text-neutral-300">
                            {{ __('app.showing') }} {{ $entries->firstItem() }} {{ __('app.to') }} {{ $entries->lastItem() }} {{ __('app.of') }} {{ $entries->total() }} {{ __('app.entries') }}
                        </div>
                        <div>{{ $entries->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
