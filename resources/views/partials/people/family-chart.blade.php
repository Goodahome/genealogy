{{-- 家族结构卡片（简介侧栏开关 / 独立页共用） --}}
@php
    $chartHref = static fn (int $id): string => route('people.show', $id) . '?panel=chart';
    $compact = $compact ?? false;
@endphp

<div @class([
    'print:break-before-page flex flex-col overflow-x-auto text-[#2a2118]',
    'zp-family-chart-compact' => $compact,
    'min-w-[48rem] rounded-sm zp-paper-card' => ! $compact,
])>
    @unless ($compact)
        <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100">
            <div class="flex flex-wrap items-start justify-center gap-2">
                <div class="flex-1 grow max-w-full min-w-max">
                    {{ __('app.family_chart') }}
                </div>

                <div class="flex-1 grow max-w-full min-w-max text-end">
                    <x-ts-icon icon="tabler.social" class="inline-block size-5" />
                </div>
            </div>
        </div>
    @endunless

    {{-- grandparents --}}
    <div class="flex flex-row">
        <div class="p-2 border basis-1/5 text-end">{{ trans('person.grandfather') }} & {{ trans('person.grandmother') }} :</div>

        <div class="items-center p-2 text-center border basis-1/5">
            @if ($person->father and $person->father->father)
                <x-link href="{{ $chartHref($person->father->father->id) }}" @class([
                    'text-red-600' => $person->father->father->isDeceased(),
                ])>
                    {{ $person->father->father->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->father->father->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
            @else
                <x-ts-icon icon="tabler.user-question" class="inline-block" />
            @endif
        </div>
        <div class="p-2 text-center border basis-1/5">
            @if ($person->father and $person->father->mother)
                <x-link href="{{ $chartHref($person->father->mother->id) }}" @class([
                    'text-red-600' => $person->father->mother->isDeceased(),
                ])>
                    {{ $person->father->mother->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->father->mother->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
            @else
                <x-ts-icon icon="tabler.user-question" class="inline-block" />
            @endif
        </div>
        <div class="p-2 text-center border basis-1/5">
            @if ($person->mother and $person->mother->father)
                <x-link href="{{ $chartHref($person->mother->father->id) }}" @class([
                    'text-red-600' => $person->mother->father->isDeceased(),
                ])>
                    {{ $person->mother->father->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->mother->father->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
            @else
                <x-ts-icon icon="tabler.user-question" class="inline-block" />
            @endif
        </div>
        <div class="p-2 text-center border basis-1/5">
            @if ($person->mother and $person->mother->mother)
                <x-link href="{{ $chartHref($person->mother->mother->id) }}" @class([
                    'text-red-600' => $person->mother->mother->isDeceased(),
                ])>
                    {{ $person->mother->mother->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->mother->mother->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
            @else
                <x-ts-icon icon="tabler.user-question" class="inline-block" />
            @endif
        </div>
    </div>

    {{-- uncles/aunts & cousins --}}
    <div class="flex flex-row">
        <div class="p-2 font-medium border basis-1/5 text-end">
            {{ trans('person.uncles') }} & {{ trans('person.aunts') }} :<br />
            {{ trans('person.cousins') }} :
        </div>

        <div class="p-2 border basis-2/5">
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @if ($person->father)
                    @foreach ($person->father->siblings(true) as $index => $sibling)
                        <div>
                            {{ $index + 1 }}.
                            <x-link href="{{ $chartHref($sibling->id) }}" @class([
                                'text-red-600' => $sibling->isDeceased(),
                            ])>
                                {{ $sibling->name }}
                            </x-link>
                            <x-ts-icon icon="tabler.{{ $sibling->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />

                            <ol class="ml-8 list-decimal">
                                @foreach ($sibling->children as $child)
                                    <li>
                                        <x-link href="{{ $chartHref($child->id) }}" @class([
                                            'text-red-600' => $child->isDeceased(),
                                        ])>
                                            {{ $child->name }}
                                        </x-link>
                                        <x-ts-icon icon="tabler.{{ $child->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="p-2 border basis-2/5">
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @if ($person->mother)
                    @foreach ($person->mother->siblings(true) as $index => $sibling)
                        <div>
                            {{ $index + 1 }}.
                            <x-link href="{{ $chartHref($sibling->id) }}" @class([
                                'text-red-600' => $sibling->isDeceased(),
                            ])>
                                {{ $sibling->name }}
                            </x-link>
                            <x-ts-icon icon="tabler.{{ $sibling->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />

                            <ol class="ml-8 list-decimal">
                                @foreach ($sibling->children as $child)
                                    <li>
                                        <x-link href="{{ $chartHref($child->id) }}" @class([
                                            'text-red-600' => $child->isDeceased(),
                                        ])>
                                            {{ $child->name }}
                                        </x-link>
                                        <x-ts-icon icon="tabler.{{ $child->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- parents --}}
    <div class="flex flex-row">
        <div class="p-2 font-medium border basis-1/5 text-end">{{ trans('person.father') }} & {{ trans('person.mother') }} :</div>

        <div class="p-2 text-center border basis-2/5">
            @if ($person->father)
                <x-link href="{{ $chartHref($person->father->id) }}" @class([
                    'text-red-600' => $person->father->isDeceased(),
                ])>
                    {{ $person->father->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->father->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
            @else
                <x-ts-icon icon="tabler.user-question" class="inline-block" />
            @endif
        </div>
        <div class="p-2 text-center border basis-2/5">
            @if ($person->mother)
                <x-link href="{{ $chartHref($person->mother->id) }}" @class([
                    'text-red-600' => $person->mother->isDeceased(),
                ])>
                    {{ $person->mother->name }}
                </x-link>
                <x-ts-icon icon="tabler.{{ $person->mother->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
            @else
                <x-ts-icon icon="tabler.user-question" class="inline-block" />
            @endif
        </div>
    </div>

    {{-- person --}}
    <div class="flex flex-row">
        <div class="p-2 font-medium border basis-1/5 text-end"></div>

        <div class="p-2 text-center border basis-4/5">
            <x-link href="{{ $chartHref($person->id) }}" @class([
                'text-red-600' => $person->isDeceased(),
            ])>
                {{ $person->name }}
            </x-link>
            <x-ts-icon icon="tabler.{{ $person->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
        </div>
    </div>

    {{-- children & grandchildren --}}
    <div class="flex flex-row">
        <div class="p-2 font-medium border basis-1/5 text-end">
            {{ trans('person.children') }} :<br />
            {{ trans('person.grandchildren') }} :
        </div>

        <div class="p-2 border basis-4/5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($person->children_with_children as $index => $child)
                    <div>
                        {{ $index + 1 }}.
                        <x-link href="{{ $chartHref($child->id) }}" @class([
                            'text-red-600' => $child->isDeceased(),
                        ])>
                            {{ $child->name }}
                        </x-link>
                        <x-ts-icon icon="tabler.{{ $child->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />

                        <ol class="ml-8 list-decimal">
                            @foreach ($child->children as $grandchild)
                                <li>
                                    <x-link href="{{ $chartHref($grandchild->id) }}" @class([
                                        'text-red-600' => $grandchild->isDeceased(),
                                    ])>
                                        {{ $grandchild->name }}
                                    </x-link>
                                    <x-ts-icon icon="tabler.{{ $grandchild->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- siblings & nephews/nieces --}}
    <div class="flex flex-row">
        <div class="p-2 font-medium border basis-1/5 text-end">
            {{ trans('person.siblings') }} :<br />
            {{ trans('person.nephews') }} & {{ trans('person.nieces') }} :
        </div>

        <div class="p-2 border basis-4/5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($person->siblings(true) as $index => $sibling)
                    <div>
                        {{ $index + 1 }}.
                        <x-link href="{{ $chartHref($sibling->id) }}" @class([
                            'text-red-600' => $sibling->isDeceased(),
                        ])>
                            {{ $sibling->name }}
                        </x-link>
                        <x-ts-icon icon="tabler.{{ $sibling->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />

                        <ol class="ml-8 list-decimal">
                            @foreach ($sibling->children as $child)
                                <li>
                                    <x-link href="{{ $chartHref($child->id) }}" @class([
                                        'text-red-600' => $child->isDeceased(),
                                    ])>
                                        {{ $child->name }}
                                    </x-link>
                                    <x-ts-icon icon="tabler.{{ $child->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
