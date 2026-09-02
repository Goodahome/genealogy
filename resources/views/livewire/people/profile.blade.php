<div class="mx-auto flex w-full min-w-0 max-w-sm flex-col rounded-sm bg-white text-neutral-800 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50 sm:max-w-md md:mx-0 md:min-w-100 md:max-w-192">
    <div class="flex flex-col p-2 text-lg font-medium border-b-2 rounded-t h-14 min-h-min border-neutral-100 dark:border-neutral-600 dark:text-neutral-50">
        <div class="flex flex-wrap items-start justify-center gap-2">
            <div class="items-center justify-center flex-1 grow max-w-full align-middle min-w-max">
                {{ __('person.profile') }}
            </div>

            @if (auth()->user()->hasPermission('person:update') or auth()->user()->hasPermission('person:delete'))
                <div class="flex-1 grow min-w-max max-w-min text-end">
                    <x-ts-dropdown icon="tabler.menu-2" position="bottom-end">
                        @if (auth()->user()->hasPermission('person:update'))
                            <a href="/people/{{ $person->id }}/edit-profile">
                                <x-ts-dropdown.items>
                                    <x-ts-icon icon="tabler.id" class="mr-2" />
                                    {{ __('person.edit_profile') }}
                                </x-ts-dropdown.items>
                            </a>

                            <a href="/people/{{ $person->id }}/edit-contact">
                                <x-ts-dropdown.items>
                                    <x-ts-icon icon="tabler.address-book" class="mr-2" />
                                    {{ __('person.edit_contact') }}
                                </x-ts-dropdown.items>
                            </a>

                            <a href="/people/{{ $person->id }}/edit-death">
                                <x-ts-dropdown.items>
                                    <x-ts-icon icon="tabler.grave-2" class="mr-2" />
                                    {{ __('person.edit_death') }}
                                </x-ts-dropdown.items>
                            </a>

                            <hr />
                            <a href="/people/{{ $person->id }}/edit-photos">
                                <x-ts-dropdown.items>
                                    <x-ts-icon icon="tabler.photo" class="mr-2" />
                                    {{ __('person.edit_photos') }}
                                </x-ts-dropdown.items>
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('person:delete') and $person->isDeletable())
                            <hr />

                            <x-ts-dropdown.items separator class="text-red-600! dark:text-red-400!" wire:click="confirm()">
                                <x-ts-icon icon="tabler.trash" class="mr-2" />
                                {{ __('person.delete_person') }}
                            </x-ts-dropdown.items>
                        @endif
                    </x-ts-dropdown>
                </div>
            @endif
        </div>
    </div>

    {{-- image --}}
    <div class="grid justify-center px-2 pt-2">
        <livewire:people.gallery :person="$person" class="max-w-96" />
    </div>

    {{-- lifetime & age --}}
    <div class="flex px-2">
        <div class="grow">
            {!! isset($person->lifetime) ? $person->lifetime : '&nbsp;' !!}
        </div>

        <div class="grow text-end">
            {!! isset($person->age) ? $person->age . ' ' . trans_choice('person.years', $person->age) : '&nbsp;' !!}
        </div>
    </div>

    {{-- data --}}
    <div class="p-2">
        <table class="w-full table-fixed">
            <colgroup>
                <col style="width: 5.5rem" />
                <col />
            </colgroup>
            <tbody>
                <tr class="align-top border-b-2 h-8">
                    <td class="w-[5.5rem] pr-2 border-t-2 border-r-2 align-top">{{ __('person.firstname') }}{{ __('person.surname') }}</td>
                    <td class="pl-2 break-words border-t-2">{{ $person->firstname }}{{ $person->surname }}</td>
                </tr>
                <tr class="align-top border-b-2 h-8">
                    <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.nickname') }}</td>
                    <td class="pl-2 break-words">{{ $person->nickname }}</td>
                </tr>

                <tr class="align-top border-b-2 h-8">
                    <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.sex') }}</td>
                    <td class="pl-2">
                        {{ $person->sex === 'm' ? __('app.male') : __('app.female') }}
                        <x-ts-icon icon="tabler.{{ $person->sex === 'm' ? 'gender-male' : 'gender-female' }}" class="inline-block size-5" />
                    </td>
                </tr>
                <tr class="align-top border-b-2 h-8">
                    <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.dob') }}</td>
                    <td class="pl-2 break-words">
                        @if($person->dob && $person->dob_lunar_year)
                            {{ $person->birth_formatted }} (阳历)<br>
                            {{ $person->lunar_birth_formatted }} (农历)
                        @elseif($person->dob)
                            {{ $person->birth_formatted }} (阳历)
                        @elseif($person->dob_lunar_year)
                            {{ $person->lunar_birth_formatted }} (农历)
                        @endif
                    </td>
                </tr>
                
                @if ($person->isDeceased())
                    <tr class="align-top">
                        <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.dod') }}</td>
                        <td class="pl-2">
                            @if($person->dod && $person->dod_lunar_year)
                                {{ $person->death_formatted }} (阳历)<br>
                                {{ $person->lunar_death_formatted }} (农历)
                            @elseif($person->dod)
                                {{ $person->death_formatted }} (阳历)
                            @elseif($person->dod_lunar_year)
                                {{ $person->lunar_death_formatted }} (农历)
                            @endif
                            @if ($person->isDeathdayToday())
                                <x-ts-icon icon="tabler.cake" class="inline-block size-5 text-red-600 dark:text-red-400" />
                            @endif
                        </td>
                    </tr>
                    <tr class="align-top border-b-2">
                        <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.pod') }}</td>
                        <td class="pl-2 break-words">{{ $person->pod }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.cemetery') }}</td>
                        <td class="pl-2 break-words">{{ $person->getMetadataValue('cemetery_location_name') }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="w-[5.5rem] pr-2 border-b-2 border-r-2 align-top">
                            @if ($person->cemetery_google)
                                <a target="_blank" href="{{ $person->cemetery_google }}">
                                    <x-ts-button color="cyan" class="p-2! mb-2 text-white" title="{{ __('app.show_on_google_maps') }}">
                                        <x-ts-icon icon="tabler.brand-google-maps" class="size-5" />
                                    </x-ts-button>
                                </a>
                            @endif
                        </td>
                        <td class="pl-2 break-words whitespace-pre-line border-b-2">{{ $person->getMetadataValue('cemetery_location_address') }}</td>
                    </tr>
                @else
                    <tr class="align-top">
                        <td class="w-[5.5rem] pr-2 border-b-2 border-r-2 h-8 align-top">
                            {{ __('person.address') }}<br />
                            @if ($person->address)
                                <a target="_blank" href="{{ $person->address_google }}">
                                    <x-ts-button color="cyan" class="p-2! mb-2 text-white" title="{{ __('app.show_on_google_maps') }}">
                                        <x-ts-icon icon="tabler.brand-google-maps" class="size-5" />
                                    </x-ts-button>
                                </a>
                            @endif
                        </td>
                        <td class="pl-2 break-words whitespace-pre-line border-b-2">{{ $person->address }}</td>
                    </tr>
                    <tr class="align-top h-8">
                        <td class="w-[5.5rem] pr-2 border-b-2 border-r-2 align-top">{{ __('person.phone') }}</td>
                        <td class="pl-2 break-words border-b-2">{{ $person->phone }}</td>
                    </tr>
                @endif

                <tr class="align-top border-b-2 h-8">
                    <td class="w-[5.5rem] pr-2 border-r-2 align-top">{{ __('person.summary') }}</td>
                    <td class="pl-2 break-words whitespace-pre-line">{{ $person->summary }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
