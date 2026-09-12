@extends('layout')

@section('main')
    <main class="w-full">
        <nav class="flex p-8 justify-between items-center">
            <img src="{{ asset('favicon.svg') }}" alt="Logo enReparacion" class="w-12 h-12">
            <div class="flex gap-6">
                <a href="#" class="text-text-secondary">¿Quienes Somos?</a>
                <a href="#" class="text-text-secondary">Precios</a>
            </div>
        </nav>

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
            <button class="h-14 rounded-2xl bg-primary px-8 text-lg font-bold text-white transition-all hover:bg-primary-hover active:scale-[0.98] shadow-md flex items-center gap-2.5 cursor-pointer shrink-0">Comenzar</button>
        </div>
        <section class="relative w-full min-h-screen overflow-hidden bg-surface">

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
            <div
                class="w-full max-w-5xl rounded-3xl
                    border border-white/10
                    bg-white/5
                    p-4
                    backdrop-blur-md
                    shadow-2xl">

                <video
                    class="w-full rounded-2xl"
                    autoplay
                    muted
                    loop
                    playsinline
                >
                    <source src="/videos/demo.mp4" type="video/mp4">
                </video>

            </div>

        </div>

        </section>
    </main>
@endsection