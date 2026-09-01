@extends('layout')

@section('main') 
    <main class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6 bg-background">
        
        {{-- Encabezado: Icono de correo y título --}}
        <div class="flex flex-col items-center mb-8 sm:mb-10 text-center animate-fade-in-down">
            <div class="mb-4 text-primary flex justify-center">
                {{-- Lock / Keyhole Icon --}}
                <svg
                    class="w-12 h-12 sm:w-14 sm:h-14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect x="4" y="11" width="16" height="11" rx="2.5" />
                    <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                    <circle cx="12" cy="16.25" r="1.25" fill="currentColor" stroke="none" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                Recupera tu Contraseña
            </h1>
        </div>

        {{-- Tarjeta de Verificación --}}
        <div class="w-full max-w-[460px] bg-surface rounded-[28px] sm:rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-black/60 border border-slate-800/30 animate-fade-in-scale">
            
            <h2 class="text-2xl sm:text-[28px] font-bold text-white text-center mb-2 tracking-tight">
                Ingresa tu Email
            </h2>

            <p class="text-text-secondary text-center text-sm leading-relaxed mb-4 max-w-[320px] mx-auto">
                Vas a recibir un correo a tu email con los pasos que debes seguir para recuperar tu contraseña
            </p>

            <form action="{{ route('olvide.store') }}" method="POST">
                @csrf

                <div class="mb-2">
                    <label for="email" class="text-lg text-text-primary font-semibold">Email</label>

                    <input
                    id="email"
                    type="email" 
                    name="email" 
                    class="w-full h-14 px-6 rounded-2xl bg-[#e6f7ff] text-[#1e293b] placeholder-[#64748b] font-medium text-base md:text-lg outline-none focus:ring-2 focus:ring-[#0081cc] focus:bg-white transition-all duration-200 @error('email') border-2 border-red-500 @enderror"
                    required
                    placeholder="Tu Email"
                    >
                    @error('email')
                        <p class="text-red-400 text-xs mt-1 px-3 font-medium">{{ $message }}</p>
                    @enderror
                </div>


                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full h-14 rounded-2xl bg-[#0081cc] hover:bg-[#33b4ff] active:scale-[0.99] text-white font-bold text-lg shadow-lg hover:shadow-cyan-500/20 transition-all duration-200 cursor-pointer flex items-center justify-center"
                    >
                        Enviar
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

    </main>
@endsection