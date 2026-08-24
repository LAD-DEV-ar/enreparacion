<aside
    class="fixed inset-y-0 left-0 z-40 w-56 border-r border-border bg-background"
>
    <div class="flex h-full flex-col">


        {{-- =========================================
             LOGO
        ========================================== --}}

        <div class="flex h-32 items-center px-8">

            <span class="text-2xl font-bold tracking-tight">
                EnReparación
            </span>

        </div>


        {{-- =========================================
             NAVEGACIÓN
        ========================================== --}}

        <nav class="flex-1 px-4">

            {{-- Inicio --}}
            <a
                href="{{ route('dashboard.index') }}"
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
                class="mt-3 flex h-16 items-center gap-4 rounded-2xl px-5 {{ request()->routeIs('reparaciones.*') ? 'bg-surface-hover' : 'transition-colors hover:bg-surface-hover' }}"
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
                        <g transform="translate(12 12) scale(1.28) translate(-12 -12)">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 1 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"
                            />
                        </g>
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
                href="#"
                class="flex items-center gap-3 rounded-2xl transition-colors hover:bg-surface-hover"
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

        </div>

    </div>

</aside>