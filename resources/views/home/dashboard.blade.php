@extends('layout')

@section('main')
    <main class="ml-56 min-h-screen" x-data="{ openModal: {{ $errors->any() ? 'true' : 'false' }} }">
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
                    @click="openModal = true"
                    class="h-16 rounded-2xl bg-primary px-10 text-xl font-bold text-white transition-colors hover:bg-primary-hover cursor-pointer"
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

                        @if (!$reparaciones)
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
                        @else
                        <span>
                            @foreach ($reparaciones as $reparacion )
                            {{  $reparacion->id }}
                            @endforeach
                        </span>
                        @endif

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

        {{-- =========================================
            MODAL NUEVA REPARACIÓN (Diseño Compacto)
        ========================================== --}}
        <div
            x-show="openModal"
            x-cloak
            @keydown.escape.window="openModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openModal = false"
                class="fixed inset-0 bg-black/75 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-4xl max-h-[90vh] rounded-3xl bg-[#141c25] p-4 sm:p-6 shadow-2xl border border-border/30 overflow-y-auto my-auto"
            >
                <form action="{{ route('dashboard.store') }}" method="POST">
                    @csrf

                    {{-- Grid 2 Columnas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                        {{-- COLUMNA IZQUIERDA: CLIENTE / FALLA / SEÑA --}}
                        <div class="flex flex-col gap-3 rounded-2xl bg-[#273343] p-4 sm:p-5 border border-border/20">

                            {{-- Encabezado Cliente --}}
                            <div class="flex items-center justify-between pb-1">
                                <h3 class="text-xl sm:text-2xl font-bold text-white tracking-wide">
                                    Cliente:
                                </h3>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-7 h-7 text-[#0081cc]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.72m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.773m0 0A5.97 5.97 0 0 0 6 18.72m0 0a9.093 9.093 0 0 1-3.741-.479 3 3 0 0 1 4.682-2.72m.94 3.198.001.031c0 .225.012.447.037.666A11.94 11.94 0 0 0 12 21M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                </svg>
                            </div>

                            {{-- Input Nombre --}}
                            <div>
                                <input
                                    type="text"
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    placeholder="Nombre"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('nombre')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Input Telefono --}}
                            <div>
                                <input
                                    type="text"
                                    name="telefono"
                                    value="{{ old('telefono') }}"
                                    placeholder="Telefono"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('telefono')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Input Email --}}
                            <div>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Email"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('email')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Falla reportada --}}
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Falla reportada:
                                </label>
                                <textarea
                                    name="falla_reportada"
                                    rows="2"
                                    placeholder="Detalle de la falla"
                                    class="w-full rounded-xl bg-[#6f7b8c] p-3 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner resize-none"
                                >{{ old('falla_reportada') }}</textarea>
                                @error('falla_reportada')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Seña del cliente --}}
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Seña del cliente:
                                </label>
                                <input
                                    type="number"
                                    step="any"
                                    name="sena"
                                    value="{{ old('sena') }}"
                                    placeholder="$$$"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('sena')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- COLUMNA DERECHA: DISPOSITIVO / CLAVE DE ACCESO / IMEI / VALOR --}}
                        <div class="flex flex-col gap-3 rounded-2xl bg-[#273343] p-4 sm:p-5 border border-border/20">

                            {{-- Encabezado Dispositivo --}}
                            <div class="flex items-center justify-between pb-1">
                                <h3 class="text-xl sm:text-2xl font-bold text-white tracking-wide">
                                    Dispositivo:
                                </h3>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-7 h-7 text-[#0081cc]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21 21 17.25l-5.83-5.83m-3.75 3.75L6 20.5 3.5 18l5.33-5.33M15 3a6 6 0 0 0-7.3 7.3L3 15l6 6 4.7-4.7A6 6 0 0 0 15 3Z" />
                                </svg>
                            </div>

                            {{-- Input Marca y modelo --}}
                            <div>
                                <input
                                    type="text"
                                    name="marca_y_modelo"
                                    value="{{ old('marca_y_modelo') }}"
                                    placeholder="Marca y modelo"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('marca_y_modelo')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Clave de acceso --}}
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Clave de acceso:
                                </label>
                                <div class="flex gap-2">
                                    <div class="relative w-2/3">
                                        <select
                                            name="clave_de_acceso"
                                            class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 pr-10 text-sm font-semibold text-white outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner appearance-none cursor-pointer"
                                        >
                                            <option value="Sin clave" {{ old('clave_de_acceso') == 'Sin clave' ? 'selected' : '' }}>Sin clave</option>
                                            <option value="PIN / Contraseña" {{ old('clave_de_acceso') == 'PIN / Contraseña' ? 'selected' : '' }}>PIN / Contraseña</option>
                                            <option value="Patrón de desbloqueo" {{ old('clave_de_acceso') == 'Patrón de desbloqueo' ? 'selected' : '' }}>Patrón de desbloqueo</option>
                                            <option value="Huella / Face ID" {{ old('clave_de_acceso') == 'Huella / Face ID' ? 'selected' : '' }}>Huella / Face ID</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-200">
                                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('clave_de_acceso')
                                        <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                    @enderror
                                    <button class="w-1/3 h-10 sm:h-11 rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white outline-none border border-transparent cursor-pointer flex items-center justify-center"> 
                                        Guardar
                                    </button>
                                </div>
                            </div>

                            {{-- IMEI / Nº Serie --}}
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-white mb-1">
                                    IMEI/Nº Serie:
                                </label>
                                <input
                                    type="text"
                                    name="imei_o_serie"
                                    value="{{ old('imei_o_serie') }}"
                                    placeholder="IMEI/Nº Serie"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('imei_o_serie')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Costo Estimado --}}
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Valor:
                                </label>
                                <input
                                    type="number"
                                    step="any"
                                    name="costo_estimado"
                                    value="{{ old('costo_estimado') }}"
                                    placeholder="$$$"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#6f7b8c] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('costo_estimado')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                    </div>

                    {{-- Pie del modal: Acciones --}}
                    <div class="flex items-center justify-end gap-4 sm:gap-6 pt-4">
                        <button
                            type="button"
                            @click="openModal = false"
                            class="text-base font-bold text-white hover:text-gray-300 transition-colors cursor-pointer px-3 py-1.5"
                        >
                            Cerrar
                        </button>
                        <button
                            type="submit"
                            class="h-11 sm:h-12 rounded-xl bg-[#0081cc] hover:bg-[#33b4ff] px-6 sm:px-8 text-base font-bold text-white transition-all shadow-md active:scale-[0.98] cursor-pointer"
                        >
                            Guardar Reparacion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
