@extends('layout')

@section('main')
    <main class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6 bg-background">
        
        {{-- Encabezado: Icono de negocio y mensaje informativo --}}
        <div class="flex flex-col items-center mb-8 sm:mb-10 text-center animate-fade-in-down">
            <div class="mb-4 text-primary">
                {{-- Store/House Icon --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="w-12 h-12 sm:w-14 sm:h-14"
                >
                    <path d="M3 10.5 12 3l9 7.5V20a1.5 1.5 0 0 1-1.5 1.5H4.5A1.5 1.5 0 0 1 3 20V10.5Z" />
                    <path d="M9.5 21.5V13a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v8.5" />
                </svg>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                No tienes un negocio asociado.
            </h1>
        </div>

        {{-- Tarjeta de Registro de Negocio --}}
        <div class="w-full max-w-[460px] bg-surface rounded-[28px] sm:rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-black/60 border border-slate-800/30 animate-fade-in-scale">
            
            <h2 class="text-2xl sm:text-[28px] font-bold text-white text-center mb-8 tracking-tight">
                Registra tu Negocio
            </h2>

            <form method="POST" action="#" class="space-y-5">
                @csrf

                {{-- Campo: Tu Negocio --}}
                <div>
                    <label for="nombre" class="block text-white font-bold text-base mb-2">
                        Tu Negocio
                    </label>
                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre') }}"
                        placeholder="Nombre de tu negocio"
                        required
                        autofocus
                        class="w-full h-12 px-4 rounded-xl bg-[#566170] text-white placeholder-[#98a6b5] font-normal text-base outline-none focus:ring-2 focus:ring-primary focus:bg-[#5f6b7c] transition-all duration-200 @error('nombre') border-2 border-red-500 @enderror"
                    >
                    @error('nombre')
                        <p class="text-red-400 text-xs mt-1.5 px-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Dirección --}}
                <div>
                    <label for="direccion" class="block text-white font-bold text-base mb-2">
                        Dirección
                    </label>
                    <input
                        type="text"
                        name="direccion"
                        id="direccion"
                        value="{{ old('direccion') }}"
                        placeholder="Dirección"
                        class="w-full h-12 px-4 rounded-xl bg-[#566170] text-white placeholder-[#98a6b5] font-normal text-base outline-none focus:ring-2 focus:ring-primary focus:bg-[#5f6b7c] transition-all duration-200 @error('direccion') border-2 border-red-500 @enderror"
                    >
                    @error('direccion')
                        <p class="text-red-400 text-xs mt-1.5 px-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Teléfono --}}
                <div>
                    <label for="telefono" class="block text-white font-bold text-base mb-2">
                        Teléfono
                    </label>
                    <input
                        type="tel"
                        name="telefono"
                        id="telefono"
                        value="{{ old('telefono') }}"
                        placeholder="Teléfono"
                        class="w-full h-12 px-4 rounded-xl bg-[#566170] text-white placeholder-[#98a6b5] font-normal text-base outline-none focus:ring-2 focus:ring-primary focus:bg-[#5f6b7c] transition-all duration-200 @error('telefono') border-2 border-red-500 @enderror"
                    >
                    @error('telefono')
                        <p class="text-red-400 text-xs mt-1.5 px-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón de envío --}}
                <div class="pt-3">
                    <button
                        type="submit"
                        class="w-full h-12 rounded-xl bg-primary hover:bg-primary-hover active:scale-[0.99] text-white font-bold text-base sm:text-lg shadow-lg hover:shadow-cyan-500/20 transition-all duration-200 cursor-pointer flex items-center justify-center"
                    >
                        Registra Negocio
                    </button>
                </div>
            </form>

        </div>

    </main>
@endsection