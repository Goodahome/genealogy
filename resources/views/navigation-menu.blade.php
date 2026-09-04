<nav x-data="{ open: false }" 
     class="zp-nav zp-wood text-[var(--zp-wood-text-paint)]"
     x-bind:class="{'fixed inset-0 z-50 overflow-y-auto': open, 'relative': !open}"
     x-on:scroll.window="if(open) $event.preventDefault()">
    {{-- primary navigation menu --}}
    <div class="flex min-h-16 items-center gap-3 px-2 md:gap-5">
        <div class="flex grow items-center gap-3 md:gap-5">
            {{-- logo --}}
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" title="{{ __('app.home') }}" class="zp-display">
                    <x-svg.genealogy class="size-12 fill-[var(--zp-wood-text-paint)] hover:fill-[var(--zp-wood-text-active)]" alt="genealogy" />
                </a>
            </div>

            {{-- navigation links (desktop) --}}
            <div class="hidden flex-wrap items-center gap-2 py-2 md:flex">
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                    <x-ts-icon icon="tabler.home" class="mr-1 size-5" />
                    {{ __('app.home') }}
                </x-nav-link>

                <x-nav-link href="{{ route('people.search') }}" :active="request()->routeIs('people.search')">
                    <x-ts-icon icon="tabler.search" class="mr-1 size-5" />
                    {{ __('app.search') }}
                </x-nav-link>

                <x-nav-link href="{{ route('people.birthdays') }}" :active="request()->routeIs('people.birthdays')">
                    <x-ts-icon icon="tabler.cake" class="mr-1 size-5" />
                    {{ __('birthday.birthdays') }}
                </x-nav-link>

                <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                    <x-ts-icon icon="tabler.calendar-event" class="mr-1 size-5" />
                    {{ __('app.events') }}
                </x-nav-link>

                <x-nav-link href="{{ route('ancient-text.index') }}" :active="request()->routeIs('ancient-text.index')">
                    <x-ts-icon icon="tabler.book" class="mr-1 size-5" />
                    {{ __('app.ancient_texts') }}
                </x-nav-link>

                <x-nav-link href="{{ route('people.clan-lineage') }}" :active="request()->routeIs('people.clan-lineage')">
                    <x-ts-icon icon="tabler.binary-tree" class="mr-1 size-5" />
                    {{ __('app.clan_lineage') }}
                </x-nav-link>

                <x-nav-link href="{{ route('family-fund.index') }}" :active="request()->routeIs('family-fund.*')">
                    <x-ts-icon icon="tabler.coin" class="mr-1 size-5" />
                    {{ __('fund.family_fund') }}
                </x-nav-link>

                <x-nav-link href="{{ route('help') }}" :active="request()->routeIs('help')">
                    <x-ts-icon icon="tabler.help" class="mr-1 size-5" />
                    {{ __('app.help') }}
                </x-nav-link>
            </div>
        </div>

        <div class="hidden gap-5 md:flex md:items-center">
            @auth
                {{-- user dropdown --}}
                <div class="relative min-w-max">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-hidden focus:border-gray-300">
                                    <img class="object-cover w-8 h-8 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" title="{{ auth()->user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-sm">
                                    <button type="button" title="{{ auth()->user()->name }}"
                                        class="zp-wood zp-wood-text inline-flex items-center rounded-sm px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                                        {{ auth()->user()->name }}

                                        <svg class="ml-2 -mr-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            {{-- account management --}}
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('app.manage_account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                <x-ts-icon icon="tabler.id" class="inline-block mr-1 size-5" />
                                {{ __('app.my_profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    <x-ts-icon icon="tabler.api" class="inline-block mr-1 size-5" />
                                    {{ __('app.api_tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-[rgba(160,120,55,0.35)]"></div>

                            {{-- authentication --}}
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    <x-ts-icon icon="tabler.logout" class="inline-block mr-1 size-5" />
                                    {{ __('auth.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- teams dropdown --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative min-w-max">
                        <x-dropdown align="right" width="60px">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-sm">
                                    <button type="button"
                                        class="zp-wood zp-wood-text inline-flex items-center rounded-sm px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                                        {{ auth()->user()->currentTeam->name }}

                                        <svg class="ml-2 -mr-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-80">
                                    {{-- teams switcher --}}
                                    @if (auth()->user()->allTeams()->count() > 1)
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('team.switch') }}
                                        </div>

                                        @foreach (auth()->user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach

                                        <hr />
                                    @endif

                                    {{-- teams management --}}
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('team.manage') }}
                                    </div>

                                    {{-- teams settings --}}
                                    <x-dropdown-link href="{{ route('teams.show', auth()->user()->currentTeam->id) }}">
                                        <x-ts-icon icon="tabler.droplet-cog" class="inline-block mr-1 size-5" />
                                        {{ __('team.settings') }}
                                    </x-dropdown-link>

                                    {{-- create / import / export team --}}
                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            <x-ts-icon icon="tabler.droplet-plus" class="inline-block mr-1 size-5" />
                                            {{ __('team.create') }}
                                        </x-dropdown-link>

                                        <hr />

                                        {{-- gedcom --}}
                                        <x-dropdown-link href="{{ route('gedcom.importteam') }}">
                                            <x-ts-icon icon="tabler.droplet-up" class="inline-block mr-1 size-5" />
                                            {{ __('gedcom.gedcom_import') }}
                                        </x-dropdown-link>

                                        <x-dropdown-link href="{{ route('gedcom.exportteam') }}">
                                            <x-ts-icon icon="tabler.droplet-down" class="inline-block mr-1 size-5" />
                                            {{ __('gedcom.gedcom_export') }}
                                        </x-dropdown-link>
                                    @endcan
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif
            @else
                <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')" class="whitespace-nowrap">
                    <x-ts-icon icon="tabler.login-2" class="inline-block mr-1 size-5" />
                    {{ __('auth.login') }}
                </x-nav-link>

                <x-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')" class="whitespace-nowrap">
                    <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1 size-5" />
                    {{ __('auth.register') }}
                </x-nav-link>

                <x-set.language />
            @endauth
        </div>

         {{-- theme switch and offcanvas (desktop) --}}
        <div class="hidden flex-col place-items-center md:flex">
            <div class="hidden space-x-6 min-h-8 md:flex md:items-center md:ml-5">
                <x-ts-theme-switch only-icons />
            </div>
            <div class="hidden space-x-6 min-h-8 md:flex md:items-center md:ml-5">
                <x-set.offcanvas />
            </div>
        </div>

        {{-- hamburger (mobile) --}}
        <div class="ml-auto flex items-center md:hidden">
            <button @click="open = ! open"
                class="zp-wood zp-wood-text inline-flex items-center justify-center rounded-sm p-2 transition duration-150 ease-in-out">
                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- responsive navigation menu --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden border-t border-[rgba(160,120,55,0.35)] md:hidden">
        {{-- primary navigation links (mobile) --}}
        <div class="space-y-1 pb-2">
            <x-nav-link-responsive href="{{ route('home') }}" :active="request()->routeIs('home')">
                <x-ts-icon icon="tabler.home" class="inline-block mr-1 size-5" />
                {{ __('app.home') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('people.search') }}" :active="request()->routeIs('people.search')">
                <x-ts-icon icon="tabler.search" class="inline-block mr-1 size-5" />
                {{ __('app.search') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('people.birthdays') }}" :active="request()->routeIs('people.birthdays')">
                <x-ts-icon icon="tabler.cake" class="inline-block mr-1 size-5" />
                {{ __('birthday.birthdays') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                <x-ts-icon icon="tabler.calendar-event" class="inline-block mr-1 size-5" />
                {{ __('app.events') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('ancient-text.index') }}" :active="request()->routeIs('ancient-text.index')">
                <x-ts-icon icon="tabler.book" class="inline-block mr-1 size-5" />
                {{ __('app.ancient_texts') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('people.clan-lineage') }}" :active="request()->routeIs('people.clan-lineage')">
                <x-ts-icon icon="tabler.binary-tree" class="inline-block mr-1 size-5" />
                {{ __('app.clan_lineage') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('family-fund.index') }}" :active="request()->routeIs('family-fund.*')">
                <x-ts-icon icon="tabler.coin" class="inline-block mr-1 size-5" />
                {{ __('fund.family_fund') }}
            </x-nav-link-responsive>

            <x-nav-link-responsive href="{{ route('help') }}" :active="request()->routeIs('help')">
                <x-ts-icon icon="tabler.help" class="inline-block mr-1 size-5" />
                {{ __('app.help') }}
            </x-nav-link-responsive>
        </div>

        @guest
            <div class="space-y-1 border-t border-[rgba(160,120,55,0.35)] pt-2">
                <x-nav-link-responsive href="{{ route('login') }}" :active="request()->routeIs('login')">
                    <x-ts-icon icon="tabler.login-2" class="inline-block mr-1 size-5" />
                    {{ __('auth.login') }}
                </x-nav-link-responsive>

                <x-nav-link-responsive href="{{ route('register') }}" :active="request()->routeIs('register')">
                    <x-ts-icon icon="tabler.user-plus" class="inline-block mr-1 size-5" />
                    {{ __('auth.register') }}
                </x-nav-link-responsive>
            </div>

            {{-- responsive settings options --}}
            <div class="border-t border-[rgba(160,120,55,0.35)] pb-2 pl-4 pt-2">
                <x-set.language />
            </div>
            <div class="border-t border-[rgba(160,120,55,0.35)] pb-2 pl-4 pt-2">
                <x-ts-theme-switch only-icons />
            </div>
            <div class="border-t border-[rgba(160,120,55,0.35)] pb-2 pl-4 pt-2">
                <x-set.offcanvas />
            </div>
        @endguest

        @auth
            <div class="border-t border-[rgba(160,120,55,0.35)] pb-2 pt-2">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="mr-3 shrink-0">
                            <img class="object-cover w-10 h-10 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="zp-display text-base font-medium text-[var(--zp-wood-text-paint)]">{{ auth()->user()->name }}</div>
                        <div class="text-sm font-medium text-[var(--zp-wood-text-paint-deep)]">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="space-y-1">
                    {{-- account management --}}
                    <x-nav-link-responsive href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        <x-ts-icon icon="tabler.id" class="inline-block mr-1 size-5" />
                        {{ __('app.my_profile') }}
                    </x-nav-link-responsive>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-nav-link-responsive href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                            <x-ts-icon icon="tabler.api" class="inline-block mr-1 size-5" />
                            {{ __('app.api_tokens') }}
                        </x-nav-link-responsive>
                    @endif

                    {{-- authentication --}}
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-nav-link-responsive href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            <x-ts-icon icon="tabler.logout" class="inline-block mr-1 size-5" />
                            {{ __('auth.logout') }}
                        </x-nav-link-responsive>
                    </form>

                    {{-- team switcher --}}
                    @if (auth()->user()->allTeams()->count() > 1)
                        <div class="border-t border-[rgba(160,120,55,0.35)]"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('team.switch') }}
                        </div>

                        @foreach (auth()->user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="nav-link-responsive" />
                        @endforeach
                    @endif

                    {{-- team management --}}
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="border-t border-[rgba(160,120,55,0.35)]"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('team.manage') }}
                        </div>

                        {{-- team settings --}}
                        <x-nav-link-responsive href="{{ route('teams.show', auth()->user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                            <x-ts-icon icon="tabler.droplet-cog" class="inline-block mr-1 size-5" />
                            {{ __('team.settings') }}
                        </x-nav-link-responsive>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-nav-link-responsive href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                                <x-ts-icon icon="tabler.droplet-plus" class="inline-block mr-1 size-5" />
                                {{ __('team.create') }}
                            </x-nav-link-responsive>

                            {{-- gedcom --}}
                            <x-nav-link-responsive href="{{ route('gedcom.importteam') }}" :active="request()->routeIs('gedcom.importteam')">
                                <x-ts-icon icon="tabler.droplet-up" class="inline-block mr-1 size-5" />
                                {{ __('gedcom.gedcom_import') }}
                            </x-nav-link-responsive>

                            <x-nav-link-responsive href="{{ route('gedcom.exportteam') }}" :active="request()->routeIs('gedcom.exportteam')">
                                <x-ts-icon icon="tabler.droplet-down" class="inline-block mr-1 size-5" />
                                {{ __('gedcom.gedcom_export') }}
                            </x-nav-link-responsive>
                        @endcan
                    @endif
                </div>
            </div>

            {{-- responsive settings options --}}
            <div class="py-2 pl-4 border-t border-[rgba(160,120,55,0.35)]">
                <x-ts-theme-switch only-icons />
            </div>
            <div class="py-1 pl-4 border-t border-[rgba(160,120,55,0.35)]">
                <x-set.offcanvas />
            </div>
        @endauth
    </div>
</nav>
