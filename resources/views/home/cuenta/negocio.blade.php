<div class="px-12">

    @php
        $suscripcion = $user->negocio?->suscripcion;
        $plan = $suscripcion?->plan;
    @endphp

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

    </div>{{-- /card Mi suscripción --}}

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
</div>