@extends('layout')

@section('main')
    <main
        class="ml-56 min-h-screen pb-16"
        x-data="{
            search: '',
            openNewClientModal: {{ $errors->any() ? 'true' : 'false' }},
            openDetailModal: false,
            selectedCliente: null,
            clientes: {{ Js::from($clientes) }},

            filtrados() {
                if (!this.search || !this.search.trim()) {
                    return this.clientes;
                }
                const q = this.search.toLowerCase().trim();
                return this.clientes.filter(c => c.search_target && c.search_target.includes(q));
            },

            verFicha(cliente) {
                this.selectedCliente = cliente;
                this.openDetailModal = true;
            },

            limpiarBusqueda() {
                this.search = '';
            }
        }"
    >
        @include('components.sidebar')

        <div class="px-12 py-10">

            {{-- =========================================
                HEADER / ENCABEZADO DE BÚSQUEDA ACTIVA
            ========================================== --}}
            <div class="flex flex-col gap-6">

                <div class="flex items-center justify-between gap-6">

                    {{-- Barra de Búsqueda Grande y Central --}}
                    <div class="relative flex-1">
                        {{-- Icono de Búsqueda --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="absolute left-6 top-1/2 h-7 w-7 -translate-y-1/2 text-text-disabled pointer-events-none"
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
                            placeholder="Buscar por nombre o celular..."
                            class="h-16 w-full rounded-full border-0 bg-surface-hover pl-20 pr-14 text-base font-semibold text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary shadow-sm transition-all"
                        >

                        {{-- Botón para limpiar búsqueda --}}
                        <button
                            x-show="search.length > 0"
                            x-cloak
                            @click="limpiarBusqueda()"
                            type="button"
                            class="absolute right-5 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-surface text-text-disabled hover:text-white hover:bg-border/60 transition-colors"
                            title="Limpiar búsqueda"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Botón Nuevo Cliente --}}
                    <button
                        type="button"
                        @click="openNewClientModal = true"
                        class="h-16 rounded-2xl bg-primary px-8 text-lg font-bold text-white transition-all hover:bg-primary-hover active:scale-[0.98] shadow-md flex items-center gap-2.5 cursor-pointer shrink-0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                        </svg>
                        <span>+ Nuevo Cliente</span>
                    </button>

                </div>

                {{-- Subtítulo / Resumen de Contador --}}
                <div class="flex items-center justify-between px-2 text-sm">
                    <div class="flex items-center gap-3 font-semibold text-text-secondary">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                            <span x-text="filtrados().length"></span>
                            <span x-text="filtrados().length === 1 ? 'cliente encontrado' : 'clientes encontrados'"></span>
                        </span>
                        <template x-if="search.trim().length > 0">
                            <span class="text-text-disabled">
                                para "<span class="text-primary-light font-bold" x-text="search"></span>"
                            </span>
                        </template>
                    </div>

                    <div class="hidden md:flex items-center gap-6 text-xs text-text-disabled">
                        <span>Total registrados: <strong class="text-text-secondary font-bold">{{ $totalClientes }}</strong></span>
                        <span>•</span>
                        <span>Equipos en taller: <strong class="text-warning font-bold">{{ $totalEquiposEnTaller }}</strong></span>
                    </div>
                </div>

            </div>


            {{-- =========================================
                LISTADO: FILAS INTELIGENTES (SMART ROWS)
            ========================================== --}}
            <div class="mt-8 flex flex-col gap-3.5">

                {{-- Template iterativo de Filas Inteligentes --}}
                <template x-for="cliente in filtrados()" :key="cliente.id">
                    <div
                        @click="verFicha(cliente)"
                        class="group relative flex flex-col md:flex-row md:items-center justify-between gap-5 rounded-[22px] bg-[#1c2530] border border-[#2b3a4d] px-6 py-5 shadow-md hover:bg-[#232f3e] hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition-all duration-200 cursor-pointer select-none"
                    >
                        {{-- Resplandor sutil al hacer hover --}}
                        <div class="absolute inset-0 rounded-[22px] bg-gradient-to-r from-primary/0 via-primary/5 to-transparent opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300"></div>

                        {{-- =========================================
                            BLOQUE 1: IDENTIDAD (Avatar, Nombre, Celular)
                        ========================================== --}}
                        <div class="relative z-10 flex items-center gap-4.5 min-w-0 md:w-5/12 lg:w-4/12">
                            {{-- Círculo Avatar con Iniciales --}}
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1a3854] to-[#122538] border border-primary/30 text-primary-light font-bold text-lg tracking-wider shadow-inner group-hover:scale-105 group-hover:border-primary/60 transition-transform duration-200"
                                x-text="cliente.iniciales"
                            ></div>

                            {{-- Nombre y WhatsApp --}}
                            <div class="flex flex-col min-w-0">
                                <h3
                                    class="text-base sm:text-lg font-bold text-white tracking-tight truncate group-hover:text-primary-light transition-colors"
                                    :title="cliente.nombre"
                                    x-text="cliente.nombre"
                                ></h3>

                                <div class="flex items-center gap-2 mt-1">
                                    {{-- Icono sutil WhatsApp --}}
                                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/>
                                    </svg>
                                    <span
                                        class="text-xs sm:text-sm font-medium text-text-secondary tracking-wide group-hover:text-text-primary transition-colors"
                                        x-text="cliente.telefono"
                                    ></span>
                                </div>
                            </div>
                        </div>


                        {{-- =========================================
                            BLOQUE 2: VALOR COMERCIAL (Métricas Claras)
                        ========================================== --}}
                        <div class="relative z-10 flex flex-wrap items-center gap-3 sm:gap-4 md:w-4/12 lg:w-5/12">

                            {{-- Métrica 1: Reparaciones Totales --}}
                            <div class="flex items-center gap-2 rounded-xl bg-[#141c25]/80 px-3.5 py-2 border border-border/30">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-text-disabled">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21 21 17.25l-5.83-5.83m-3.75 3.75L6 20.5 3.5 18l5.33-5.33M15 3a6 6 0 0 0-7.3 7.3L3 15l6 6 4.7-4.7A6 6 0 0 0 15 3Z" />
                                </svg>
                                <span class="text-xs sm:text-sm font-semibold text-text-secondary" x-text="cliente.total_reparaciones_label"></span>
                            </div>

                            {{-- Métrica 2: Badge Equipo en Taller (si tiene activos) --}}
                            <template x-if="cliente.equipos_en_taller > 0">
                                <div class="inline-flex items-center gap-2 rounded-xl bg-amber-500/15 border border-amber-500/30 px-3.5 py-2 text-amber-400 shadow-sm">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    <span class="text-xs sm:text-sm font-bold tracking-tight" x-text="cliente.equipos_en_taller_label"></span>
                                </div>
                            </template>

                            <template x-if="cliente.equipos_en_taller === 0">
                                <div class="hidden sm:inline-flex items-center gap-1.5 text-xs text-text-disabled font-medium px-2 py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-text-disabled">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Sin equipos activos</span>
                                </div>
                            </template>

                        </div>


                        {{-- =========================================
                            BLOQUE 3: ACCIONES RÁPIDAS (Iconografía de Alta Fidelidad)
                        ========================================== --}}
                        <div class="relative z-10 flex items-center justify-end gap-2.5 md:w-3/12 lg:w-3/12 shrink-0">

                            {{-- Botón 1: WhatsApp Directo --}}
                            <a
                                :href="cliente.whatsapp_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                @click.stop
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all duration-200 shadow-sm hover:scale-105 active:scale-95"
                                title="Abrir chat de WhatsApp"
                                aria-label="Abrir WhatsApp"
                            >
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/>
                                </svg>
                            </a>

                            {{-- Botón 2: Ver Historial / Ficha Completa --}}
                            <button
                                type="button"
                                @click.stop="verFicha(cliente)"
                                class="flex h-11 px-4 items-center justify-center gap-2 rounded-xl bg-primary/15 text-primary-light border border-primary/30 hover:bg-primary hover:text-white hover:border-primary transition-all duration-200 shadow-sm hover:scale-105 active:scale-95 cursor-pointer"
                                title="Ver ficha completa e historial"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span class="text-xs font-bold hidden xl:inline">Historial</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>

                        </div>

                    </div>
                </template>


                {{-- =========================================
                    ESTADO VACÍO: BÚSQUEDA SIN RESULTADOS
                ========================================== --}}
                <div
                    x-show="filtrados().length === 0 && clientes.length > 0"
                    x-cloak
                    class="flex flex-col items-center justify-center rounded-[26px] bg-[#1c2530]/50 border border-border/40 py-16 px-6 text-center"
                >
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-hover text-text-disabled mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">No se encontraron clientes</h3>
                    <p class="mt-1 text-sm text-text-secondary max-w-sm">
                        No hay coincidencias para "<span class="text-primary-light font-semibold" x-text="search"></span>". Intenta con otro nombre, celular o dispositivo.
                    </p>
                    <button
                        type="button"
                        @click="limpiarBusqueda()"
                        class="mt-5 rounded-xl bg-surface-hover hover:bg-border/60 px-5 py-2 text-xs font-bold text-text-primary transition-colors cursor-pointer"
                    >
                        Limpiar búsqueda
                    </button>
                </div>


                {{-- =========================================
                    ESTADO VACÍO: SIN CLIENTES REGISTRADOS
                ========================================== --}}
                <div
                    x-show="clientes.length === 0"
                    x-cloak
                    class="flex flex-col items-center justify-center rounded-[30px] bg-[#1c2530]/40 border-2 border-dashed border-border/60 py-20 px-6 text-center"
                >
                    <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-primary/10 border border-primary/20 text-primary mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.72m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.773m0 0A5.97 5.97 0 0 0 6 18.72m0 0a9.093 9.093 0 0 1-3.741-.479 3 3 0 0 1 4.682-2.72m.94 3.198.001.031c0 .225.012.447.037.666A11.94 11.94 0 0 0 12 21M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Comienza a registrar tus clientes</h2>
                    <p class="mt-2 text-sm text-text-secondary max-w-md">
                        Cada vez que recibas una reparación en el dashboard o registres un cliente, aparecerá automáticamente aquí con su historial y accesos directos.
                    </p>
                    <button
                        type="button"
                        @click="openNewClientModal = true"
                        class="mt-6 rounded-2xl bg-primary hover:bg-primary-hover px-8 py-3.5 text-sm font-bold text-white transition-all shadow-lg active:scale-95 cursor-pointer"
                    >
                        + Registrar Primer Cliente
                    </button>
                </div>

            </div>

        </div>


        {{-- =========================================
            MODAL: FICHA COMPLETA E HISTORIAL DEL CLIENTE
        ========================================== --}}
        <div
            x-show="openDetailModal"
            x-cloak
            @keydown.escape.window="openDetailModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop con Desenfoque --}}
            <div
                x-show="openDetailModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openDetailModal = false"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Ventana Modal Principal --}}
            <div
                x-show="openDetailModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                class="relative z-10 w-full max-w-4xl max-h-[90vh] rounded-3xl bg-[#141c25] p-6 sm:p-8 shadow-2xl border border-border/40 overflow-y-auto my-auto"
            >
                <template x-if="selectedCliente">
                    <div class="flex flex-col gap-6">

                        {{-- ENCABEZADO DE LA FICHA --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-b border-border/30 pb-6">
                            
                            {{-- Info Principal del Cliente --}}
                            <div class="flex items-center gap-4.5">
                                <div
                                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1a3854] to-[#122538] border-2 border-primary/40 text-primary-light font-bold text-2xl tracking-wider shadow-md"
                                    x-text="selectedCliente.iniciales"
                                ></div>

                                <div>
                                    <h2 class="text-2xl font-bold text-white tracking-tight" x-text="selectedCliente.nombre"></h2>
                                    
                                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-text-secondary">
                                        <span class="flex items-center gap-1 font-semibold text-text-primary">
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/>
                                            </svg>
                                            <span x-text="selectedCliente.telefono"></span>
                                        </span>

                                        <template x-if="selectedCliente.email && selectedCliente.email !== 'Sin correo'">
                                            <span class="flex items-center gap-1">
                                                <span>•</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-text-disabled">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                </svg>
                                                <span x-text="selectedCliente.email"></span>
                                            </span>
                                        </template>

                                        <span>•</span>
                                        <span x-text="selectedCliente.tiempo_registro_relativo"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción en Header --}}
                            <div class="flex items-center gap-3 shrink-0">
                                <a
                                    :href="selectedCliente.whatsapp_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition-all shadow-md active:scale-95"
                                >
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/>
                                    </svg>
                                    <span>Chatear por WhatsApp</span>
                                </a>

                                <button
                                    type="button"
                                    @click="openDetailModal = false"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-hover text-text-disabled hover:text-white hover:bg-border/60 transition-colors cursor-pointer"
                                    title="Cerrar modal"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                        </div>


                        {{-- TARJETAS KPI DE RESUMEN COMERCIAL --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                            
                            <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30">
                                <span class="text-xs font-semibold text-text-disabled uppercase tracking-wider">Reparaciones</span>
                                <div class="text-xl font-extrabold text-white mt-1" x-text="selectedCliente.total_reparaciones"></div>
                                <span class="text-[11px] text-text-secondary mt-0.5 block">Historial total</span>
                            </div>

                            <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30">
                                <span class="text-xs font-semibold text-text-disabled uppercase tracking-wider">En Taller</span>
                                <div
                                    class="text-xl font-extrabold mt-1"
                                    :class="selectedCliente.equipos_en_taller > 0 ? 'text-amber-400' : 'text-text-secondary'"
                                    x-text="selectedCliente.equipos_en_taller"
                                ></div>
                                <span class="text-[11px] text-text-secondary mt-0.5 block">Trabajos activos</span>
                            </div>

                            <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30">
                                <span class="text-xs font-semibold text-text-disabled uppercase tracking-wider">Total Facturado</span>
                                <div class="text-xl font-extrabold text-primary-light mt-1" x-text="selectedCliente.total_gastado"></div>
                                <span class="text-[11px] text-text-secondary mt-0.5 block">Costo estimado acumulado</span>
                            </div>

                            <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30">
                                <span class="text-xs font-semibold text-text-disabled uppercase tracking-wider">Saldo Pendiente</span>
                                <div
                                    class="text-xl font-extrabold mt-1"
                                    :class="selectedCliente.saldo_pendiente_total !== '$0' ? 'text-danger' : 'text-emerald-400'"
                                    x-text="selectedCliente.saldo_pendiente_total"
                                ></div>
                                <span class="text-[11px] text-text-secondary mt-0.5 block">Por cobrar</span>
                            </div>

                        </div>


                        {{-- SECCIÓN: HISTORIAL DE REPARACIONES --}}
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-primary">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Historial de Reparaciones</span>
                                </h3>
                                <span class="text-xs text-text-disabled font-medium" x-text="`${selectedCliente.historial_reparaciones.length} registro(s)`"></span>
                            </div>

                            {{-- Lista de tarjetas de historial --}}
                            <div class="flex flex-col gap-3 max-h-80 overflow-y-auto pr-1">

                                <template x-for="rep in selectedCliente.historial_reparaciones" :key="rep.id">
                                    <div class="flex flex-col gap-3 rounded-2xl bg-[#1c2530] border border-border/30 p-4 hover:border-border transition-colors">
                                        
                                        {{-- Header Reparación: Código, Dispositivo y Estado --}}
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center gap-2.5">
                                                <span class="font-bold text-xs text-[#8ba0b8] bg-[#141c25] px-2.5 py-1 rounded-lg border border-border/40" x-text="rep.codigo_seguimiento"></span>
                                                <h4 class="text-sm font-bold text-white" x-text="rep.dispositivo_marca_modelo"></h4>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-xs italic text-text-disabled" x-text="rep.tiempo_relativo"></span>
                                                
                                                {{-- Badge Estado --}}
                                                <span
                                                    class="px-2.5 py-0.5 rounded-lg text-xs font-bold uppercase tracking-wider"
                                                    :class="{
                                                        'bg-primary/20 text-primary-light border border-primary/30': rep.estado_slug === 'recibido',
                                                        'bg-amber-500/20 text-amber-400 border border-amber-500/30': rep.estado_slug === 'en_reparacion',
                                                        'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': rep.estado_slug === 'listo'
                                                    }"
                                                    x-text="rep.estado"
                                                ></span>
                                            </div>
                                        </div>

                                        {{-- Detalle: Falla e Información Técnica --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-[#141c25]/60 rounded-xl p-3 border border-border/20">
                                            <div>
                                                <span class="text-text-disabled font-semibold">Falla:</span>
                                                <p class="text-white font-medium mt-0.5" x-text="rep.falla_reportada"></p>
                                            </div>
                                            <div>
                                                <span class="text-text-disabled font-semibold">IMEI / Serie:</span>
                                                <p class="text-white font-mono mt-0.5" x-text="rep.imei_o_serie"></p>
                                            </div>
                                        </div>

                                        {{-- Footer Reparación: Costos y Saldos --}}
                                        <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-text-secondary pt-1 border-t border-border/20">
                                            <div class="flex items-center gap-4">
                                                <span>Costo: <strong class="text-white font-bold" x-text="rep.costo_estimado"></strong></span>
                                                <span>Seña: <strong class="text-emerald-400 font-bold" x-text="rep.sena"></strong></span>
                                                <span>Saldo: <strong class="text-danger font-bold" x-text="rep.saldo_pendiente"></strong></span>
                                            </div>

                                            <div class="text-text-disabled">
                                                Ingreso: <span x-text="rep.fecha_ingreso"></span>
                                            </div>
                                        </div>

                                    </div>
                                </template>

                                {{-- Sin reparaciones --}}
                                <template x-if="selectedCliente.historial_reparaciones.length === 0">
                                    <div class="flex flex-col items-center justify-center py-8 text-center text-text-disabled">
                                        <p class="text-sm">Este cliente aún no tiene reparaciones registradas.</p>
                                    </div>
                                </template>

                            </div>
                        </div>


                        {{-- PIE DEL MODAL: CIERRE Y ACCIONES --}}
                        <div class="flex items-center justify-end gap-4 border-t border-border/30 pt-5">
                            <button
                                type="button"
                                @click="openDetailModal = false"
                                class="h-11 rounded-xl bg-surface-hover hover:bg-border/60 px-6 text-sm font-bold text-white transition-colors cursor-pointer"
                            >
                                Cerrar Ficha
                            </button>
                        </div>

                    </div>
                </template>
            </div>
        </div>


        {{-- =========================================
            MODAL: NUEVO CLIENTE (Registro Rápido)
        ========================================== --}}
        <div
            x-show="openNewClientModal"
            x-cloak
            @keydown.escape.window="openNewClientModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openNewClientModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openNewClientModal = false"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openNewClientModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-lg rounded-3xl bg-[#141c25] p-6 sm:p-7 shadow-2xl border border-border/30 my-auto"
            >
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf

                    <div class="flex items-center justify-between border-b border-border/30 pb-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/20 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white tracking-wide">Nuevo Cliente</h3>
                                <p class="text-xs text-text-disabled">Registra los datos de contacto</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="openNewClientModal = false"
                            class="text-text-disabled hover:text-white transition-colors cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-col gap-4">

                        {{-- Nombre --}}
                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5 uppercase tracking-wider">
                                Nombre y Apellido: <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                placeholder="Ej. Julio Rodríguez"
                                required
                                class="h-12 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('nombre')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Celular / WhatsApp --}}
                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5 uppercase tracking-wider">
                                Celular / WhatsApp: <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                placeholder="Ej. 11 5544-3322"
                                required
                                class="h-12 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('telefono')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email (Opcional) --}}
                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5 uppercase tracking-wider">
                                Email: <span class="text-text-disabled font-normal text-[11px]">(Opcional)</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="cliente@ejemplo.com"
                                class="h-12 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('email')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center justify-end gap-3.5 pt-6 border-t border-border/30 mt-6">
                        <button
                            type="button"
                            @click="openNewClientModal = false"
                            class="h-11 rounded-xl bg-surface-hover hover:bg-border/60 px-5 text-sm font-bold text-white transition-colors cursor-pointer"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="h-11 rounded-xl bg-primary hover:bg-primary-hover px-7 text-sm font-bold text-white transition-all shadow-md active:scale-95 cursor-pointer"
                        >
                            Guardar Cliente
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>
@endsection