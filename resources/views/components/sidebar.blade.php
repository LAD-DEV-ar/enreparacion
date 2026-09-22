<div x-data="{ open: false }">


    {{-- =========================================
         TOPBAR MÓVIL (solo visible en < lg)
    ========================================== --}}

    <div class="lg:hidden fixed top-0 inset-x-0 z-40 flex h-14 items-center justify-between border-b border-border bg-background px-4">

        {{-- Botón hamburguesa --}}
        <button
            @click="open = true"
            class="flex h-10 w-10 items-center justify-center rounded-xl text-text-secondary hover:bg-surface-hover hover:text-text-primary transition-colors cursor-pointer"
            aria-label="Abrir menú"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        {{-- Logo centrado --}}
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2">
            <img src="{{ asset('favicon.svg') }}" alt="EnReparacion" class="h-8 w-8 object-contain">
            <span class="text-lg font-bold tracking-tight">
                En<span class="text-primary">Reparación</span>
            </span>
        </a>

        {{-- Espaciador derecho para centrar el logo --}}
        <div class="h-10 w-10"></div>

    </div>


    {{-- =========================================
         BACKDROP (oscuro al abrir el drawer)
    ========================================== --}}

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="lg:hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
    ></div>


    {{-- =========================================
         ASIDE: fijo en desktop / slide-over en móvil
    ========================================== --}}

    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-56 border-r border-border bg-background
               transition-transform duration-300 ease-in-out
               lg:translate-x-0"
    >
        <div class="flex h-full flex-col">


            {{-- =========================================
                 BOTÓN DE CIERRE (solo móvil)
            ========================================== --}}

            <button
                @click="open = false"
                class="lg:hidden absolute top-3 right-3 flex h-8 w-8 items-center justify-center rounded-lg text-text-secondary hover:bg-surface-hover hover:text-text-primary transition-colors cursor-pointer"
                aria-label="Cerrar menú"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>


            {{-- =========================================
                 LOGO
            ========================================== --}}

            <a href="{{ route('dashboard.index') }}" class="flex flex-col items-center p-8">
        
                {{-- Logo --}}
                <div
                    x-data="{ loaded: false }"
                    x-init="$nextTick(() => { if ($refs.logo?.complete) loaded = true })"
                    class="relative h-10 w-10 overflow-hidden rounded-md"
                >
                    <div
                        x-show="!loaded"
                        x-cloak
                        class="absolute inset-0 rounded-md bg-gray-500/20"
                    >
                        <div class="shimmer absolute inset-0 -translate-x-full"></div>
                    </div>

                    <img
                        x-ref="logo"
                        src="{{ asset('/favicon.svg') }}"
                        title="EnReparacion"
                        alt="EnReparacion"
                        class="relative h-10 w-10 object-contain transition-opacity duration-200"
                        :class="loaded ? 'opacity-100' : 'opacity-0'"
                        @load="loaded = true"
                        x-on:error="loaded = true"
                    >
                </div>

                <span class="flex text-2xl font-bold tracking-tight">
                    En <span class="text-primary">Reparación</span>
                </span>

            </a>


            {{-- =========================================
                 NAVEGACIÓN
            ========================================== --}}

            <nav class="flex-1 px-4">

                {{-- Inicio --}}
                <a
                    href="{{ route('dashboard.index') }}"
                    @click="open = false"
                    class="flex h-16 items-center gap-4 rounded-2xl {{ request()->routeIs('dashboard.*') ? 'bg-surface-hover' : 'transition-colors hover:bg-surface-hover' }} px-5"
                >

                    <span class="flex size-8 shrink-0 items-center justify-center text-primary">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="size-full"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m2.25 12 8.954-8.954a1.125 1.125 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-6.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"
                            />
                        </svg>
                    </span>

                    <span class="text-base font-semibold leading-none">
                        Inicio
                    </span>

                </a>


                {{-- Clientes --}}
                <a
                    href="{{ route('clientes.index') }}"
                    @click="open = false"
                    class="mt-3 flex h-16 items-center gap-4 rounded-2xl px-5 {{ request()->routeIs('clientes.*') ? 'bg-surface-hover' : 'transition-colors hover:bg-surface-hover' }}"
                >

                    <span class="flex size-8 shrink-0 items-center justify-center text-primary">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="size-full"
                        >
                            <g transform="translate(12 12) scale(1.12) translate(-12 -12)">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="9" cy="7" r="4" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M22 21v-2a4 4 0 0 0-3-3.87"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16 3.13a4 4 0 0 1 0 7.75"
                                />
                            </g>
                        </svg>
                    </span>

                    <span class="text-base font-semibold leading-none">
                        Clientes
                    </span>

                </a>

                {{-- Reparaciones --}}
                <a
                    href="{{ route('reparaciones.index') }}"
                    @click="open = false"
                    class="mt-3 flex h-16 items-center gap-4 rounded-2xl px-5 {{ request()->routeIs('reparaciones.*') ? 'bg-surface-hover' : 'transition-colors hover:bg-surface-hover' }}"
                >

                    <span class="flex size-8 shrink-0 items-center justify-center overflow-visible text-primary">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="size-full overflow-visible"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M7 10h3V7L6.5 3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1-3 3l-6-6a6 6 0 0 1-8-8L7 10Z"
                            />
                        </svg>
                    </span>

                    <span class="text-base font-semibold leading-none">
                        Reparaciones
                    </span>

                </a>

            </nav>


            {{-- =========================================
                 CUENTA
            ========================================== --}}

            <div class="px-8 pb-10">

                <a
                    href="{{ route('cuenta.index') }}"
                    @click="open = false"
                    class="flex items-center gap-3 rounded-2xl {{ request()->routeIs('cuenta.*') ? 'bg-surface-hover' : 'transition-colors hover:bg-surface-hover' }}"
                >

                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-hover"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-8 w-8 text-primary"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"
                            />
                        </svg>

                    </div>

                    <span class="font-semibold">
                        Cuenta
                    </span>

                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('login.logout') }}" class="mt-2">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-2xl px-4 py-4 text-left transition-colors hover:bg-surface-hover cursor-pointer"
                    >

                        <div class="flex h-8 w-8 shrink-0 items-center justify-center text-muted-foreground text-text-secondary">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="size-full"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"
                                />
                            </svg>
                        </div>

                        <span class="font-semibold text-sm text-muted-foreground text-text-secondary">
                            Cerrar sesión
                        </span>

                    </button>
                </form>

            </div>

        </div>

    </aside>

</div>