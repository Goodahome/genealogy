@props([
    'person',
    'descendants',
    'spousesMap',
    'mainClanIds',
    'affinalRelativesByPerson',
    'level_current' => 0,
    'level_max',
])

@php
    $level_current++;

    $person_sequence = $descendants->firstWhere('id', $person->id)->sequence;

    $descendants_next = $descendants
        ->where('degree', $level_current)
        ->filter(function ($item) use ($person_sequence, $person, $mainClanIds) {
            return str_starts_with($item->sequence, $person_sequence . ',')
                && (int) $item->father_id === (int) $person->id
                && $mainClanIds->contains($item->id);
        })
        ->sortBy([
            ['sex', 'desc'],
            ['dob', 'asc'],
            ['yob', 'asc'],
            ['id', 'asc'],
        ]);

    $spouse = $spousesMap->get($person->id, collect())->first();
    $affinalRelatives = $spouse
        ? $affinalRelativesByPerson->get($person->id, collect())
        : collect();

    $affinalParents = $spouse
        ? $affinalRelatives->filter(function ($relative) use ($spouse) {
            return ($spouse->father_id && (int) $relative->id === (int) $spouse->father_id)
                || ($spouse->mother_id && (int) $relative->id === (int) $spouse->mother_id);
        })->sortBy(fn ($relative) => ($relative->sex ?? '') === 'm' ? 0 : 1)->values()
        : collect();

    $affinalSiblings = $spouse
        ? $affinalRelatives->reject(function ($relative) use ($spouse) {
            return ($spouse->father_id && (int) $relative->id === (int) $spouse->father_id)
                || ($spouse->mother_id && (int) $relative->id === (int) $spouse->mother_id);
        })->values()
        : collect();

    $hasAffinalLineage = $affinalParents->isNotEmpty() || $affinalSiblings->isNotEmpty();
    $showChildren = $level_current < $level_max && $descendants_next->isNotEmpty();

    $affinalPeers = $hasAffinalLineage
        ? collect($affinalSiblings)
            ->push($spouse)
            ->sortBy([
                ['sex', 'desc'],
                ['dob', 'asc'],
                ['yob', 'asc'],
                ['id', 'asc'],
            ])
            ->values()
        : collect();
@endphp

<li class="tree-clan-node">
    @if ($spouse)
        <div
            @class([
                'tree-couple-wrap',
                'tree-couple-wrap--affinal' => $hasAffinalLineage,
            ])
            @if ($hasAffinalLineage)
                x-data="clanAffinalPopover()"
                :class="{ 'tree-couple-wrap--open': open }"
            @endif
        >
            <div class="tree-couple-wrap__main">
                <div class="tree-couple">
                    <x-tree-node.person-card :person="$person" />
                    <span class="tree-couple__bond" title="{{ __('couple.couple') }}">—</span>

                    <div class="tree-couple__spouse">
                        <x-tree-node.person-card :person="$spouse" />

                        @if ($hasAffinalLineage)
                            <button
                                type="button"
                                class="tree-affinal-trigger"
                                title="{{ __('app.affinal_lineage') }}"
                                aria-label="{{ __('app.affinal_lineage') }}"
                                @click.stop="toggle()"
                                @mousedown.stop
                            >
                                <x-ts-icon icon="tabler.binary-tree" class="size-3.5" />
                            </button>
                        @endif
                    </div>
                </div>

                @if ($hasAffinalLineage)
                    <div
                        class="tree-affinal-popover"
                        x-ref="popover"
                        x-show="open"
                        x-cloak
                        x-transition
                        @click.stop
                    >
                        <div class="tree-affinal-popover__header">
                            <span>{{ __('app.affinal_lineage') }}</span>
                            <button
                                type="button"
                                class="tree-affinal-popover__close"
                                @click.stop="close()"
                                @mousedown.stop
                                aria-label="{{ __('app.close') }}"
                            >
                                <x-ts-icon icon="tabler.x" class="size-4" />
                            </button>
                        </div>

                        <div class="tree-affinal-popover__body">
                            <div class="tree-affinal-popover-tree">
                                <ul>
                                    @if ($affinalParents->isNotEmpty())
                                        <li>
                                            <div class="tree-couple">
                                                @foreach ($affinalParents as $parent)
                                                    <x-tree-node.person-card :person="$parent" />
                                                    @if (! $loop->last)
                                                        <span class="tree-couple__bond" title="{{ __('couple.couple') }}">—</span>
                                                    @endif
                                                @endforeach
                                            </div>

                                            @if ($affinalPeers->isNotEmpty())
                                                <ul class="tree-clan-children">
                                                    @foreach ($affinalPeers as $peer)
                                                        <li @class(['tree-affinal-popover__self' => (int) $peer->id === (int) $spouse->id])>
                                                            <x-tree-node.person-card :person="$peer" />
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @else
                                        @foreach ($affinalPeers as $peer)
                                            <li @class(['tree-affinal-popover__self' => (int) $peer->id === (int) $spouse->id])>
                                                <x-tree-node.person-card :person="$peer" />
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($hasAffinalLineage)
                <div
                    class="tree-affinal-popover-spacer"
                    x-show="open"
                    x-cloak
                    :style="`width: ${spacerW}px; height: ${spacerH}px;`"
                ></div>
            @endif
        </div>
    @else
        <x-tree-node.person-card :person="$person" />
    @endif

    @if ($showChildren)
        <ul class="tree-clan-children">
            @foreach ($descendants_next as $descendant)
                <x-tree-node.clan-descendants
                    :person="$descendant"
                    :descendants="$descendants"
                    :spouses-map="$spousesMap"
                    :main-clan-ids="$mainClanIds"
                    :affinal-relatives-by-person="$affinalRelativesByPerson"
                    :level_current="$level_current"
                    :level_max="$level_max"
                />
            @endforeach
        </ul>
    @endif
</li>
