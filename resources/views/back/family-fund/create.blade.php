@php
    use App\Models\FamilyFundEntry;
@endphp

@section('title')
    &vert; {{ __('fund.add_entry') }}
@endsection

<x-app-layout>
    <x-slot name="heading">
        {{ __('fund.add_entry') }}
    </x-slot>

    <div class="w-full p-2 sm:p-4">
        <div class="mx-auto max-w-2xl rounded-sm bg-white p-4 text-neutral-800 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50 sm:p-6">
            <form action="{{ route('family-fund.store') }}" method="POST" x-data="{
                type: '{{ old('type', 'income') }}',
                categories: {
                    income: @js(FamilyFundEntry::incomeCategories()),
                    expense: @js(FamilyFundEntry::expenseCategories()),
                },
                category: '{{ old('category', 'donation') }}',
                people: @js($peopleOptions),
                personId: @js(old('person_id', '')),
                personSearch: '',
                contributor: @js(old('contributor', '')),
                get isDonation() {
                    return this.type === 'income' && this.category === 'donation';
                },
                get filteredPeople() {
                    const q = this.personSearch.trim().toLowerCase();
                    if (!q) return this.people;
                    return this.people.filter(p => (p.label || '').toLowerCase().includes(q) || String(p.id) === q);
                },
                syncCategory() {
                    const keys = Object.keys(this.categories[this.type] || {});
                    if (!keys.includes(this.category)) {
                        this.category = keys[0] || '';
                    }
                },
                onPersonChange() {
                    const person = this.people.find(p => String(p.id) === String(this.personId));
                    if (person) {
                        this.contributor = person.name || this.contributor;
                    }
                }
            }" x-init="syncCategory()">
                @csrf

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium">{{ __('fund.type') }}</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="type" value="income" x-model="type" @change="syncCategory()" class="rounded-sm">
                            {{ __('fund.income') }}
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="type" value="expense" x-model="type" @change="syncCategory()" class="rounded-sm">
                            {{ __('fund.expense') }}
                        </label>
                    </div>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="category" class="mb-1 block text-sm font-medium">{{ __('fund.category') }}</label>
                    <select id="category" name="category" x-model="category" class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800" required>
                        <template x-for="(label, key) in categories[type]" :key="key">
                            <option :value="key" x-text="label"></option>
                        </template>
                    </select>
                    @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="amount" class="mb-1 block text-sm font-medium">{{ __('fund.amount') }}</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}"
                            class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800" required>
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="occurred_on" class="mb-1 block text-sm font-medium">{{ __('fund.occurred_on') }}</label>
                        <input type="date" name="occurred_on" id="occurred_on" value="{{ old('occurred_on', now()->toDateString()) }}"
                            class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800" required>
                        @error('occurred_on') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="title" class="mb-1 block text-sm font-medium">{{ __('fund.title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" maxlength="255"
                        class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800" required>
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4" x-show="isDonation" x-cloak>
                    <label for="person_id" class="mb-1 block text-sm font-medium">
                        {{ __('fund.linked_person') }} <span class="text-red-500">*</span>
                    </label>
                    <p class="mb-2 text-xs text-neutral-500 dark:text-neutral-300">{{ __('fund.linked_person_tip') }}</p>
                    <input type="search" x-model="personSearch" placeholder="{{ __('fund.search_person') }}"
                        class="mb-2 block w-full rounded-sm border-neutral-300 text-sm dark:border-neutral-500 dark:bg-neutral-800">
                    <select id="person_id" name="person_id" x-model="personId" @change="onPersonChange()"
                        class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800"
                        :required="isDonation">
                        <option value="">{{ __('fund.select_person') }}</option>
                        <template x-for="person in filteredPeople" :key="person.id">
                            <option :value="person.id" x-text="person.label"></option>
                        </template>
                    </select>
                    @error('person_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="contributor" class="mb-1 block text-sm font-medium">{{ __('fund.contributor') }}</label>
                    <input type="text" name="contributor" id="contributor" x-model="contributor" maxlength="255"
                        class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800">
                    @error('contributor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="mb-1 block text-sm font-medium">{{ __('fund.description') }}</label>
                    <textarea name="description" id="description" rows="4"
                        class="block w-full rounded-sm border-neutral-300 dark:border-neutral-500 dark:bg-neutral-800">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <x-ts-button href="{{ route('family-fund.index') }}" color="secondary" class="text-sm">
                        {{ __('app.back') }}
                    </x-ts-button>
                    <x-ts-button type="submit" color="primary" class="text-sm">
                        {{ __('app.save') }}
                    </x-ts-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
