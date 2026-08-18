@extends('layout')

@section('main')
    <main class="ml-56 min-h-screen" x-data="{
        openModal: {{ $errors->any() ? 'true' : 'false' }},
        openDetailModal: false,
        search: '',
        selectedReparacion: null,
        verDetalle(item) {
            this.selectedReparacion = item;
            this.openDetailModal = true;
        }
    }">
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
                        x-model="search"
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
                    class="flex h-[560px] flex-col rounded-[30px] border-4 border-border bg-background p-6"
                >

                    <div class="flex items-center justify-between pb-3">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            Recibidos
                        </h2>
                        <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-surface-hover px-2.5 text-xs font-bold text-text-secondary border border-border/40">
                            {{ $reparacionesRecibidas->count() }}
                        </span>
                    </div>


                    @if ($reparacionesRecibidas->isEmpty())
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
                    @else
                        <div class="flex flex-1 flex-col gap-4 overflow-y-auto pr-1">
                            @foreach ($reparacionesRecibidas as $reparacion)
                                @php
                                    $clienteNombre = $reparacion->dispositivo?->cliente?->nombre ?? 'Cliente';
                                    $marcaModelo = $reparacion->dispositivo?->marca_y_modelo ?? 'Dispositivo';
                                    $codigo = $reparacion->codigo_seguimiento ? ('#' . $reparacion->codigo_seguimiento) : ('#' . $reparacion->id);
                                    $searchTarget = strtolower($clienteNombre . ' ' . $marcaModelo . ' ' . $reparacion->falla_reportada . ' ' . $codigo . ' ' . $reparacion->id);
                                @endphp
                                <div
                                    x-show="!search || '{{ addslashes($searchTarget) }}'.includes(search.toLowerCase())"
                                    x-transition
                                    class="rounded-[22px] bg-[#1e2938] border border-[#2b3a4d] p-5 shadow-lg hover:border-primary/50 transition-all flex flex-col justify-between gap-4"
                                >
                                    {{-- Fila Superior --}}
                                    <div class="flex items-start justify-between gap-3">
                                        {{-- Izquierda: Cliente y Código --}}
                                        <div class="flex flex-col min-w-0">
                                            <h3 class="text-xl font-bold text-white tracking-tight truncate max-w-[150px]" title="{{ $clienteNombre }}">
                                                {{ $clienteNombre }}
                                            </h3>
                                            <span class="text-sm font-semibold text-[#6d7e93] mt-0.5">
                                                {{ $codigo }}
                                            </span>
                                        </div>

                                        {{-- Derecha: Dispositivo y Falla --}}
                                        <div class="flex flex-col items-end text-right min-w-0">
                                            <span class="text-xl font-bold text-white tracking-tight truncate max-w-[150px]" title="{{ $marcaModelo }}">
                                                {{ $marcaModelo }}
                                            </span>
                                            <span class="text-sm font-medium text-text-secondary mt-0.5 truncate max-w-[160px]" title="{{ $reparacion->falla_reportada }}">
                                                {{ $reparacion->falla_reportada }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Fila Inferior --}}
                                    <div class="flex items-center justify-between pt-1">
                                        {{-- Tiempo transcurrido en cursiva --}}
                                        <span class="text-sm italic font-medium text-[#738294]">
                                            {{ $reparacion->tiempo_transcurrido }}
                                        </span>

                                        {{-- Botón Ojo Azul --}}
                                        <button
                                            type="button"
                                            @click="verDetalle({{ json_encode([
                                                'id' => $reparacion->id,
                                                'codigo_seguimiento' => $codigo,
                                                'cliente_nombre' => $clienteNombre,
                                                'cliente_telefono' => $reparacion->dispositivo?->cliente?->telefono ?? 'Sin teléfono',
                                                'cliente_email' => $reparacion->dispositivo?->cliente?->email ?? 'Sin correo',
                                                'dispositivo_marca_modelo' => $marcaModelo,
                                                'imei_o_serie' => $reparacion->dispositivo?->imei_o_serie ?? 'No especificado',
                                                'clave_de_acceso' => $reparacion->clave_de_acceso ?? 'Sin clave',
                                                'falla_reportada' => $reparacion->falla_reportada,
                                                'costo_estimado' => $reparacion->costo_estimado ? ('$' . number_format($reparacion->costo_estimado, 0, ',', '.')) : 'Sin costo estimado',
                                                'sena' => $reparacion->sena ? ('$' . number_format($reparacion->sena, 0, ',', '.')) : '$0',
                                                'saldo_pendiente' => '$' . number_format($reparacion->saldo_pendiente, 0, ',', '.'),
                                                'estado' => ucfirst($reparacion->estado ?? 'Recibido'),
                                                'notas_internas' => $reparacion->notas_internas ?? '',
                                                'fecha_ingreso' => $reparacion->created_at ? $reparacion->created_at->format('d/m/Y H:i') : '-',
                                                'tiempo_relativo' => $reparacion->tiempo_transcurrido,
                                                'tecnico' => $reparacion->usuario?->name ?? 'No asignado'
                                            ]) }})"
                                            class="flex h-10 w-12 items-center justify-center rounded-2xl bg-[#0081cc] text-white shadow-md hover:bg-primary-hover active:scale-95 transition-all cursor-pointer"
                                            title="Ver detalles de la reparación"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>



                {{-- =====================================
                    EN REPARACIÓN
                ====================================== --}}

                <div
                    class="flex h-[560px] flex-col rounded-[30px] border-4 border-border bg-surface p-6"
                >

                    <div class="flex items-center justify-between pb-3">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            En Reparación
                        </h2>
                        <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-surface-hover px-2.5 text-xs font-bold text-text-secondary border border-border/40">
                            {{ $reparacionesEnProceso->count() }}
                        </span>
                    </div>


                    @if ($reparacionesEnProceso->isEmpty())
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
                    @else
                        <div class="flex flex-1 flex-col gap-4 overflow-y-auto pr-1">
                            @foreach ($reparacionesEnProceso as $reparacion)
                                @php
                                    $clienteNombre = $reparacion->dispositivo?->cliente?->nombre ?? 'Cliente';
                                    $marcaModelo = $reparacion->dispositivo?->marca_y_modelo ?? 'Dispositivo';
                                    $codigo = $reparacion->codigo_seguimiento ? ('#' . $reparacion->codigo_seguimiento) : ('#' . $reparacion->id);
                                    $searchTarget = strtolower($clienteNombre . ' ' . $marcaModelo . ' ' . $reparacion->falla_reportada . ' ' . $codigo . ' ' . $reparacion->id);
                                @endphp
                                <div
                                    x-show="!search || '{{ addslashes($searchTarget) }}'.includes(search.toLowerCase())"
                                    x-transition
                                    class="rounded-[22px] bg-[#1e2938] border border-[#2b3a4d] p-5 shadow-lg hover:border-primary/50 transition-all flex flex-col justify-between gap-4"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex flex-col min-w-0">
                                            <h3 class="text-xl font-bold text-white tracking-tight truncate max-w-[150px]" title="{{ $clienteNombre }}">
                                                {{ $clienteNombre }}
                                            </h3>
                                            <span class="text-sm font-semibold text-[#6d7e93] mt-0.5">
                                                {{ $codigo }}
                                            </span>
                                        </div>

                                        <div class="flex flex-col items-end text-right min-w-0">
                                            <span class="text-xl font-bold text-white tracking-tight truncate max-w-[150px]" title="{{ $marcaModelo }}">
                                                {{ $marcaModelo }}
                                            </span>
                                            <span class="text-sm font-medium text-text-secondary mt-0.5 truncate max-w-[160px]" title="{{ $reparacion->falla_reportada }}">
                                                {{ $reparacion->falla_reportada }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-sm italic font-medium text-[#738294]">
                                            {{ $reparacion->tiempo_transcurrido }}
                                        </span>

                                        <button
                                            type="button"
                                            @click="verDetalle({{ json_encode([
                                                'id' => $reparacion->id,
                                                'codigo_seguimiento' => $codigo,
                                                'cliente_nombre' => $clienteNombre,
                                                'cliente_telefono' => $reparacion->dispositivo?->cliente?->telefono ?? 'Sin teléfono',
                                                'cliente_email' => $reparacion->dispositivo?->cliente?->email ?? 'Sin correo',
                                                'dispositivo_marca_modelo' => $marcaModelo,
                                                'imei_o_serie' => $reparacion->dispositivo?->imei_o_serie ?? 'No especificado',
                                                'clave_de_acceso' => $reparacion->clave_de_acceso ?? 'Sin clave',
                                                'falla_reportada' => $reparacion->falla_reportada,
                                                'costo_estimado' => $reparacion->costo_estimado ? ('$' . number_format($reparacion->costo_estimado, 0, ',', '.')) : 'Sin costo estimado',
                                                'sena' => $reparacion->sena ? ('$' . number_format($reparacion->sena, 0, ',', '.')) : '$0',
                                                'saldo_pendiente' => '$' . number_format($reparacion->saldo_pendiente, 0, ',', '.'),
                                                'estado' => ucfirst($reparacion->estado ?? 'En Reparación'),
                                                'notas_internas' => $reparacion->notas_internas ?? '',
                                                'fecha_ingreso' => $reparacion->created_at ? $reparacion->created_at->format('d/m/Y H:i') : '-',
                                                'tiempo_relativo' => $reparacion->tiempo_transcurrido,
                                                'tecnico' => $reparacion->usuario?->name ?? 'No asignado'
                                            ]) }})"
                                            class="flex h-10 w-12 items-center justify-center rounded-2xl bg-[#0081cc] text-white shadow-md hover:bg-primary-hover active:scale-95 transition-all cursor-pointer"
                                            title="Ver detalles de la reparación"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>



                {{-- =====================================
                    LISTOS
                ====================================== --}}

                <div
                    class="flex h-[560px] flex-col rounded-[30px] border-4 border-primary-hover bg-surface-hover p-6"
                >

                    <div class="flex items-center justify-between pb-3">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            Listos
                        </h2>
                        <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-primary/20 px-2.5 text-xs font-bold text-primary-light border border-primary/30">
                            {{ $reparacionesListas->count() }}
                        </span>
                    </div>


                    @if ($reparacionesListas->isEmpty())
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
                    @else
                        <div class="flex flex-1 flex-col gap-4 overflow-y-auto pr-1">
                            @foreach ($reparacionesListas as $reparacion)
                                @php
                                    $clienteNombre = $reparacion->dispositivo?->cliente?->nombre ?? 'Cliente';
                                    $marcaModelo = $reparacion->dispositivo?->marca_y_modelo ?? 'Dispositivo';
                                    $codigo = $reparacion->codigo_seguimiento ? ('#' . $reparacion->codigo_seguimiento) : ('#' . $reparacion->id);
                                    $searchTarget = strtolower($clienteNombre . ' ' . $marcaModelo . ' ' . $reparacion->falla_reportada . ' ' . $codigo . ' ' . $reparacion->id);
                                @endphp
                                <div
                                    x-show="!search || '{{ addslashes($searchTarget) }}'.includes(search.toLowerCase())"
                                    x-transition
                                    class="rounded-[22px] bg-[#1e2938] border border-[#2b3a4d] p-5 shadow-lg hover:border-primary/50 transition-all flex flex-col justify-between gap-4"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex flex-col min-w-0">
                                            <h3 class="text-xl font-bold text-white tracking-tight truncate max-w-[150px]" title="{{ $clienteNombre }}">
                                                {{ $clienteNombre }}
                                            </h3>
                                            <span class="text-sm font-semibold text-[#6d7e93] mt-0.5">
                                                {{ $codigo }}
                                            </span>
                                        </div>

                                        <div class="flex flex-col items-end text-right min-w-0">
                                            <span class="text-xl font-bold text-white tracking-tight truncate max-w-[150px]" title="{{ $marcaModelo }}">
                                                {{ $marcaModelo }}
                                            </span>
                                            <span class="text-sm font-medium text-text-secondary mt-0.5 truncate max-w-[160px]" title="{{ $reparacion->falla_reportada }}">
                                                {{ $reparacion->falla_reportada }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-sm italic font-medium text-[#738294]">
                                            {{ $reparacion->tiempo_transcurrido }}
                                        </span>

                                        <button
                                            type="button"
                                            @click="verDetalle({{ json_encode([
                                                'id' => $reparacion->id,
                                                'codigo_seguimiento' => $codigo,
                                                'cliente_nombre' => $clienteNombre,
                                                'cliente_telefono' => $reparacion->dispositivo?->cliente?->telefono ?? 'Sin teléfono',
                                                'cliente_email' => $reparacion->dispositivo?->cliente?->email ?? 'Sin correo',
                                                'dispositivo_marca_modelo' => $marcaModelo,
                                                'imei_o_serie' => $reparacion->dispositivo?->imei_o_serie ?? 'No especificado',
                                                'clave_de_acceso' => $reparacion->clave_de_acceso ?? 'Sin clave',
                                                'falla_reportada' => $reparacion->falla_reportada,
                                                'costo_estimado' => $reparacion->costo_estimado ? ('$' . number_format($reparacion->costo_estimado, 0, ',', '.')) : 'Sin costo estimado',
                                                'sena' => $reparacion->sena ? ('$' . number_format($reparacion->sena, 0, ',', '.')) : '$0',
                                                'saldo_pendiente' => '$' . number_format($reparacion->saldo_pendiente, 0, ',', '.'),
                                                'estado' => ucfirst($reparacion->estado ?? 'Listo'),
                                                'notas_internas' => $reparacion->notas_internas ?? '',
                                                'fecha_ingreso' => $reparacion->created_at ? $reparacion->created_at->format('d/m/Y H:i') : '-',
                                                'tiempo_relativo' => $reparacion->tiempo_transcurrido,
                                                'tecnico' => $reparacion->usuario?->name ?? 'No asignado'
                                            ]) }})"
                                            class="flex h-10 w-12 items-center justify-center rounded-2xl bg-[#0081cc] text-white shadow-md hover:bg-primary-hover active:scale-95 transition-all cursor-pointer"
                                            title="Ver detalles de la reparación"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

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

        {{-- =========================================
            MODAL DETALLE DE REPARACIÓN (Vista Rápida)
        ========================================== --}}
        <div
            x-show="openDetailModal"
            x-cloak
            @keydown.escape.window="openDetailModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openDetailModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openDetailModal = false"
                class="fixed inset-0 bg-black/75 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openDetailModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-4xl max-h-[90vh] rounded-3xl bg-[#141c25] p-5 sm:p-7 shadow-2xl border border-border/30 overflow-y-auto my-auto"
            >
                {{-- Encabezado Modal --}}
                <div class="flex items-center justify-between border-b border-border/30 pb-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/20 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-white tracking-wide">
                                Reparación <span class="text-primary-light" x-text="selectedReparacion?.codigo_seguimiento"></span>
                            </h2>
                            <p class="text-xs text-text-disabled" x-text="selectedReparacion?.tiempo_relativo"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            class="px-3.5 py-1 rounded-full text-xs font-bold tracking-wide uppercase"
                            :class="{
                                'bg-[#0081cc]/20 text-[#33b4ff] border border-[#0081cc]/40': selectedReparacion?.estado === 'Recibido',
                                'bg-warning/20 text-warning border border-warning/40': selectedReparacion?.estado === 'En Reparación' || selectedReparacion?.estado === 'En reparacion',
                                'bg-success/20 text-success border border-success/40': selectedReparacion?.estado === 'Listo' || selectedReparacion?.estado === 'Listos'
                            }"
                            x-text="selectedReparacion?.estado"
                        ></span>

                        <button
                            type="button"
                            @click="openDetailModal = false"
                            class="text-text-disabled hover:text-white transition-colors p-1.5 rounded-xl hover:bg-surface-hover cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Grid 2 Columnas Detalle --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                    {{-- COLUMNA IZQUIERDA: CLIENTE Y FALLA --}}
                    <div class="flex flex-col gap-4 rounded-2xl bg-[#273343] p-4 sm:p-5 border border-border/20">

                        {{-- Encabezado Cliente --}}
                        <div class="flex items-center justify-between pb-1 border-b border-white/10">
                            <h3 class="text-lg sm:text-xl font-bold text-white tracking-wide flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-[#0081cc]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.72m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.773m0 0A5.97 5.97 0 0 0 6 18.72m0 0a9.093 9.093 0 0 1-3.741-.479 3 3 0 0 1 4.682-2.72m.94 3.198.001.031c0 .225.012.447.037.666A11.94 11.94 0 0 0 12 21M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                </svg>
                                Cliente
                            </h3>
                        </div>

                        {{-- Nombre --}}
                        <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                            <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Nombre</span>
                            <span class="text-base font-bold text-white" x-text="selectedReparacion?.cliente_nombre"></span>
                        </div>

                        {{-- Teléfono y Email --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Teléfono</span>
                                <span class="text-sm font-semibold text-primary-light" x-text="selectedReparacion?.cliente_telefono"></span>
                            </div>
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Email</span>
                                <span class="text-sm font-medium text-gray-300 truncate block" x-text="selectedReparacion?.cliente_email"></span>
                            </div>
                        </div>

                        {{-- Falla Reportada --}}
                        <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                            <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider mb-1">Falla Reportada</span>
                            <p class="text-sm font-medium text-white/90 whitespace-pre-wrap leading-relaxed" x-text="selectedReparacion?.falla_reportada"></p>
                        </div>

                        {{-- Seña abonada --}}
                        <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20 flex items-center justify-between">
                            <span class="text-xs font-semibold text-text-disabled uppercase tracking-wider">Seña Abonada</span>
                            <span class="text-base font-bold text-success" x-text="selectedReparacion?.sena"></span>
                        </div>

                    </div>

                    {{-- COLUMNA DERECHA: DISPOSITIVO Y DATOS ECONÓMICOS --}}
                    <div class="flex flex-col gap-4 rounded-2xl bg-[#273343] p-4 sm:p-5 border border-border/20">

                        {{-- Encabezado Dispositivo --}}
                        <div class="flex items-center justify-between pb-1 border-b border-white/10">
                            <h3 class="text-lg sm:text-xl font-bold text-white tracking-wide flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-[#0081cc]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21 21 17.25l-5.83-5.83m-3.75 3.75L6 20.5 3.5 18l5.33-5.33M15 3a6 6 0 0 0-7.3 7.3L3 15l6 6 4.7-4.7A6 6 0 0 0 15 3Z" />
                                </svg>
                                Dispositivo
                            </h3>
                        </div>

                        {{-- Marca y Modelo --}}
                        <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                            <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Marca y Modelo</span>
                            <span class="text-base font-bold text-white" x-text="selectedReparacion?.dispositivo_marca_modelo"></span>
                        </div>

                        {{-- Clave de acceso e IMEI --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Clave de Acceso</span>
                                <span class="text-sm font-semibold text-gray-200" x-text="selectedReparacion?.clave_de_acceso"></span>
                            </div>
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">IMEI / Serie</span>
                                <span class="text-sm font-medium text-gray-300 truncate block" x-text="selectedReparacion?.imei_o_serie"></span>
                            </div>
                        </div>

                        {{-- Costo y Saldo Pendiente --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Valor Estimado</span>
                                <span class="text-base font-bold text-white" x-text="selectedReparacion?.costo_estimado"></span>
                            </div>
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Saldo Pendiente</span>
                                <span class="text-base font-bold text-warning" x-text="selectedReparacion?.saldo_pendiente"></span>
                            </div>
                        </div>

                        {{-- Fecha de ingreso y Técnico --}}
                        <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20 flex items-center justify-between">
                            <div>
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Fecha de Ingreso</span>
                                <span class="text-sm font-medium text-gray-300" x-text="selectedReparacion?.fecha_ingreso"></span>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Técnico</span>
                                <span class="text-sm font-medium text-gray-300" x-text="selectedReparacion?.tecnico"></span>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Pie del modal: Acciones --}}
                <div class="flex items-center justify-end gap-4 pt-5 mt-2 border-t border-border/20">
                    <button
                        type="button"
                        @click="openDetailModal = false"
                        class="h-11 rounded-xl bg-surface-hover hover:bg-[#364252] px-6 text-sm font-bold text-white transition-all cursor-pointer"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection
