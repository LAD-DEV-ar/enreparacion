@props([
    'action' => null,
])

@php
    $formAction = $action ?? (request()->routeIs('dashboard.*') ? route('dashboard.store') : route('reparaciones.store'));
@endphp

{{-- =========================================================================
    COMPONENTE: MODAL REUTILIZABLE DE NUEVA REPARACIÓN
    Compatible con Alpine.js en Dashboard, Reparaciones, etc.
========================================================================== --}}
<div
    x-show="openNewModal"
    x-cloak
    @keydown.escape.window="cerrarModalNuevaReparacion()"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop con desenfoque --}}
    <div
        x-show="openNewModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="cerrarModalNuevaReparacion()"
        class="fixed inset-0 bg-black/75 backdrop-blur-sm"
    ></div>

    {{-- Modal Box con x-scrollbar --}}
    <div
        x-show="openNewModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative z-10 w-full max-w-4xl max-h-[90vh] rounded-3xl bg-[#141c25] shadow-2xl border border-border/30 overflow-hidden my-auto flex flex-col"
    >
        <x-scrollbar
            class="p-5 sm:p-7"
            max-height="90vh"
            variant="dark"
            size="sm"
            :rounded="true"
            :hover="true"
        >
            <form action="{{ $formAction }}" method="POST">
                @csrf

                {{-- Encabezado Modal --}}
                <div class="flex items-center justify-between border-b border-border/30 pb-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/20 text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white tracking-wide">Nueva Orden de Reparación</h3>
                            <p class="text-xs text-text-disabled">Registra el cliente, equipo y detalles de ingreso</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="cerrarModalNuevaReparacion()"
                        class="text-text-disabled hover:text-white transition-colors p-1.5 rounded-xl hover:bg-surface-hover cursor-pointer"
                        title="Cerrar modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Grid 2 Columnas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                    {{-- COLUMNA IZQUIERDA: CLIENTE / FALLA / SEÑA --}}
                    <div class="flex flex-col gap-3 rounded-2xl bg-[#273343] p-4 sm:p-5 border border-border/20">

                        <div class="flex items-center justify-between pb-1 border-b border-white/10">
                            <h4 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                Cliente
                            </h4>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Nombre y Apellido *</label>
                            <input
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                placeholder="Nombre completo"
                                required
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('nombre')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Teléfono / Celular *</label>
                            <input
                                type="text"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                placeholder="Ej: 11 2345-6789"
                                required
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('telefono')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Email (Opcional)</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="correo@ejemplo.com"
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('email')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Falla reportada *</label>
                            <textarea
                                name="falla_reportada"
                                rows="2"
                                placeholder="Detalle del problema reportado por el cliente"
                                required
                                class="w-full rounded-xl bg-[#1c2530] p-3 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner resize-none"
                            >{{ old('falla_reportada') }}</textarea>
                            @error('falla_reportada')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Seña Inicial ($)</label>
                            <input
                                type="number"
                                step="any"
                                name="sena"
                                value="{{ old('sena') }}"
                                placeholder="0"
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('sena')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- COLUMNA DERECHA: DISPOSITIVO / CLAVE / IMEI / VALOR --}}
                    <div class="flex flex-col gap-3 rounded-2xl bg-[#273343] p-4 sm:p-5 border border-border/20">

                        <div class="flex items-center justify-between pb-1 border-b border-white/10">
                            <h4 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                Dispositivo & Seguridad
                            </h4>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Marca y Modelo *</label>
                            <input
                                type="text"
                                name="marca_y_modelo"
                                value="{{ old('marca_y_modelo') }}"
                                placeholder="Ej: Samsung S23, iPhone 14 Pro, Moto G84"
                                required
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('marca_y_modelo')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Clave de acceso con selector interactivo y soporte de Patrón 3x3 --}}
                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">
                                Clave de Acceso / PIN / Patrón:
                            </label>
                            
                            {{-- Input hidden para persistir el valor de la clave en el formulario --}}
                            <input type="hidden" name="clave_de_acceso" :value="claveAccesoValor">

                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <select
                                        x-model="tipoClaveSeleccionada"
                                        @change="onTipoClaveChange($event.target.value)"
                                        class="h-11 w-full rounded-xl bg-[#1c2530] px-4 pr-10 text-sm font-semibold text-white outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner appearance-none cursor-pointer"
                                    >
                                        <option value="Sin clave">Sin clave</option>
                                        <option value="PIN / Contraseña">PIN / Contraseña</option>
                                        <option value="Patrón de desbloqueo">Patrón de desbloqueo</option>
                                        <option value="Huella / Face ID">Huella / Face ID</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-300">
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Botón Configurar / Dibujar Clave --}}
                                <template x-if="tipoClaveSeleccionada === 'Patrón de desbloqueo' || tipoClaveSeleccionada === 'PIN / Contraseña'">
                                    <button
                                        type="button"
                                        @click="verOEditarClave()"
                                        class="h-11 px-3 sm:px-4 rounded-xl bg-[#0081cc]/20 hover:bg-[#0081cc] text-[#33b4ff] hover:text-white border border-[#0081cc]/40 transition-all flex items-center gap-1.5 text-xs sm:text-sm font-bold cursor-pointer shrink-0 shadow-sm"
                                        :title="tipoClaveSeleccionada === 'Patrón de desbloqueo' ? 'Dibujar o modificar patrón' : 'Ingresar o modificar PIN'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <span x-text="tipoClaveSeleccionada === 'Patrón de desbloqueo' ? 'Dibujar' : 'Editar'"></span>
                                    </button>
                                </template>
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
                                    <span class="text-text-disabled">Configurada:</span>
                                    <span class="text-amber-300 font-mono font-bold truncate" x-text="claveAccesoValor"></span>
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

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">IMEI / Número de Serie</label>
                            <input
                                type="text"
                                name="imei_o_serie"
                                value="{{ old('imei_o_serie') }}"
                                placeholder="Opcional pero recomendado"
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('imei_o_serie')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Presupuesto / Valor Estimado ($)</label>
                            <input
                                type="number"
                                step="any"
                                name="costo_estimado"
                                value="{{ old('costo_estimado') }}"
                                placeholder="Ej: 45000"
                                class="h-11 w-full rounded-xl bg-[#1c2530] px-4 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner"
                            >
                            @error('costo_estimado')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1 uppercase tracking-wider">Notas Internas (Opcional)</label>
                            <textarea
                                name="notas_internas"
                                rows="2"
                                placeholder="Observaciones de ingreso, rayones previos, etc."
                                class="w-full rounded-xl bg-[#1c2530] p-3 text-sm font-semibold text-white placeholder:text-text-disabled outline-none border border-transparent focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all shadow-inner resize-none"
                            >{{ old('notas_internas') }}</textarea>
                            @error('notas_internas')
                                <p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                </div>

                {{-- Pie del modal: Acciones --}}
                <div class="flex items-center justify-end gap-4 pt-5 mt-2 border-t border-border/20">
                    <button
                        type="button"
                        @click="cerrarModalNuevaReparacion()"
                        class="px-5 py-2.5 rounded-xl bg-surface-hover hover:bg-border/60 text-sm font-bold text-white transition-colors cursor-pointer"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="h-11 sm:h-12 rounded-xl bg-primary hover:bg-primary-hover px-8 text-sm font-bold text-white transition-all shadow-md active:scale-[0.98] cursor-pointer"
                    >
                        Guardar Reparación
                    </button>
                </div>
            </form>
        </x-scrollbar>
    </div>
</div>
