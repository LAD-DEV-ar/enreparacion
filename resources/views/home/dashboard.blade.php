@extends('layout')

@section('main')
    <main class="ml-56 min-h-screen" x-data="{
        openModal: {{ $errors->any() ? 'true' : 'false' }},
        openDetailModal: false,
        openConfirmModal: false,
        openConfirmEntregaModal: false,
        isUpdating: false,
        isEntrega: false,
        search: '',
        selectedReparacion: null,
        draggedItem: null,
        dragOverColumn: null,
        pendingMove: null,

        // Control de Clave de Acceso y Sub-modales
        openClaveSubModal: false,
        tipoClaveSeleccionada: {{ Js::from(old('clave_de_acceso') ? (str_starts_with(old('clave_de_acceso'), 'Patrón') || str_starts_with(old('clave_de_acceso'), 'Patron') ? 'Patrón de desbloqueo' : (str_starts_with(old('clave_de_acceso'), 'PIN') || (old('clave_de_acceso') !== 'Sin clave' && old('clave_de_acceso') !== 'Huella / Face ID') ? 'PIN / Contraseña' : old('clave_de_acceso'))) : 'Sin clave') }},
        tipoClaveConfirmada: {{ Js::from(old('clave_de_acceso') ? (str_starts_with(old('clave_de_acceso'), 'Patrón') || str_starts_with(old('clave_de_acceso'), 'Patron') ? 'Patrón de desbloqueo' : (str_starts_with(old('clave_de_acceso'), 'PIN') || (old('clave_de_acceso') !== 'Sin clave' && old('clave_de_acceso') !== 'Huella / Face ID') ? 'PIN / Contraseña' : old('clave_de_acceso'))) : 'Sin clave') }},
        claveAccesoValor: {{ Js::from(old('clave_de_acceso', 'Sin clave')) }},

        // PIN / Contraseña
        tempPinValor: '',
        mostrarPinTexto: true,

        // Patrón 3x3
        tempPatronSecuencia: [],
        isDrawingPattern: false,
        patternCoords: { x: 0, y: 0 },

        // Visor de Patrón en Detalle
        openPatternViewerModal: false,
        visorPatronSecuencia: [],
        visorPatronTitulo: '',

        recibidas: {{ Js::from($reparacionesRecibidas) }},
        enProceso: {{ Js::from($reparacionesEnProceso) }},
        listas: {{ Js::from($reparacionesListas) }},

        filtradas(lista) {
            if (!this.search || !this.search.trim()) return lista;
            const q = this.search.toLowerCase().trim();
            return lista.filter(item => item.search_target && item.search_target.includes(q));
        },

        onTipoClaveChange(val) {
            if (val === 'PIN / Contraseña') {
                this.tempPinValor = this.extraerPin(this.claveAccesoValor);
                this.openClaveSubModal = true;
            } else if (val === 'Patrón de desbloqueo') {
                this.tempPatronSecuencia = this.extraerSecuenciaPatron(this.claveAccesoValor);
                this.openClaveSubModal = true;
            } else if (val === 'Huella / Face ID') {
                this.claveAccesoValor = 'Huella / Face ID';
                this.tipoClaveConfirmada = 'Huella / Face ID';
                this.openClaveSubModal = false;
            } else {
                this.claveAccesoValor = 'Sin clave';
                this.tipoClaveConfirmada = 'Sin clave';
                this.tempPinValor = '';
                this.tempPatronSecuencia = [];
                this.openClaveSubModal = false;
            }
        },

        cancelarSubModalClave() {
            this.openClaveSubModal = false;
            this.isDrawingPattern = false;
            if (!this.tipoClaveConfirmada || this.tipoClaveConfirmada === 'Sin clave') {
                this.tipoClaveSeleccionada = 'Sin clave';
                this.claveAccesoValor = 'Sin clave';
            } else {
                this.tipoClaveSeleccionada = this.tipoClaveConfirmada;
            }
        },

        guardarSubModalClave() {
            if (this.tipoClaveSeleccionada === 'PIN / Contraseña') {
                const pin = (this.tempPinValor || '').trim();
                if (!pin) {
                    this.tipoClaveSeleccionada = 'Sin clave';
                    this.tipoClaveConfirmada = 'Sin clave';
                    this.claveAccesoValor = 'Sin clave';
                    this.openClaveSubModal = false;
                    return;
                }
                this.claveAccesoValor = pin.startsWith('PIN:') || pin.startsWith('Contraseña:') ? pin : `PIN: ${pin}`;
                this.tipoClaveConfirmada = 'PIN / Contraseña';
                this.openClaveSubModal = false;
            } else if (this.tipoClaveSeleccionada === 'Patrón de desbloqueo') {
                if (this.tempPatronSecuencia.length < 2) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: 'Debes conectar al menos 2 puntos para formar un patrón.',
                            type: 'error'
                        }
                    }));
                    return;
                }
                this.claveAccesoValor = `Patrón: ${this.tempPatronSecuencia.join('-')}`;
                this.tipoClaveConfirmada = 'Patrón de desbloqueo';
                this.openClaveSubModal = false;
            }
        },

        verOEditarClave() {
            if (this.tipoClaveConfirmada === 'PIN / Contraseña' || this.tipoClaveSeleccionada === 'PIN / Contraseña') {
                this.tempPinValor = this.extraerPin(this.claveAccesoValor);
                this.tipoClaveSeleccionada = 'PIN / Contraseña';
                this.openClaveSubModal = true;
            } else if (this.tipoClaveConfirmada === 'Patrón de desbloqueo' || this.tipoClaveSeleccionada === 'Patrón de desbloqueo') {
                this.tempPatronSecuencia = this.extraerSecuenciaPatron(this.claveAccesoValor);
                this.tipoClaveSeleccionada = 'Patrón de desbloqueo';
                this.openClaveSubModal = true;
            }
        },

        extraerPin(texto) {
            if (!texto || texto === 'Sin clave' || texto === 'Huella / Face ID') return '';
            return texto.replace(/^PIN:\s*/i, '').replace(/^Contraseña:\s*/i, '');
        },

        extraerSecuenciaPatron(texto) {
            if (!texto) return [];
            const match = texto.match(/Patr[oó]n:\s*([0-9\-]+)/i);
            if (match) {
                return match[1].split('-').map(Number).filter(n => n >= 1 && n <= 9);
            }
            if (/^[1-9](\-[1-9])+$/.test(texto.trim())) {
                return texto.trim().split('-').map(Number);
            }
            return [];
        },

        esPatron(texto) {
            if (!texto) return false;
            return texto.toLowerCase().includes('patrón') || texto.toLowerCase().includes('patron') || /^[1-9](\-[1-9])+$/.test(texto.trim());
        },

        obtenerPosPunto(num) {
            const mapa = {
                1: { x: 50, y: 50 },
                2: { x: 150, y: 50 },
                3: { x: 250, y: 50 },
                4: { x: 50, y: 150 },
                5: { x: 150, y: 150 },
                6: { x: 250, y: 150 },
                7: { x: 50, y: 250 },
                8: { x: 150, y: 250 },
                9: { x: 250, y: 250 }
            };
            return mapa[num] || { x: 150, y: 150 };
        },

        generarPuntosPolyline(secuencia) {
            if (!secuencia || !secuencia.length) return '';
            return secuencia.map(n => {
                const p = this.obtenerPosPunto(n);
                return `${p.x},${p.y}`;
            }).join(' ');
        },

        obtenerPuntoCercano(x, y) {
            for (let i = 1; i <= 9; i++) {
                const p = this.obtenerPosPunto(i);
                const dist = Math.hypot(x - p.x, y - p.y);
                if (dist <= 36) {
                    return i;
                }
            }
            return null;
        },

        actualizarPunteroDesdeEvento(e, container) {
            if (!container) return;
            const rect = container.getBoundingClientRect();
            const clientX = e.touches && e.touches.length > 0 ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches && e.touches.length > 0 ? e.touches[0].clientY : e.clientY;
            const scaleX = 300 / rect.width;
            const scaleY = 300 / rect.height;
            const x = Math.max(0, Math.min(300, (clientX - rect.left) * scaleX));
            const y = Math.max(0, Math.min(300, (clientY - rect.top) * scaleY));
            this.patternCoords = { x, y };

            const punto = this.obtenerPuntoCercano(x, y);
            if (punto && !this.tempPatronSecuencia.includes(punto)) {
                this.tempPatronSecuencia.push(punto);
            }
        },

        iniciarTrazo(e, container) {
            this.isDrawingPattern = true;
            this.actualizarPunteroDesdeEvento(e, container);
        },

        moverTrazo(e, container) {
            if (!this.isDrawingPattern) return;
            this.actualizarPunteroDesdeEvento(e, container);
        },

        finalizarTrazo() {
            this.isDrawingPattern = false;
        },

        hacerClicEnPunto(num) {
            if (!this.tempPatronSecuencia.includes(num)) {
                this.tempPatronSecuencia.push(num);
            }
        },

        limpiarPatron() {
            this.tempPatronSecuencia = [];
            this.isDrawingPattern = false;
        },

        verPatronEnDetalle(item) {
            if (!item) return;
            this.visorPatronSecuencia = this.extraerSecuenciaPatron(item.clave_de_acceso);
            this.visorPatronTitulo = `${item.codigo_seguimiento} - ${item.dispositivo_marca_modelo}`;
            this.openPatternViewerModal = true;
        },

        columnaLabel(key) {
            if (key === 'recibido') return 'Recibidos';
            if (key === 'en_reparacion' || key === 'En_reparacion') return 'En Reparación';
            if (key === 'listo') return 'Listos';
            return key || '';
        },

        verDetalle(item) {
            this.selectedReparacion = item;
            this.openDetailModal = true;
        },

        detallesDeEntrega(item){
            this.selectedReparacion = item;
            this.openConfirmEntregaModal = true;
        },

        onDragStart(e, item, fromColumn) {
            this.draggedItem = { item: item, from: fromColumn };
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', String(item.id));
            if (e.target) {
                e.target.classList.add('opacity-40');
            }
        },

        onDragEnd(e) {
            if (e && e.target) {
                e.target.classList.remove('opacity-40');
            }
            this.dragOverColumn = null;
        },

        onDragOver(columna) {
            this.dragOverColumn = columna;
        },

        onDragLeave(columna) {
            if (this.dragOverColumn === columna) {
                this.dragOverColumn = null;
            }
        },

        onDrop(targetColumn) {
            const fromCol = this.draggedItem?.from;
            const item = this.draggedItem?.item;
            this.dragOverColumn = null;

            if (!item || !fromCol || fromCol === targetColumn) {
                this.draggedItem = null;
                return;
            }

            this.pendingMove = {
                item: item,
                from: fromCol,
                to: targetColumn
            };
            this.openConfirmModal = true;
        },

        cancelarCambio() {
            this.openConfirmModal = false;
            this.pendingMove = null;
            this.draggedItem = null;
            this.dragOverColumn = null;
        },

        cancelarEntrega(){
            this.openConfirmEntregaModal = false;
        },

        async confirmarEntrega(){
            if (!this.selectedReparacion || this.isEntrega) return;

            this.isEntrega = true;
            const item = this.selectedReparacion;

            try {
                const url = `{{ url('/dashboard/reparaciones') }}/${item.id}/estado`;
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ estado: 'entregado' })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.recibidas = this.recibidas.filter(r => r.id !== item.id);
                    this.enProceso = this.enProceso.filter(r => r.id !== item.id);
                    this.listas = this.listas.filter(r => r.id !== item.id);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.message || 'Reparación marcada como entregada.',
                            type: 'success'
                        }
                    }));

                    this.openConfirmEntregaModal = false;
                    this.selectedReparacion = null;
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.message || 'Error al confirmar la entrega.',
                            type: 'error'
                        }
                    }));
                }
            } catch (error) {
                console.error('Error al confirmar entrega:', error);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: 'Hubo un error de conexión al confirmar la entrega.',
                        type: 'error'
                    }
                }));
            } finally {
                this.isEntrega = false;
            }
        },

        async confirmarCambio() {
            if (!this.pendingMove || this.isUpdating) return;

            this.isUpdating = true;
            const { item, from, to } = this.pendingMove;

            try {
                const url = `{{ url('/dashboard/reparaciones') }}/${item.id}/estado`;
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ estado: to })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Quitar de la columna de origen
                    if (from === 'recibido') {
                        this.recibidas = this.recibidas.filter(r => r.id !== item.id);
                    } else if (from === 'en_reparacion') {
                        this.enProceso = this.enProceso.filter(r => r.id !== item.id);
                    } else if (from === 'listo') {
                        this.listas = this.listas.filter(r => r.id !== item.id);
                    }

                    // Actualizar item
                    const updatedItem = {
                        ...item,
                        estado: this.columnaLabel(to),
                        estado_slug: to
                    };

                    // Agregar a la columna de destino
                    if (to === 'recibido') {
                        this.recibidas.unshift(updatedItem);
                    } else if (to === 'en_reparacion') {
                        this.enProceso.unshift(updatedItem);
                    } else if (to === 'listo') {
                        this.listas.unshift(updatedItem);
                    }

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.message || `Reparación movida a ${this.columnaLabel(to)}`,
                            type: 'success'
                        }
                    }));

                    this.openConfirmModal = false;
                    this.pendingMove = null;
                    this.draggedItem = null;
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.message || 'Error al actualizar el estado de la reparación.',
                            type: 'error'
                        }
                    }));
                }
            } catch (error) {
                console.error('Error al actualizar estado:', error);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: 'Hubo un error de conexión al actualizar el estado.',
                        type: 'error'
                    }
                }));
            } finally {
                this.isUpdating = false;
            }
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
                ESTADOS DE REPARACIONES (Tablero Kanban Drag & Drop)
            ========================================== --}}

            <div class="mt-8 grid grid-cols-3 gap-12">


                {{-- =====================================
                    COLUMNA: RECIBIDOS
                ====================================== --}}

                <div
                    class="flex h-[560px] flex-col rounded-[30px] border-4 p-6 transition-all duration-200"
                    :class="dragOverColumn === 'recibido' ? 'border-primary ring-4 ring-primary/25 bg-primary/5' : 'border-border bg-background'"
                    @dragover.prevent="onDragOver('recibido')"
                    @dragenter.prevent="onDragOver('recibido')"
                    @dragleave="onDragLeave('recibido')"
                    @drop.prevent="onDrop('recibido')"
                >

                    <div class="flex items-center justify-between pb-3">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            Recibidos
                        </h2>
                        <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-surface-hover px-2.5 text-xs font-bold text-text-secondary border border-border/40" x-text="recibidas.length"></span>
                    </div>

                    {{-- Estado vacío --}}
                    <div
                        x-show="recibidas.length === 0"
                        class="flex flex-1 flex-col items-center justify-center select-none pointer-events-none"
                    >
                        <span class="text-8xl font-medium leading-none text-text-disabled">
                            0
                        </span>
                        <span class="mt-2 text-3xl text-text-disabled">
                            Recibidos
                        </span>
                    </div>

                    {{-- Lista de tarjetas --}}
                    <x-scrollbar
                        x-show="recibidas.length > 0"
                        class="flex flex-1 flex-col gap-4"
                        variant="dark"
                        size="sm"
                        :rounded="true"
                        :hover="true"
                    >
                        <template x-for="item in filtradas(recibidas)" :key="item.id">
                            <div
                                draggable="true"
                                @dragstart="onDragStart($event, item, 'recibido')"
                                @dragend="onDragEnd($event)"
                                class="rounded-[22px] bg-[#1e2938] border border-[#2b3a4d] p-4.5 shadow-lg hover:border-primary/50 transition-all flex flex-col justify-between gap-3.5 cursor-grab active:cursor-grabbing select-none"
                            >
                                {{-- Header: Código y Tiempo --}}
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="font-bold text-[#8ba0b8] bg-[#141c25] px-2.5 py-0.5 rounded-lg border border-border/40 shrink-0" x-text="item.codigo_seguimiento"></span>
                                    <span class="text-xs italic font-medium text-[#738294] truncate" x-text="item.tiempo_relativo"></span>
                                </div>

                                {{-- Cuerpo: Cliente, Dispositivo y Falla --}}
                                <div class="flex flex-col gap-1 min-w-0">
                                    <h3 class="text-sm font-bold text-white tracking-tight truncate" :title="item.cliente_nombre" x-text="item.cliente_nombre"></h3>
                                    <div class="text-xs font-semibold text-primary-light tracking-tight truncate" :title="item.dispositivo_marca_modelo" x-text="item.dispositivo_marca_modelo"></div>
                                    <p class="text-xs font-medium text-text-secondary truncate mt-0.5" :title="item.falla_reportada" x-text="item.falla_reportada"></p>
                                </div>

                                {{-- Footer: Botón Acción --}}
                                <div class="flex items-center justify-end pt-1 border-t border-border/20">
                                    <button
                                        type="button"
                                        @click.stop="verDetalle(item)"
                                        class="flex h-9 w-11 items-center justify-center rounded-xl bg-[#0081cc] text-white shadow-md hover:bg-primary-hover active:scale-95 transition-all cursor-pointer"
                                        title="Ver detalles de la reparación"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </x-scrollbar>

                </div>



                {{-- =====================================
                    COLUMNA: EN REPARACIÓN
                ====================================== --}}

                <div
                    class="flex h-[560px] flex-col rounded-[30px] border-4 p-6 transition-all duration-200"
                    :class="dragOverColumn === 'en_reparacion' ? 'border-primary ring-4 ring-primary/25 bg-primary/5' : 'border-border bg-surface'"
                    @dragover.prevent="onDragOver('en_reparacion')"
                    @dragenter.prevent="onDragOver('en_reparacion')"
                    @dragleave="onDragLeave('en_reparacion')"
                    @drop.prevent="onDrop('en_reparacion')"
                >

                    <div class="flex items-center justify-between pb-3">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            En Reparación
                        </h2>
                        <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-surface-hover px-2.5 text-xs font-bold text-text-secondary border border-border/40" x-text="enProceso.length"></span>
                    </div>

                    {{-- Estado vacío --}}
                    <div
                        x-show="enProceso.length === 0"
                        class="flex flex-1 items-center justify-center select-none pointer-events-none"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-12 w-12 text-border""
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M7 10h3V7L6.5 3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1-3 3l-6-6a6 6 0 0 1-8-8L7 10Z"
                            />
                        </svg>
                    </div>

                    {{-- Lista de tarjetas --}}
                    <x-scrollbar
                        x-show="enProceso.length > 0"
                        class="flex flex-1 flex-col gap-4"
                        variant="dark"
                        size="sm"
                        :rounded="true"
                        :hover="true"
                    >
                        <template x-for="item in filtradas(enProceso)" :key="item.id">
                            <div
                                draggable="true"
                                @dragstart="onDragStart($event, item, 'en_reparacion')"
                                @dragend="onDragEnd($event)"
                                class="rounded-[22px] bg-[#1e2938] border border-[#2b3a4d] p-4.5 shadow-lg hover:border-primary/50 transition-all flex flex-col justify-between gap-3.5 cursor-grab active:cursor-grabbing select-none"
                            >
                                {{-- Header: Código y Tiempo --}}
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="font-bold text-[#8ba0b8] bg-[#141c25] px-2.5 py-0.5 rounded-lg border border-border/40 shrink-0" x-text="item.codigo_seguimiento"></span>
                                    <span class="text-xs italic font-medium text-[#738294] truncate" x-text="item.tiempo_relativo"></span>
                                </div>

                                {{-- Cuerpo: Cliente, Dispositivo y Falla --}}
                                <div class="flex flex-col gap-1 min-w-0">
                                    <h3 class="text-sm font-bold text-white tracking-tight truncate" :title="item.cliente_nombre" x-text="item.cliente_nombre"></h3>
                                    <div class="text-xs font-semibold text-primary-light tracking-tight truncate" :title="item.dispositivo_marca_modelo" x-text="item.dispositivo_marca_modelo"></div>
                                    <p class="text-xs font-medium text-text-secondary truncate mt-0.5" :title="item.falla_reportada" x-text="item.falla_reportada"></p>
                                </div>

                                {{-- Footer: Botón Acción --}}
                                <div class="flex items-center justify-end pt-1 border-t border-border/20">
                                    <button
                                        type="button"
                                        @click.stop="verDetalle(item)"
                                        class="flex h-9 w-11 items-center justify-center rounded-xl bg-[#0081cc] text-white shadow-md hover:bg-primary-hover active:scale-95 transition-all cursor-pointer"
                                        title="Ver detalles de la reparación"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </x-scrollbar>

                </div>



                {{-- =====================================
                    COLUMNA: LISTOS
                ====================================== --}}

                <div
                    class="flex h-[560px] flex-col rounded-[30px] border-4 p-6 transition-all duration-200"
                    :class="dragOverColumn === 'listo' ? 'border-primary ring-4 ring-primary/25 bg-primary/5' : 'border-primary-hover bg-surface-hover'"
                    @dragover.prevent="onDragOver('listo')"
                    @dragenter.prevent="onDragOver('listo')"
                    @dragleave="onDragLeave('listo')"
                    @drop.prevent="onDrop('listo')"
                >

                    <div class="flex items-center justify-between pb-3">
                        <h2 class="text-xl font-bold text-white tracking-wide">
                            Listos
                        </h2>
                        <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-primary/20 px-2.5 text-xs font-bold text-primary-light border border-primary/30" x-text="listas.length"></span>
                    </div>

                    {{-- Estado vacío --}}
                    <div
                        x-show="listas.length === 0"
                        class="flex flex-1 items-center justify-center select-none pointer-events-none"
                    >
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

                    {{-- Lista de tarjetas --}}
                    <x-scrollbar
                        x-show="listas.length > 0"
                        class="flex flex-1 flex-col gap-4"
                        variant="primary"
                        size="sm"
                        :rounded="true"
                        :hover="true"
                    >
                        <template x-for="item in filtradas(listas)" :key="item.id">
                            <div
                                draggable="true"
                                @dragstart="onDragStart($event, item, 'listo')"
                                @dragend="onDragEnd($event)"
                                class="rounded-[22px] bg-[#1e2938] border border-[#2b3a4d] p-4.5 shadow-lg hover:border-primary/50 transition-all flex flex-col justify-between gap-3.5 cursor-grab active:cursor-grabbing select-none"
                            >
                                {{-- Header: Código y Tiempo --}}
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="font-bold text-[#8ba0b8] bg-[#141c25] px-2.5 py-0.5 rounded-lg border border-border/40 shrink-0" x-text="item.codigo_seguimiento"></span>
                                    <span class="text-xs italic font-medium text-[#738294] truncate" x-text="item.tiempo_relativo"></span>
                                </div>

                                {{-- Cuerpo: Cliente, Dispositivo y Falla --}}
                                <div class="flex flex-col gap-1 min-w-0">
                                    <h3 class="text-sm font-bold text-white tracking-tight truncate" :title="item.cliente_nombre" x-text="item.cliente_nombre"></h3>
                                    <div class="text-xs font-semibold text-primary-light tracking-tight truncate" :title="item.dispositivo_marca_modelo" x-text="item.dispositivo_marca_modelo"></div>
                                    <p class="text-xs font-medium text-text-secondary truncate mt-0.5" :title="item.falla_reportada" x-text="item.falla_reportada"></p>
                                </div>

                                {{-- Footer: Botón Acción --}}
                                <div class="flex gap-2 items-center justify-end pt-1 border-t border-border/20">
                                    <button
                                        type="button"
                                        @click.stop="verDetalle(item)"
                                        class="flex h-9 w-11 items-center justify-center rounded-xl bg-[#0081cc] text-white shadow-md hover:bg-primary-hover active:scale-95 transition-all cursor-pointer"
                                        title="Ver detalles de la reparación"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        @click.stop="detallesDeEntrega(item)"
                                        class="flex h-9 w-auto p-2 items-center justify-center rounded-xl bg-success/50 text-white shadow-md hover:bg-success/70 active:scale-95 transition-all cursor-pointer"
                                        title="Ver detalles de la reparación"
                                    >
                                        <span class="text-sm font-bold tracking-wide uppercase text-text-primary">Entregar</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </x-scrollbar>

                </div>

            </div>

        </div> </div>

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
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
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
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
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
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('email')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Falla reportada --}}
                            <div>
                                <label for="falla_reportada" class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Falla reportada:
                                </label>
                                <textarea
                                    id="falla_reportada"
                                    name="falla_reportada"
                                    rows="2"
                                    placeholder="Detalle de la falla"
                                    class="w-full rounded-xl bg-[#1c2530] p-3 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner resize-none"
                                >{{ old('falla_reportada') }}</textarea>
                                @error('falla_reportada')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Seña del cliente --}}
                            <div>
                                <label for="sena" class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Seña del cliente:
                                </label>
                                <input
                                    type="number"
                                    step="any"
                                    name="sena"
                                    id="sena"
                                    value="{{ old('sena') }}"
                                    placeholder="$$$"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
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
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="w-7 h-7 text-primary"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7 10h3V7L6.5 3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1-3 3l-6-6a6 6 0 0 1-8-8L7 10Z"
                                    />
                                </svg>
                            </div>

                            {{-- Input Marca y modelo --}}
                            <div>
                                <input
                                    type="text"
                                    name="marca_y_modelo"
                                    value="{{ old('marca_y_modelo') }}"
                                    placeholder="Marca y modelo"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
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
                                
                                {{-- Input hidden para persistir el valor de la clave en el formulario --}}
                                <input type="hidden" name="clave_de_acceso" :value="claveAccesoValor">

                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <select
                                            x-model="tipoClaveSeleccionada"
                                            @change="onTipoClaveChange($event.target.value)"
                                            class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 pr-10 text-sm font-semibold text-white outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner appearance-none cursor-pointer"
                                        >
                                            <option value="Sin clave">Sin clave</option>
                                            <option value="PIN / Contraseña">PIN / Contraseña</option>
                                            <option value="Patrón de desbloqueo">Patrón de desbloqueo</option>
                                            <option value="Huella / Face ID">Huella / Face ID</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-200">
                                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Botón Ver / Editar (Visible solo cuando se configuró una clave) --}}
                                    <button
                                        type="button"
                                        x-show="claveAccesoValor && claveAccesoValor !== 'Sin clave' && claveAccesoValor !== 'Huella / Face ID'"
                                        x-cloak
                                        @click="verOEditarClave()"
                                        class="h-10 sm:h-11 px-3 sm:px-4 rounded-xl bg-[#0081cc]/20 hover:bg-[#0081cc]/40 text-[#33b4ff] border border-[#0081cc]/40 transition-all flex items-center gap-1.5 text-xs sm:text-sm font-bold cursor-pointer shrink-0 shadow-sm"
                                        title="Ver o editar la clave configurada"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span>Ver / Editar</span>
                                    </button>
                                </div>

                                @error('clave_de_acceso')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror

                                {{-- Badge informativo con la clave configurada --}}
                                <div
                                    x-show="claveAccesoValor && claveAccesoValor !== 'Sin clave'"
                                    x-cloak
                                    class="mt-2 flex items-center justify-between px-3 py-1.5 rounded-xl bg-[#141c25] border border-border/30 text-xs"
                                >
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="text-text-secondary">Configurada:</span>
                                        <span class="text-[#33b4ff] font-mono font-bold truncate" x-text="claveAccesoValor"></span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="claveAccesoValor = 'Sin clave'; tipoClaveConfirmada = 'Sin clave'; tipoClaveSeleccionada = 'Sin clave'"
                                        class="text-danger hover:text-red-400 font-bold ml-2 cursor-pointer text-[11px] hover:underline shrink-0"
                                        title="Quitar clave"
                                    >
                                        Quitar
                                    </button>
                                </div>
                            </div>

                            {{-- IMEI / Nº Serie --}}
                            <div>
                                <label for="imei_o_serie" class="block text-sm sm:text-base font-bold text-white mb-1">
                                    IMEI/Nº Serie:
                                </label>
                                <input
                                    type="text"
                                    name="imei_o_serie"
                                    id="imei_o_serie"
                                    value="{{ old('imei_o_serie') }}"
                                    placeholder="IMEI/Nº Serie"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
                                >
                                @error('imei_o_serie')
                                    <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Costo Estimado --}}
                            <div>
                                <label for="costo_estimado" class="block text-sm sm:text-base font-bold text-white mb-1">
                                    Valor:
                                </label>
                                <input
                                    type="number"
                                    step="any"
                                    name="costo_estimado"
                                    id="costo_estimado"
                                    value="{{ old('costo_estimado') }}"
                                    placeholder="$$$"
                                    class="h-10 sm:h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-gray-300/80 outline-none border border-transparent focus:border-[#0081cc] focus:bg-[#5b6777] focus:ring-1 focus:ring-[#0081cc]/30 transition-all shadow-inner"
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
            SUB-MODAL CLAVE DE ACCESO (PIN & Patrón 3x3)
        ========================================== --}}
        <div
            x-show="openClaveSubModal"
            x-cloak
            @keydown.escape.window="if (openClaveSubModal) cancelarSubModalClave()"
            class="fixed inset-0 z-[60] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openClaveSubModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="cancelarSubModalClave()"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openClaveSubModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-md rounded-3xl bg-[#141c25] p-5 sm:p-6 shadow-2xl border border-border/40 my-auto text-white"
            >
                {{-- Encabezado Sub-modal --}}
                <div class="flex items-center justify-between border-b border-border/30 pb-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/20 text-primary shrink-0">
                            <template x-if="tipoClaveSeleccionada === 'PIN / Contraseña'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                </svg>
                            </template>
                            <template x-if="tipoClaveSeleccionada === 'Patrón de desbloqueo'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-white tracking-wide" x-text="tipoClaveSeleccionada === 'PIN / Contraseña' ? 'PIN o Contraseña' : 'Patrón de Desbloqueo'"></h3>
                            <p class="text-xs text-text-disabled" x-text="tipoClaveSeleccionada === 'PIN / Contraseña' ? 'Ingresa la clave para desbloquear el equipo' : 'Desliza o haz clic para conectar los puntos'"></p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="cancelarSubModalClave()"
                        class="text-text-disabled hover:text-white transition-colors p-1.5 rounded-xl hover:bg-surface-hover cursor-pointer"
                        title="Cerrar sin guardar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- CONTENIDO 1: PIN / CONTRASEÑA --}}
                <div x-show="tipoClaveSeleccionada === 'PIN / Contraseña'" class="py-2 flex flex-col gap-3">
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider">
                        Contraseña o PIN numérico:
                    </label>
                    <div class="relative">
                        <input
                            :type="mostrarPinTexto ? 'text' : 'password'"
                            x-model="tempPinValor"
                            @keydown.enter.prevent="guardarSubModalClave()"
                            placeholder="Ej: 1234, ABCD, Clave123..."
                            class="h-12 w-full rounded-2xl bg-[#1c2530] px-4 pr-12 text-sm sm:text-base font-semibold text-white placeholder:text-gray-400 outline-none border border-border/40 focus:border-[#0081cc] focus:ring-2 focus:ring-[#0081cc]/25 transition-all shadow-inner"
                        >
                        <button
                            type="button"
                            @click="mostrarPinTexto = !mostrarPinTexto"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-white transition-colors cursor-pointer"
                            title="Ver u ocultar texto"
                        >
                            <svg x-show="!mostrarPinTexto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="mostrarPinTexto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- CONTENIDO 2: PATRÓN 3x3 INTERACTIVO --}}
                <div x-show="tipoClaveSeleccionada === 'Patrón de desbloqueo'" class="py-1 flex flex-col items-center gap-3">
                    
                    {{-- Lienzo Interactivo 3x3 --}}
                    <div
                        class="w-64 h-64 sm:w-72 sm:h-72 relative bg-[#1c2530] rounded-3xl border border-border/40 p-2 shadow-inner select-none touch-none overflow-hidden cursor-crosshair"
                        @mousedown="iniciarTrazo($event, $el)"
                        @mousemove="moverTrazo($event, $el)"
                        @mouseup="finalizarTrazo()"
                        @mouseleave="finalizarTrazo()"
                        @touchstart.prevent="iniciarTrazo($event, $el)"
                        @touchmove.prevent="moverTrazo($event, $el)"
                        @touchend="finalizarTrazo()"
                    >
                        {{-- Capa SVG de Trazos --}}
                        <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 300 300">
                            {{-- Línea principal del patrón conectado --}}
                            <polyline
                                :points="generarPuntosPolyline(tempPatronSecuencia)"
                                fill="none"
                                stroke="#0081cc"
                                stroke-width="6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            {{-- Línea dinámica que sigue al cursor al dibujar --}}
                            <template x-if="isDrawingPattern && tempPatronSecuencia.length > 0">
                                <line
                                    :x1="obtenerPosPunto(tempPatronSecuencia[tempPatronSecuencia.length - 1]).x"
                                    :y1="obtenerPosPunto(tempPatronSecuencia[tempPatronSecuencia.length - 1]).y"
                                    :x2="patternCoords.x"
                                    :y2="patternCoords.y"
                                    stroke="#33b4ff"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-dasharray="6 4"
                                    opacity="0.85"
                                />
                            </template>
                        </svg>

                        {{-- Los 9 Puntos Interactivos --}}
                        @php
                            $puntosCoords = [
                                1 => ['top' => '16.66%', 'left' => '16.66%'],
                                2 => ['top' => '16.66%', 'left' => '50%'],
                                3 => ['top' => '16.66%', 'left' => '83.33%'],
                                4 => ['top' => '50%', 'left' => '16.66%'],
                                5 => ['top' => '50%', 'left' => '50%'],
                                6 => ['top' => '50%', 'left' => '83.33%'],
                                7 => ['top' => '83.33%', 'left' => '16.66%'],
                                8 => ['top' => '83.33%', 'left' => '50%'],
                                9 => ['top' => '83.33%', 'left' => '83.33%'],
                            ];
                        @endphp

                        @foreach($puntosCoords as $num => $pos)
                            <div
                                class="absolute -translate-x-1/2 -translate-y-1/2 flex items-center justify-center cursor-pointer transition-transform duration-150 active:scale-90"
                                style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }}; width: 50px; height: 50px;"
                                @click.stop="hacerClicEnPunto({{ $num }})"
                            >
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="tempPatronSecuencia.includes({{ $num }})
                                        ? 'bg-[#0081cc]/25 border-2 border-[#0081cc] shadow-[0_0_12px_rgba(0,129,204,0.7)] scale-110'
                                        : 'bg-[#273343]/60 border border-border/40 hover:border-primary/50 hover:bg-surface-hover'"
                                >
                                    <template x-if="tempPatronSecuencia.includes({{ $num }})">
                                        <span
                                            class="w-5 h-5 rounded-full bg-[#0081cc] text-white text-[11px] font-bold flex items-center justify-center shadow"
                                            x-text="tempPatronSecuencia.indexOf({{ $num }}) + 1"
                                        ></span>
                                    </template>
                                    <template x-if="!tempPatronSecuencia.includes({{ $num }})">
                                        <span class="w-3 h-3 rounded-full bg-gray-400/60"></span>
                                    </template>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Indicador y botón limpiar --}}
                    <div class="w-full flex items-center justify-between px-1 text-xs">
                        <span class="text-text-secondary truncate">
                            <template x-if="tempPatronSecuencia.length > 0">
                                <span>Secuencia: <strong class="text-primary-light font-mono" x-text="tempPatronSecuencia.join(' → ')"></strong></span>
                            </template>
                            <template x-if="tempPatronSecuencia.length === 0">
                                <span class="italic text-text-disabled">Une al menos 2 puntos</span>
                            </template>
                        </span>

                        <button
                            type="button"
                            @click="limpiarPatron()"
                            class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-border/60 text-xs font-bold text-text-secondary hover:text-white transition-all cursor-pointer flex items-center gap-1 shrink-0"
                            title="Reiniciar trazo"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span>Limpiar</span>
                        </button>
                    </div>

                </div>

                {{-- Pie del Sub-modal: Botones de Acción --}}
                <div class="flex items-center justify-end gap-3 pt-4 mt-2 border-t border-border/30">
                    <button
                        type="button"
                        @click="cancelarSubModalClave()"
                        class="px-4 py-2.5 rounded-xl bg-surface-hover hover:bg-[#364252] text-xs font-bold text-white transition-all cursor-pointer"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        @click="guardarSubModalClave()"
                        class="px-5 py-2.5 rounded-xl bg-[#0081cc] hover:bg-[#33b4ff] active:scale-95 text-xs font-bold text-white transition-all shadow-md cursor-pointer flex items-center gap-1.5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Confirmar Clave</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- =========================================
            MODAL VISOR DE PATRÓN EN DETALLE (3x3 Gráfico)
        ========================================== --}}
        <div
            x-show="openPatternViewerModal"
            x-cloak
            @keydown.escape.window="openPatternViewerModal = false"
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openPatternViewerModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openPatternViewerModal = false"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openPatternViewerModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-sm rounded-3xl bg-[#141c25] p-5 sm:p-6 shadow-2xl border border-border/40 my-auto text-white flex flex-col items-center"
            >
                <div class="w-full flex items-center justify-between pb-3 border-b border-border/30 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/20 text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Patrón de Desbloqueo</h3>
                            <p class="text-[11px] text-text-disabled truncate max-w-[200px]" x-text="visorPatronTitulo"></p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="openPatternViewerModal = false"
                        class="p-1.5 rounded-xl text-text-disabled hover:text-white hover:bg-surface-hover transition-colors cursor-pointer"
                        title="Cerrar visor"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Canvas 3x3 Visor Reconstruido --}}
                <div class="w-64 h-64 relative bg-[#1c2530] rounded-2xl border border-border/40 p-2 shadow-inner select-none overflow-hidden my-2">
                    <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 300 300">
                        <polyline
                            :points="generarPuntosPolyline(visorPatronSecuencia)"
                            fill="none"
                            stroke="#0081cc"
                            stroke-width="6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    @foreach($puntosCoords as $num => $pos)
                        <div
                            class="absolute -translate-x-1/2 -translate-y-1/2 flex items-center justify-center"
                            style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }}; width: 46px; height: 46px;"
                        >
                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center transition-all"
                                :class="visorPatronSecuencia.includes({{ $num }})
                                    ? 'bg-[#0081cc]/25 border-2 border-[#0081cc] shadow-[0_0_10px_rgba(0,129,204,0.7)] scale-105'
                                    : 'bg-[#273343]/60 border border-border/40'"
                            >
                                <template x-if="visorPatronSecuencia.includes({{ $num }})">
                                    <span
                                        class="w-5 h-5 rounded-full bg-[#0081cc] text-white text-[11px] font-bold flex items-center justify-center shadow"
                                        x-text="visorPatronSecuencia.indexOf({{ $num }}) + 1"
                                    ></span>
                                </template>
                                <template x-if="!visorPatronSecuencia.includes({{ $num }})">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-500/50"></span>
                                </template>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Secuencia en texto --}}
                <div class="mt-2 w-full px-3 py-2 rounded-xl bg-[#1c2530] border border-border/30 text-xs font-mono text-center text-primary-light">
                    <span class="text-text-secondary font-sans mr-1">Secuencia:</span>
                    <strong x-text="visorPatronSecuencia.join(' → ')"></strong>
                </div>

                {{-- Botón Cerrar --}}
                <div class="w-full flex justify-end mt-4 pt-3 border-t border-border/30">
                    <button
                        type="button"
                        @click="openPatternViewerModal = false"
                        class="w-full h-10 rounded-xl bg-surface-hover hover:bg-[#364252] text-xs font-bold text-white transition-all cursor-pointer"
                    >
                        Cerrar Visor
                    </button>
                </div>
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
                                'bg-warning/20 text-warning border border-warning/40': selectedReparacion?.estado === 'En_reparacion' || selectedReparacion?.estado === 'En reparacion',
                                'bg-success/20 text-success border border-success/40': selectedReparacion?.estado === 'Listo' || selectedReparacion?.estado === 'Listos'
                            }"
                            x-text="columnaLabel(selectedReparacion?.estado)"
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
                            <div class="rounded-xl bg-[#1c2530] p-3 border border-border/20 flex flex-col justify-between gap-1.5">
                                <span class="block text-xs font-semibold text-text-disabled uppercase tracking-wider">Clave de Acceso</span>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-200 truncate" x-text="selectedReparacion?.clave_de_acceso"></span>
                                    
                                    {{-- Botón para ver patrón si corresponde --}}
                                    <template x-if="esPatron(selectedReparacion?.clave_de_acceso)">
                                        <button
                                            type="button"
                                            @click="verPatronEnDetalle(selectedReparacion)"
                                            class="px-2.5 py-1 rounded-lg bg-[#0081cc]/20 hover:bg-[#0081cc] text-[#33b4ff] hover:text-white border border-[#0081cc]/40 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer shrink-0 shadow-sm"
                                            title="Ver patrón gráfico en 3x3"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>Ver Patrón</span>
                                        </button>
                                    </template>
                                </div>
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

        {{-- =========================================
            MODAL CONFIRMACIÓN DE CAMBIO DE ESTADO (Drag & Drop)
        ========================================== --}}
        <div
            x-show="openConfirmModal"
            x-cloak
            @keydown.escape.window="if (!isUpdating) cancelarCambio()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openConfirmModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="if (!isUpdating) cancelarCambio()"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openConfirmModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-lg rounded-3xl bg-[#141c25] p-6 sm:p-7 shadow-2xl border border-border/40 my-auto"
            >
                {{-- Icono & Título --}}
                <div class="flex items-center gap-4 pb-4 border-b border-border/30">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/20 text-primary shrink-0 border border-primary/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-white tracking-wide">
                            ¿Confirmar cambio de estado?
                        </h3>
                        <p class="text-xs text-text-secondary mt-0.5">
                            La reparación cambiará su posición en el tablero de trabajo.
                        </p>
                    </div>
                </div>

                {{-- Contenido: Tarjeta a mover & Indicador de columnas --}}
                <div class="py-5 flex flex-col gap-4">
                    
                    {{-- Preview de la reparación --}}
                    <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-primary-light bg-primary/10 px-2.5 py-0.5 rounded-lg border border-primary/20" x-text="pendingMove?.item?.codigo_seguimiento"></span>
                            <span class="text-xs italic text-text-disabled" x-text="pendingMove?.item?.tiempo_relativo"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-white truncate" x-text="pendingMove?.item?.cliente_nombre"></span>
                            <span class="text-xs font-semibold text-primary-light truncate max-w-[180px]" x-text="pendingMove?.item?.dispositivo_marca_modelo"></span>
                        </div>
                    </div>

                    {{-- Transición de estados: De -> Hacia --}}
                    <div class="flex items-center justify-center gap-3 bg-[#1c2530]/60 p-3.5 rounded-2xl border border-border/20">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-[#0081cc]/20 text-[#33b4ff] border border-[#0081cc]/40': pendingMove?.from === 'recibido',
                                    'bg-warning/20 text-warning border border-warning/40': pendingMove?.from === 'en_reparacion',
                                    'bg-success/20 text-success border border-success/40': pendingMove?.from === 'listo'
                                }"
                                x-text="columnaLabel(pendingMove?.from)"
                            ></span>
                        </div>

                        {{-- Flecha --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-text-disabled animate-pulse">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>

                        <div class="flex items-center gap-2">
                            <span
                                class="px-3.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-[#0081cc]/20 text-[#33b4ff] border border-[#0081cc]/40': pendingMove?.to === 'recibido',
                                    'bg-warning/20 text-warning border border-warning/40': pendingMove?.to === 'en_reparacion',
                                    'bg-success/20 text-success border border-success/40': pendingMove?.to === 'listo'
                                }"
                                x-text="columnaLabel(pendingMove?.to)"
                            ></span>
                        </div>
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/30">
                    <button
                        type="button"
                        :disabled="isUpdating"
                        @click="cancelarCambio()"
                        class="px-5 py-2.5 rounded-xl bg-surface-hover hover:bg-[#364252] text-sm font-bold text-white transition-all cursor-pointer disabled:opacity-50"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        :disabled="isUpdating"
                        @click="confirmarCambio()"
                        class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover active:scale-[0.98] text-sm font-bold text-white transition-all shadow-md cursor-pointer flex items-center gap-2 disabled:opacity-50"
                    >
                        <svg x-show="isUpdating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isUpdating ? 'Actualizando...' : 'Confirmar Cambio'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- =========================================
            MODAL CONFIRMACIÓN DE CAMBIO DE ESTADO (Drag & Drop)
        ========================================== --}}
        <div
            x-show="openConfirmEntregaModal"
            x-cloak
            @keydown.escape.window="if (!isEntrega) cancelarEntrega()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openConfirmEntregaModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="if (!isEntrega) cancelarEntrega()"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openConfirmEntregaModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-lg rounded-3xl bg-[#141c25] p-6 sm:p-7 shadow-2xl border border-border/40 my-auto"
            >
                {{-- Icono & Título --}}
                <div class="flex items-center gap-4 pb-4 border-b border-border/30">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-success/20 text-success shrink-0 border border-success/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25 19.5 6.75" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-white tracking-wide">
                            ¿Confirmar entrega del equipo?
                        </h3>
                        <p class="text-xs text-text-secondary mt-0.5">
                            La reparación cambiará su estado a entregado.
                        </p>
                    </div>
                </div>

                {{-- Contenido: Tarjeta a mover & Indicador de columnas --}}
                <div class="py-5 flex flex-col gap-4">
                    
                    {{-- Preview de la reparación --}}
                    <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-success bg-success/20 px-2.5 py-0.5 rounded-lg border border-success/20" x-text="selectedReparacion?.codigo_seguimiento"></span>
                            <span class="text-xs italic text-text-disabled" x-text="selectedReparacion?.tiempo_relativo"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-white truncate" x-text="selectedReparacion?.cliente_nombre"></span>
                            <span class="text-xs font-semibold text-primary-light truncate max-w-[180px]" x-text="selectedReparacion?.dispositivo_marca_modelo"></span>
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/30">
                    <button
                        type="button"
                        :disabled="isEntrega"
                        @click="cancelarEntrega()"
                        class="px-5 py-2.5 rounded-xl bg-surface-hover hover:bg-[#364252] text-sm font-bold text-white transition-all cursor-pointer disabled:opacity-50"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        :disabled="isEntrega"
                        @click="confirmarEntrega()"
                        class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover active:scale-[0.98] text-sm font-bold text-white transition-all shadow-md cursor-pointer flex items-center gap-2 disabled:opacity-50"
                    >
                        <svg x-show="isEntrega" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isEntrega ? 'Actualizando...' : 'Confirmar Cambio'"></span>
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection
