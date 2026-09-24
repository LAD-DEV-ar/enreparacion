@extends('layout')

@section('main')
    <main class="flex flex-col items-center justify-center px-4 py-10">
        <h2 class="text-4xl p-6 text-text-primary mt-6 text-center">Suscribite para seguir usando el sistema</h2>

        @empty($planes)
            <div class="m-2 rounded-3xl border border-white/10 bg-white/5 p-6 md:p-10 backdrop-blur-md shadow-2xl text-[#F5F7FA]">
                <h2 class="text-4xl font-bold text-center mb-4 text-text-primary">No hay planes</h2>
                <p class="text-text-secondary">Actualmente no hay planes disponibles en la plataforma, espera a que un técnico se encargue de arreglar este problema.</p>
            </div>
        @else
            @php $plan = collect($planes)->first(); @endphp

            <div class="w-full max-w-[400px] rounded-[32px] border-2 border-border bg-surface px-10 pt-5 pb-10 mb-10 text-text-primary shadow-lg">
                {{-- Header --}}
                <div class="text-center">
                    <h2 class="text-4xl font-bold tracking-tight">{{ $plan->nombre }}</h2>
                    <div class="mt-2 h-px w-full bg-border"></div>
                </div>

                {{-- Precio --}}
                <div class="mt-8 flex items-center justify-center gap-5">
                    <div class="flex items-start">
                        <span class="mt-1 text-5xl font-medium leading-none">$</span>
                        <span class="text-8xl font-medium leading-[0.8] tracking-tight">{{ number_format((float) $plan->precio, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base leading-none">Por mes</span>
                        <span class="mt-2 text-3xl font-bold leading-none text-primary">ARS</span>
                    </div>
                </div>

                {{-- Descripción --}}
                <p class="mt-3 text-[17px] leading-[1.45] text-text-secondary">{{ $plan->descripcion }}</p>

                {{-- Beneficios --}}
                <ul class="mt-6 space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">Gestionar tus reparaciones</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">Notificar a tus clientes por Email</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">Soporte Técnico</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">Comprobantes de tus reparaciones</span>
                    </li>
                </ul>

                <div class="mt-8 border-t border-border pt-6">
                    <form method="POST" action="{{ route('planes.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan->id }}">

                        <button type="submit" class="w-full rounded-2xl bg-primary px-6 py-4 text-2xl font-bold text-white transition-all duration-200 hover:bg-primary-hover hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                            Suscribirme con Mercado Pago
                        </button>
                    </form>

                    <p class="mt-4 text-center text-sm text-text-secondary">
                        Te vamos a redirigir a Mercado Pago para completar el pago de forma segura.
                    </p>
                </div>
            </div>
        @endempty
    </main>
@endsection