<nav x-data="{ open: false }" class="bg-base-200 border-b border-base-300 medieval-nav">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('catalogo') }}" class="flex items-center gap-3">
                        <x-application-mark class="block h-9 w-auto" />
                        <span style="font-family: 'Cinzel Decorative', serif; color: #d4af37; font-size: 1.1rem; font-weight: 700; letter-spacing: 0.06em; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                            Biblioteca
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            📜 {{ __('Painel') }}
                        </x-nav-link>
                    @endauth
                    <x-nav-link href="{{ route('catalogo') }}" :active="request()->routeIs('catalogo')">
                        📖 Catálogo
                    </x-nav-link>
                    @auth
                        <x-nav-link href="{{ route('requisicoes') }}" :active="request()->routeIs('requisicoes')">
                            📦 Requisições
                        </x-nav-link>
                        @if(auth()->user()?->isAdmin())
                            <x-nav-link href="{{ route('livros') }}" :active="request()->routeIs('livros')">
                                📚 Acervo de Livros
                            </x-nav-link>
                            <x-nav-link href="{{ route('autores') }}" :active="request()->routeIs('autores')">
                                ✒️ Escribas & Autores
                            </x-nav-link>
                            <x-nav-link href="{{ route('editoras') }}" :active="request()->routeIs('editoras')">
                                🏰 Casas Editoras
                            </x-nav-link>
                            <x-nav-link href="{{ route('utilizadores') }}" :active="request()->routeIs('utilizadores')">
                                👤 Utilizadores
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- Teams Dropdown -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-base-content/60 bg-base-200 hover:text-base-content/80 focus:outline-none focus:bg-base-200 active:bg-base-200 transition ease-in-out duration-150">
                                            {{ Auth::user()->currentTeam->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="w-60">
                                        <!-- Team Management -->
                                        <div class="block px-4 py-2 text-xs" style="color: #8b5a2b; font-family: 'Cinzel', serif;">
                                            {{ __('Gerir Guilda') }}
                                        </div>

                                        <!-- Team Settings -->
                                        <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                            {{ __('Definições da Guilda') }}
                                        </x-dropdown-link>

                                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                            <x-dropdown-link href="{{ route('teams.create') }}">
                                                {{ __('Fundar Nova Guilda') }}
                                            </x-dropdown-link>
                                        @endcan

                                        <!-- Team Switcher -->
                                        @if (Auth::user()->allTeams()->count() > 1)
                                            <div class="border-t border-base-300"></div>

                                            <div class="block px-4 py-2 text-xs" style="color: #8b5a2b; font-family: 'Cinzel', serif;">
                                                {{ __('Trocar de Guilda') }}
                                            </div>

                                            @foreach (Auth::user()->allTeams() as $team)
                                                <x-switchable-team :team="$team" />
                                            @endforeach
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-base-300 transition" style="border-color: rgba(139,90,43,0.4);">
                                        <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" style="box-shadow: 0 0 8px rgba(0,0,0,0.6);" />
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-base-200 focus:outline-none focus:bg-base-200 active:bg-base-200 transition ease-in-out duration-150" style="font-family: 'Cinzel', serif; color: #e8dcca; letter-spacing: 0.04em;" onmouseover="this.style.color='#d4af37'; this.style.textShadow='0 0 8px rgba(212,175,55,0.15)';" onmouseout="this.style.color='#e8dcca'; this.style.textShadow='none';">
                                            {{ Auth::user()->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs" style="color: #8b5a2b; font-family: 'Cinzel', serif;">
                                    {{ __('Tua Conta') }}
                                </div>

                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    ⚔️ {{ __('Perfil do Guardião') }}
                                </x-dropdown-link>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                        🔑 {{ __('Chaves de Acesso') }}
                                    </x-dropdown-link>
                                @endif

                                <div class="border-t border-base-300"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link href="{{ route('logout') }}"
                                             @click.prevent="$root.submit();">
                                        🚪 {{ __('Abandonar o Salão') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a class="btn btn-sm" href="{{ route('login') }}">{{ __('Entrar') }}</a>
                        @if (Route::has('register'))
                            <a class="btn btn-sm btn-primary" href="{{ route('register') }}">{{ __('Registar') }}</a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md hover:text-base-content/60 hover:bg-base-100 focus:outline-none focus:bg-base-100 focus:text-base-content/60 transition duration-150 ease-in-out" style="color: #d4af37;">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    📜 {{ __('Painel') }}
                </x-responsive-nav-link>
            @endauth
            <x-responsive-nav-link href="{{ route('catalogo') }}" :active="request()->routeIs('catalogo')">
                📖 Catálogo
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link href="{{ route('requisicoes') }}" :active="request()->routeIs('requisicoes')">
                    📦 Requisições
                </x-responsive-nav-link>
                @if(auth()->user()?->isAdmin())
                    <x-responsive-nav-link href="{{ route('livros') }}" :active="request()->routeIs('livros')">
                        📚 Acervo de Livros
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('autores') }}" :active="request()->routeIs('autores')">
                        ✒️ Escribas & Autores
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('editoras') }}" :active="request()->routeIs('editoras')">
                        🏰 Casas Editoras
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('utilizadores') }}" :active="request()->routeIs('utilizadores')">
                        👤 Utilizadores
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                    {{ __('Entrar') }}
                </x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                        {{ __('Registar') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-base-300">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 me-3">
                            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" style="border: 1px solid rgba(139,90,43,0.4); box-shadow: 0 0 6px rgba(0,0,0,0.5);" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base" style="font-family: 'Cinzel', serif; color: #e8dcca;">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm" style="color: rgba(232,220,202,0.72);">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Account Management -->
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        ⚔️ {{ __('Perfil do Guardião') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                            🔑 {{ __('Chaves de Acesso') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link href="{{ route('logout') }}"
                                       @click.prevent="$root.submit();">
                            🚪 {{ __('Abandonar o Salão') }}
                        </x-responsive-nav-link>
                    </form>

                    <!-- Team Management -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="border-t border-base-300"></div>

                        <div class="block px-4 py-2 text-xs" style="color: #8b5a2b; font-family: 'Cinzel', serif;">
                            {{ __('Gerir Guilda') }}
                        </div>

                        <!-- Team Settings -->
                        <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                            {{ __('Definições da Guilda') }}
                        </x-responsive-nav-link>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                                {{ __('Fundar Nova Guilda') }}
                            </x-responsive-nav-link>
                        @endcan

                        <!-- Team Switcher -->
                        @if (Auth::user()->allTeams()->count() > 1)
                            <div class="border-t border-base-300"></div>

                            <div class="block px-4 py-2 text-xs" style="color: #8b5a2b; font-family: 'Cinzel', serif;">
                                {{ __('Trocar de Guilda') }}
                            </div>

                            @foreach (Auth::user()->allTeams() as $team)
                                <x-switchable-team :team="$team" component="responsive-nav-link" />
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        @endauth
    </div>
</nav>
