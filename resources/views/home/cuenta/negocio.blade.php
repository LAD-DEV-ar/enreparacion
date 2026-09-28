<div class="px-12">

    @php
        $suscripcion = $user->negocio?->suscripcion;
        $plan = $suscripcion?->plan;
    @endphp
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mb-6"">
        {{-- ─── CARD: Mi negocio ─── --}}
        <div class="rounded-2xl bg-surface-hover p-8">

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-text-primary">Mi negocio</h2>
                    <p class="text-xs text-text-secondary">Datos del taller o local de reparación</p>
                </div>
            </div>

            <form x-data="{
                    original: {
                        nombre: '{{ $user->negocio->nombre }}',
                        telefono: '{{ $user->negocio->telefono }}',
                        direccion: '{{ $user->negocio->direccion ?? '' }}'
                    },
                    form: {
                        nombre: '{{ $user->negocio->nombre }}',
                        telefono: '{{ $user->negocio->telefono }}',
                        direccion: '{{ $user->negocio->direccion ?? '' }}'
                    },
                    get hasChanges() {
                        return this.form.nombre !== this.original.nombre ||
                            this.form.telefono !== this.original.telefono ||
                            this.form.direccion !== this.original.direccion;
                    }
                }"
                id="form-negocio" method="POST" action="{{ route('cuenta.update-negocio') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Nombre del negocio --}}
                <div>
                    <label for="nombre" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Nombre del negocio
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        x-model="form.nombre"
                        value="{{ old('nombre', $user->negocio->nombre ?? '') }}"
                        placeholder="Ej: Tecno Reparaciones"
                        class="h-12 w-full rounded-xl border-0 bg-surface px-4 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('nombre') ring-2 ring-danger @enderror"
                    >
                    @error('nombre')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono del negocio --}}
                <div>
                    <label for="negocio_telefono" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Teléfono del negocio
                        <span class="ml-1 font-normal text-text-secondary">(opcional)</span>
                    </label>
                    <input
                        type="tel"
                        id="negocio_telefono"
                        name="telefono"
                        x-model="form.telefono"
                        value="{{ old('telefono', $user->negocio->telefono ?? '') }}"
                        placeholder="Ej: +54 9 11 1234-5678"
                        class="h-12 w-full rounded-xl border-0 bg-surface px-4 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('telefono') ring-2 ring-danger @enderror"
                    >
                    @error('telefono')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dirección --}}
                <div>
                    <label for="direccion" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Dirección
                        <span class="ml-1 font-normal text-text-secondary">(opcional)</span>
                    </label>
                    <input
                        type="text"
                        id="direccion"
                        name="direccion"
                        x-model="form.direccion"
                        value="{{ old('direccion', $user->negocio->direccion ?? '') }}"
                        placeholder="Ej: Av. Corrientes 1234, CABA"
                        class="h-12 w-full rounded-xl border-0 bg-surface px-4 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('direccion') ring-2 ring-danger @enderror"
                    >
                    @error('direccion')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-1">
                    <button
                        type="button"
                        @click="if (hasChanges) openModifyModal = true"
                        :disabled="!hasChanges"
                        :class="(hasChanges) ? 'bg-primary transition-opacity hover:opacity-90 text-white cursor-pointer' : 'bg-white/20 text-white/30 cursor-not-allowed'"
                        class="h-11 rounded-xl px-6 text-sm font-semibold"
                    >
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
        {{-- =========================================
            CARD: Mi suscripción
        ========================================== --}}
        <div class="rounded-2xl bg-surface-hover p-8">

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-text-primary">Mi suscripción</h2>
                    <p class="text-xs text-text-secondary">Plan, facturación y medio de pago</p>
                </div>
            </div>

            @if (! $suscripcion || $suscripcion->tipo === 'trial')

                <div class="rounded-xl bg-surface p-6">
                    <p class="text-sm text-text-secondary">
                        @if ($suscripcion?->tipo === 'trial')
                            Estás en período de prueba gratis, que vence el {{ $suscripcion->fin?->format('d/m/Y') }}.
                        @else
                            Aún no tenés una suscripción con Mercado Pago.
                        @endif
                    </p>
                    <a
                        href="{{ route('planes.index') }}"
                        class="mt-4 inline-flex h-11 items-center rounded-xl bg-primary px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                    >
                        Ver planes disponibles
                    </a>
                </div>

            @elseif ($suscripcion->mp_status === 'pending')

                <div class="rounded-xl bg-surface p-6">
                    <p class="text-sm text-text-secondary">Tu suscripción está pendiente de pago. Completá el pago para activar el plan.</p>
                    <a
                        href="{{ route('planes.index') }}"
                        class="mt-4 inline-flex h-11 items-center rounded-xl bg-primary px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                    >
                        Continuar el pago
                    </a>
                </div>

            @else

                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div class="rounded-xl bg-surface p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Plan</dt>
                        <dd class="mt-1 text-sm font-semibold text-text-primary">{{ $plan?->nombre ?? '—' }}</dd>
                    </div>

                    <div class="rounded-xl bg-surface p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Monto mensual</dt>
                        <dd class="mt-1 text-sm font-semibold text-text-primary">${{ number_format((float) $plan?->precio, 0, ',', '.') }}</dd>
                    </div>

                    @if ($suscripcion->mp_status === 'cancelled')

                        <div class="rounded-xl bg-surface p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex rounded-full bg-danger/15 px-3 py-1 text-xs font-semibold text-danger">Cancelada</span>
                            </dd>
                        </div>

                        <div class="rounded-xl bg-surface p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Acceso hasta</dt>
                            <dd class="mt-1 text-sm font-semibold text-text-primary">{{ $suscripcion->fin?->format('d/m/Y') ?? '—' }}</dd>
                        </div>

                    @else

                        <div class="rounded-xl bg-surface p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Próxima facturación</dt>
                            <dd class="mt-1 text-sm font-semibold text-text-primary">{{ $suscripcion->proxima_facturacion?->format('d/m/Y') ?? '—' }}</dd>
                        </div>

                        <div class="rounded-xl bg-surface p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-secondary">Acceso hasta</dt>
                            <dd class="mt-1 text-sm font-semibold text-text-primary">{{ $suscripcion->fin?->format('d/m/Y') ?? '—' }}</dd>
                        </div>

                    @endif

                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($suscripcion->mp_status === 'cancelled')

                        <a
                            href="{{ route('planes.index') }}"
                            class="inline-flex h-11 items-center rounded-xl bg-primary px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                        >
                            Reactivar suscripción
                        </a>

                    @else

                        <form id="form-actualizar-metodo" action="{{ route('planes.tarjeta') }}" method="post">
                            @csrf
                            <button
                                type="button"
                                @click="openConfirmModal = true"
                                class="inline-flex h-11 items-center rounded-xl bg-primary px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                            >
                                Actualizar medio de pago
                            </button>
                        </form>

                        <form id="form-cancelar-suscripcion" action="{{ route('planes.cancelar') }}" method="post">
                            @csrf
                            <button
                                type="button"
                                @click="openCancelModal = true"
                                class="inline-flex h-11 items-center rounded-xl bg-danger/10 px-6 text-sm font-semibold text-danger transition-opacity hover:opacity-90 cursor-pointer"
                            >
                                Cancelar suscripción
                            </button>
                        </form>

                    @endif
                </div>

                @if ($suscripcion->mp_status === 'cancelled')
                    <p class="mt-6 text-xs text-text-secondary">No se te va a volver a cobrar. Conservás el acceso hasta el final del período ya pagado.</p>
                @endif

            @endif

        </div>
    </div>

{{-- /card Mi suscripción --}}

    {{-- =========================================
        MODAL: Confirmar cancelación
    ========================================== --}}
    <x-modal-confirmacion
        form="form-actualizar-metodo"
        tit="Cambiar mi medio de pago"
        des="¿Seguro que quieres cambiar tu medio de pago?, lo vas a podrar gestionar totalmente por medio de Mercado Pago"
        botcancel="Cancelar"
        botconfirm="Cambiar medio de pago"
    />
    <x-modal-confirmacion
        form="form-cancelar-suscripcion"
        nombre="Cancel"
        tit="Cancelar mi suscripcion" 
        des="No se te va a volver a cobrar. Vas a mantener el acceso hasta el {{ $suscripcion->fin?->format('d/m/Y') }}" 
        botcancel="Mantener suscripción" 
        botconfirm="Sí, cancelar"
        colorbotconfirm="bg-danger"
    />
    <x-modal-confirmacion
        form="form-negocio"
        nombre="Modify"
        tit="¿Guardar cambios?"
        des="Se actualizarán tus datos personales."
        botconfirm="Guardar cambios"
    />
</div>