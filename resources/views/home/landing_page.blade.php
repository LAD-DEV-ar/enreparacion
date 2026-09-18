@extends('layout')

@section('main')
    <main class="w-full">
        <x-header></x-header>

        <section class="flex p-8 gap-6 sm:gap-36 justify-center flex-col sm:flex-row">
            <div class="flex flex-col justify-center items-center">
                <img src="{{ asset('favicon.svg') }}" alt="Logo enReparacion" class="w-52 h-52">
                <h1 class="font-extrabold text-5xl">EnReparacion</h1>
            </div>
            <div class="flex flex-col justify-center items-center">
                <span class="font-extralight">Ayuda para los</span>
                <h2 class="font-bold text-2xl">Servicos Técnicos.</h2>
            </div>
        </section>
        
        <div class="flex flex-col items-center p-4 gap-2">
            <p class="font-extralight">
                Trabajas en servicio técnico reparando algo? usa nuestra solucion.
            </p>
            <a href="#precios" class="h-14 rounded-2xl bg-primary px-8 text-lg font-bold text-white transition-all hover:bg-primary-hover active:scale-[0.98] shadow-md flex items-center gap-2.5 cursor-pointer shrink-0">Comenzar</a>
        </div>
        <section class="relative w-full overflow-hidden bg-surface">

        <!-- Patrón -->
        <div
            class="absolute inset-0 z-0
                bg-[url('/public/favicon.svg')]
                bg-repeat
                bg-[length:30px_30px]
                rotate-[-40deg]
                scale-250
                opacity-20">
        </div>

        <!-- Transición -->
        <div
            class="absolute top-0 left-0 z-10
                w-full h-48
                bg-gradient-to-b
                from-background
                to-transparent">
        </div>

        <!-- Contenido -->
        <div class="relative z-20 flex justify-center p-8">
            
            <!-- Glass -->
            <div id="quienes_somos"
                class="w-full max-w-5xl rounded-3xl
                    border border-white/10
                    bg-white/5
                    p-4
                    backdrop-blur-md
                    shadow-2xl">
                    <h2 class="text-center p-2 text-3xl font-bold">¿Quienes Somos?</h2>

                <video
                    class="w-full rounded-2xl"
                    autoplay
                    muted
                    loop
                    playsinline
                    controls
                >
                    <source src="{{ asset('video_demo.mp4') }}" type="video/mp4">
                </video>

            </div>

        </div>
        </section>

        <section id="precios" class="flex flex-col items-center justify-center p-8">
            <h2 class="text-center p-2 text-3xl font-bold">Precios/Planes</h2>
            <div
                class="w-full max-w-[400px]
                    rounded-[32px]
                    border-2 border-border
                    bg-surface
                    px-10 pt-5 pb-12
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
        </section>
    </main>
    <x-footer></x-footer>
@endsection