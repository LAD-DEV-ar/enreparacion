@extends('layout')

@section('main')
    <main class="flex flex-col items-center justify-center">
        <h2 class="text-4xl p-6 text-text-primary">Selecciona el plan</h2>
        <div
            class="w-full max-w-[400px]
            rounded-[32px]
            border-2 border-border
            bg-surface
            px-10 pt-5 pb-10
            mb-10
            text-text-primary
            shadow-lg"
                >
                {{-- Header --}}
                <div class="text-center">

                    <h2 class="text-4xl font-bold tracking-tight">
                        Plan Inicial
                    </h2>

                    <div class="mt-2 h-px w-full bg-border"></div>

                </div>


                {{-- Precio --}}
                <div class="mt-8 flex items-center justify-center gap-7">

                    {{-- Precio principal --}}
                    <div class="flex items-start">

                        <span class="mt-1 text-5xl font-medium leading-none">
                            $
                        </span>

                        <span class="text-8xl font-medium leading-[0.8] tracking-tight">
                            0
                        </span>

                    </div>


                    {{-- Promoción --}}
                    <div class="flex flex-col">

                        <span class="text-base leading-none">
                            Primer mes
                        </span>

                        <span class="mt-2 text-5xl font-bold leading-none">
                            Gratis
                        </span>

                    </div>

                </div>


                {{-- Precio anterior --}}
                <div class="mt-5 ml-2">

                    <span
                        class="text-base font-medium
                            text-text-secondary
                            line-through decoration-danger"
                    >
                        $22.999/ARS
                    </span>

                </div>


                {{-- Descripción --}}
                <p
                    class="mt-3
                        text-[17px]
                        leading-[1.45]
                        text-text-secondary"
                >
                    Luego del primer mes, la suscripción
                    pasa a costar $22.999 por mes
                </p>


                {{-- CTA --}}
                <a href="/auth/register">
                    <button
                        class="mt-6 w-full
                            rounded-2xl
                            bg-primary
                            px-6 py-4
                            text-2xl font-bold text-white
                            transition-all duration-200
                            hover:bg-primary-hover
                            hover:-translate-y-0.5
                            active:translate-y-0
                            cursor-pointer"
                    >
                        Suscribirse
                    </button>
                </a>


                {{-- Beneficios --}}
                <ul class="mt-8 space-y-4">

                    {{-- Beneficio --}}
                    <li class="flex items-start gap-3">

                        <svg
                            class="mt-0.5 h-6 w-6 shrink-0 text-primary"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12l5 5L20 7"
                            />
                        </svg>

                        <span class="text-lg leading-tight">
                            Gestionar tus reparaciones
                        </span>

                    </li>


                    {{-- Beneficio --}}
                    <li class="flex items-start gap-3">

                        <svg
                            class="mt-0.5 h-6 w-6 shrink-0 text-primary"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12l5 5L20 7"
                            />
                        </svg>

                        <span class="text-lg leading-tight">
                            Notificar a tus clientes por Email
                        </span>

                    </li>


                    {{-- Beneficio --}}
                    <li class="flex items-start gap-3">

                        <svg
                            class="mt-0.5 h-6 w-6 shrink-0 text-primary"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12l5 5L20 7"
                            />
                        </svg>

                        <span class="text-lg leading-tight">
                            Soporte Técnico
                        </span>

                    </li>


                    {{-- Beneficio --}}
                    <li class="flex items-start gap-3">

                        <svg
                            class="mt-0.5 h-6 w-6 shrink-0 text-primary"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12l5 5L20 7"
                            />
                        </svg>

                        <span class="text-lg leading-tight">
                            Comprobantes de tus reparaciones
                        </span>

                    </li>

                </ul>

            </div>
    </main>
@endsection