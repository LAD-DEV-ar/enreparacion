{{-- =========================================================================
    COMPONENTE: MODAL REUTILIZABLE DE CAMBIO DE ESTADO Y ENTREGA
    Compatible con Alpine.js en cualquier vista (Dashboard, Reparaciones, etc.)
========================================================================== --}}
<div
    x-show="confirmModal.open"
    x-cloak
    @keydown.escape.window="if (!confirmModal.loading) cerrarModalConfirmacion()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop con desenfoque --}}
    <div
        x-show="confirmModal.open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="if (!confirmModal.loading) cerrarModalConfirmacion()"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm"
    ></div>

    {{-- Contenedor del Modal --}}
    <div
        x-show="confirmModal.open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative z-10 w-full max-w-lg rounded-3xl bg-[#141c25] p-6 sm:p-7 shadow-2xl border border-border/40 my-auto"
    >
        {{-- Encabezado: Ícono y Título dinámicos --}}
        <div class="flex items-center gap-4 pb-4 border-b border-border/30">
            {{-- Ícono: Verde de Entrega o Azul de Transición --}}
            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl shrink-0 border transition-colors"
                :class="confirmModal.to === 'entregado'
                    ? 'bg-success/20 text-success border-success/30'
                    : 'bg-primary/20 text-primary border-primary/30'"
            >
                {{-- Ícono Check (Entrega) --}}
                <svg
                    x-show="confirmModal.to === 'entregado'"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.2"
                    stroke="currentColor"
                    class="w-6 h-6"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25 19.5 6.75" />
                </svg>

                {{-- Ícono Flechas (Cambio de estado) --}}
                <svg
                    x-show="confirmModal.to !== 'entregado'"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.2"
                    stroke="currentColor"
                    class="w-6 h-6"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
            </div>

            <div>
                <h3
                    class="text-lg sm:text-xl font-bold text-white tracking-wide"
                    x-text="confirmModal.to === 'entregado' ? '¿Confirmar entrega del equipo?' : '¿Confirmar cambio de estado?'"
                ></h3>
                <p
                    class="text-xs text-text-secondary mt-0.5"
                    x-text="confirmModal.to === 'entregado'
                        ? 'La reparación cambiará su estado a entregado.'
                        : 'La reparación cambiará su estado a ' + columnaLabel(confirmModal.to) + '.'"
                ></p>
            </div>
        </div>

        {{-- Contenido Principal --}}
        <div class="py-5 flex flex-col gap-4">

            {{-- 1. Tarjeta Preview de la Reparación --}}
            <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-bold px-2.5 py-0.5 rounded-lg border"
                        :class="confirmModal.to === 'entregado'
                            ? 'text-success bg-success/20 border-success/30'
                            : 'text-primary-light bg-primary/10 border-primary/20'"
                        x-text="confirmModal.item?.codigo_seguimiento"
                    ></span>
                    <span class="text-xs italic text-text-disabled" x-text="confirmModal.item?.tiempo_relativo"></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm font-bold text-white truncate" x-text="confirmModal.item?.cliente_nombre"></span>
                    <span class="text-xs font-semibold text-primary-light truncate max-w-[180px]" x-text="confirmModal.item?.dispositivo_marca_modelo"></span>
                </div>
            </div>

            {{-- 2. Indicador Visual de Transición (Solo si no es Entrega directa) --}}
            <div
                x-show="confirmModal.to !== 'entregado' && confirmModal.from"
                class="flex items-center justify-center gap-3 bg-[#1c2530]/60 p-3.5 rounded-2xl border border-border/20"
            >
                {{-- Estado Origen --}}
                <div class="flex items-center gap-2">
                    <span
                        class="px-3.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider"
                        :class="{
                            'bg-[#0081cc]/20 text-[#33b4ff] border border-[#0081cc]/40': confirmModal.from === 'recibido',
                            'bg-warning/20 text-warning border border-warning/40': confirmModal.from === 'en_reparacion',
                            'bg-teal-500/20 text-teal-400 border border-teal-500/40': confirmModal.from === 'listo',
                            'bg-rose-500/20 text-rose-400 border border-rose-500/40': confirmModal.from === 'cancelado'
                        }"
                        x-text="columnaLabel(confirmModal.from)"
                    ></span>
                </div>

                {{-- Flecha animada --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-text-disabled animate-pulse">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>

                {{-- Estado Destino --}}
                <div class="flex items-center gap-2">
                    <span
                        class="px-3.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider"
                        :class="{
                            'bg-[#0081cc]/20 text-[#33b4ff] border border-[#0081cc]/40': confirmModal.to === 'recibido',
                            'bg-warning/20 text-warning border border-warning/40': confirmModal.to === 'en_reparacion',
                            'bg-teal-500/20 text-teal-400 border border-teal-500/40': confirmModal.to === 'listo',
                            'bg-rose-500/20 text-rose-400 border border-rose-500/40': confirmModal.to === 'cancelado'
                        }"
                        x-text="columnaLabel(confirmModal.to)"
                    ></span>
                </div>
            </div>

            {{-- 3. Notificación por Correo al Cliente --}}
            <div class="rounded-2xl bg-[#1c2530] p-4 border border-border/30 flex flex-col gap-3">
                {{-- Caso A: El cliente tiene email registrado --}}
                <template x-if="clienteTieneEmail(confirmModal.item)">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border shrink-0"
                                    :class="confirmModal.to === 'entregado'
                                        ? 'bg-success/15 text-success border-success/30'
                                        : 'bg-primary/15 text-primary border-primary/30'"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <div>
                                    <label
                                        for="modal-email-toggle-reparacion"
                                        class="text-sm font-bold text-white cursor-pointer select-none"
                                        x-text="confirmModal.to === 'entregado' ? 'Enviar comprobante de entrega por correo' : 'Notificar al cliente por correo'"
                                    ></label>
                                    <p class="text-xs text-text-secondary truncate max-w-[240px]" x-text="confirmModal.item?.cliente_email"></p>
                                </div>
                            </div>

                            {{-- Switch Toggle --}}
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input
                                    id="modal-email-toggle-reparacion"
                                    type="checkbox"
                                    x-model="enviarEmail"
                                    class="sr-only peer"
                                >
                                <div
                                    class="w-11 h-6 bg-[#273343] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all border border-white/10"
                                    :class="confirmModal.to === 'entregado' ? 'peer-checked:bg-success' : 'peer-checked:bg-primary'"
                                ></div>
                            </label>
                        </div>

                        {{-- Sección opcional: Nota técnica personalizada --}}
                        <div x-show="enviarEmail">
                            <button
                                type="button"
                                @click="mostrarPersonalizarMensaje = !mostrarPersonalizarMensaje"
                                class="text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer pt-1"
                                :class="confirmModal.to === 'entregado'
                                    ? 'text-success hover:text-white'
                                    : 'text-primary-light hover:text-white'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 transition-transform" :class="mostrarPersonalizarMensaje ? 'rotate-90' : ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                                <span x-text="mostrarPersonalizarMensaje ? 'Ocultar nota adicional' : '+ Añadir nota técnica al correo (opcional)'"></span>
                            </button>

                            <div x-show="mostrarPersonalizarMensaje" class="mt-2.5">
                                <textarea
                                    x-model="mensajePersonalizado"
                                    rows="2"
                                    :placeholder="confirmModal.to === 'entregado'
                                        ? 'Ej: Se entregó con cargador y cable original verificado...'
                                        : 'Ej: Ya cambiamos la pantalla y está en etapa de pruebas finales...'"
                                    class="w-full rounded-xl bg-[#141c25] border border-border/40 p-2.5 text-xs text-white placeholder:text-text-disabled outline-none focus:ring-1 transition-all resize-none"
                                    :class="confirmModal.to === 'entregado'
                                        ? 'focus:ring-success focus:border-success'
                                        : 'focus:ring-primary focus:border-primary'"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Caso B: El cliente no tiene email registrado --}}
                <template x-if="!clienteTieneEmail(confirmModal.item)">
                    <div class="flex items-center gap-3 text-text-disabled bg-[#141c25]/80 p-2.5 rounded-xl border border-border/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-warning shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span class="text-xs font-medium">El cliente no tiene email registrado. Solo se actualizará el estado.</span>
                    </div>
                </template>
            </div>

        </div>

        {{-- Pie de Modal: Botones de Acción --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/30">
            <button
                type="button"
                :disabled="confirmModal.loading"
                @click="cerrarModalConfirmacion()"
                class="px-5 py-2.5 rounded-xl bg-surface-hover hover:bg-[#364252] text-sm font-bold text-white transition-all cursor-pointer disabled:opacity-50"
            >
                Cancelar
            </button>

            <button
                type="button"
                :disabled="confirmModal.loading"
                @click="confirmarCambioEstado()"
                class="px-6 py-2.5 rounded-xl active:scale-[0.98] text-sm font-bold text-white transition-all shadow-md cursor-pointer flex items-center gap-2 disabled:opacity-50"
                :class="confirmModal.to === 'entregado'
                    ? 'bg-success hover:bg-success/80'
                    : 'bg-primary hover:bg-primary-hover'"
            >
                {{-- Spinner animado --}}
                <svg x-show="confirmModal.loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span x-text="confirmModal.loading
                    ? 'Actualizando...'
                    : (confirmModal.to === 'entregado' ? 'Confirmar Entrega' : 'Confirmar Cambio')">
                </span>
            </button>
        </div>
    </div>
</div>
