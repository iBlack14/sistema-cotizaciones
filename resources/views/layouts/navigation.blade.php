<nav x-data="{ open: false }" class="bg-vc-purple rounded-b-[15px] shadow-lg border-b border-white/10 z-[100] relative">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> <!-- Increased height for premium feel -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <!-- White Logo -->
                        <!-- White Logo -->
                        <x-application-logo class="block h-16 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links + User -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-8 sm:-my-px">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('quotations.create')" :active="request()->routeIs('quotations.create')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        {{ __('Nueva Cotización') }}
                    </x-nav-link>
                    <x-nav-link :href="route('quotations.index')" :active="request()->routeIs('quotations.index')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        {{ __('Seguimiento') }}
                    </x-nav-link>
                    <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        {{ __('Mensajes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.index')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        {{ __('Ajustes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('soporte.index')" :active="request()->routeIs('soporte.*')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z" />
                            </svg>
                            <span>{{ __('Soporte') }}</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('quotations.notes')" :active="request()->routeIs('quotations.notes')"
                        class="text-white hover:text-gray-200 border-transparent hover:border-white">
                        {{ __('Notas') }}
                    </x-nav-link>



                    <!-- Settings Dropdown (Administrador) -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar Sesion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden bg-white/95 backdrop-blur-xl rounded-b-[15px] shadow-xl">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quotations.create')" :active="request()->routeIs('quotations.create')">
                {{ __('Nueva Cotización') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quotations.index')" :active="request()->routeIs('quotations.index')">
                {{ __('Seguimiento') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                {{ __('Mensajes') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('soporte.index')" :active="request()->routeIs('soporte.*')">
                {{ __('Soporte') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quotations.notes')" :active="request()->routeIs('quotations.notes')">
                {{ __('Notas') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
