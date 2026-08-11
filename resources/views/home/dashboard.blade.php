@extends('layout')

@section('main')
    <main class="ml-56 min-h-screen">
        @include('components.sidebar')
        <div class="px-12 py-10">


            {{-- =========================================
                HEADER
            ========================================== --}}

            <div class="flex items-center gap-12">


                {{-- Buscador --}}
                <div class="relative flex-1">

                    {{-- Search icon --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2.5"
                        stroke="currentColor"
                        class="absolute left-6 top-1/2 h-7 w-7 -translate-y-1/2 text-text-disabled"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
                        />
                    </svg>


                    <input
                        type="search"
                        placeholder="Buscar cliente o dispositivo..."
                        class="h-16 w-full rounded-full border-0 bg-surface-hover pl-20 pr-6 text-base font-semibold text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary"
                    >

                </div>


                {{-- Nueva reparación --}}
                <button
                    type="button"
                    class="h-16 rounded-2xl bg-primary px-10 text-xl font-bold text-white transition-colors hover:bg-primary-hover"
                >
                    + Nueva Reparación
                </button>

            </div>



            {{-- =========================================
                ESTADOS DE REPARACIONES
            ========================================== --}}

            <div class="mt-8 grid grid-cols-3 gap-12">


                {{-- =====================================
                    RECIBIDOS
                ====================================== --}}

                <div
                    class="flex h-[516px] flex-col rounded-[30px] border-4 border-border bg-background p-7"
                >

                    <h2 class="font-bold">
                        Recibidos
                    </h2>


                    <div
                        class="flex flex-1 flex-col items-center justify-center"
                    >

                        <span
                            class="text-8xl font-medium leading-none text-text-disabled"
                        >
                            0
                        </span>

                        <span
                            class="mt-2 text-3xl text-text-disabled"
                        >
                            Recibidos
                        </span>

                    </div>

                </div>



                {{-- =====================================
                    EN REPARACIÓN
                ====================================== --}}

                <div
                    class="flex h-[516px] flex-col rounded-[30px] border-4 border-border bg-surface p-7"
                >

                    <h2 class="font-bold">
                        En Reparación
                    </h2>


                    <div
                        class="flex flex-1 items-center justify-center"
                    >

                        {{-- Wrench --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-12 w-12 text-border"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11.42 15.17 17.25 21 21 17.25l-5.83-5.83m-3.75 3.75L6 20.5 3.5 18l5.33-5.33M15 3a6 6 0 0 0-7.3 7.3L3 15l6 6 4.7-4.7A6 6 0 0 0 15 3Z"
                            />
                        </svg>

                    </div>

                </div>



                {{-- =====================================
                    LISTOS
                ====================================== --}}

                <div
                    class="flex h-[516px] flex-col rounded-[30px] border-4 border-primary-hover bg-surface-hover p-7"
                >

                    <h2 class="font-bold">
                        Listos
                    </h2>


                    <div
                        class="flex flex-1 items-center justify-center"
                    >

                        {{-- Check --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-12 w-12 text-primary-hover"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75"
                            />

                            <rect
                                width="16"
                                height="16"
                                x="4"
                                y="4"
                                rx="2"
                            />

                        </svg>

                    </div>

                </div>

            </div>

        </div>
    </main>
@endsection