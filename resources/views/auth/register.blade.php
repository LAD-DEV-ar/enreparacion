@extends('layout')

@section('main')
    <main class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-[#131a22]">
        <div class="w-full max-w-[960px] min-h-[520px] grid grid-cols-1 md:grid-cols-2 rounded-[32px] overflow-hidden shadow-2xl shadow-black/70 border border-slate-800/40">
            
            {{-- SECCIÓN IZQUIERDA: MARCA / SLOGAN --}}
            <div class="bg-gradient-to-br from-[#2c3948] via-[#202a36] to-[#171f28] p-10 md:p-14 flex flex-col justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight">
                        EnReparacion
                    </h1>
                    <p class="mt-6 text-lg md:text-xl text-slate-200 font-normal leading-relaxed max-w-sm">
                        Gestiona tu negocio de reparación de forma simple, organizada y eficiente.
                    </p>
                </div>

                <div class="mt-12 md:mt-0 relative z-10">
                    <p class="text-xs text-slate-400 font-normal tracking-wide">
                        ©2026 laddev. Todos los derechos reservados
                    </p>
                </div>
            </div>

            {{-- SECCIÓN DERECHA: FORMULARIO DE REGISTRO --}}
            <div class="bg-[#171f28] p-10 md:p-14 flex flex-col justify-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-8 tracking-tight">
                    Registrate
                </h2>

                <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                    @csrf

                    {{-- Campo Nombre --}}
                    <div>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            placeholder="Tu nombre"
                            required
                            autofocus
                            class="w-full h-14 px-6 rounded-2xl bg-[#e6f7ff] text-[#1e293b] placeholder-[#64748b] font-medium text-base md:text-lg outline-none focus:ring-2 focus:ring-[#0081cc] focus:bg-white transition-all duration-200 @error('name') border-2 border-red-500 @enderror"
                        >
                        @error('name')
                            <p class="text-red-400 text-xs mt-1 px-3 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Email --}}
                    <div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            required
                            class="w-full h-14 px-6 rounded-2xl bg-[#e6f7ff] text-[#1e293b] placeholder-[#64748b] font-medium text-base md:text-lg outline-none focus:ring-2 focus:ring-[#0081cc] focus:bg-white transition-all duration-200 @error('email') border-2 border-red-500 @enderror"
                        >
                        @error('email')
                            <p class="text-red-400 text-xs mt-1 px-3 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Contraseña --}}
                    <div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Contraseña"
                            required
                            class="w-full h-14 px-6 rounded-2xl bg-[#e6f7ff] text-[#1e293b] placeholder-[#64748b] font-medium text-base md:text-lg outline-none focus:ring-2 focus:ring-[#0081cc] focus:bg-white transition-all duration-200 @error('password') border-2 border-red-500 @enderror"
                        >
                        @error('password')
                            <p class="text-red-400 text-xs mt-1 px-3 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón de envío --}}
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full h-14 rounded-2xl bg-[#0081cc] hover:bg-[#33b4ff] active:scale-[0.99] text-white font-bold text-lg shadow-lg hover:shadow-cyan-500/20 transition-all duration-200 cursor-pointer flex items-center justify-center"
                        >
                            Registrarse
                        </button>
                    </div>

                    {{-- Enlace a Login --}}
                    <p class="pt-2 text-center text-sm text-slate-400">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="text-[#0081cc] hover:text-[#33b4ff] font-semibold hover:underline transition-colors">
                            Inicia sesión
                        </a>
                    </p>
                </form>
            </div>

        </div>
    </main>
@endsection
