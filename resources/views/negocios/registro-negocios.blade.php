@extends('layout')

@section('main')
    <main
        class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6 bg-background"
        x-data="{
            nombre: '',
            direccion: '',
            telefono: '',
            errores: {},
            cargando: false,

            async validar() {
                this.errores = {};
                if (!this.nombre.trim()) {
                    this.errores.nombre = 'El nombre es obligatorio.';
                    return;
                }

                this.cargando = true;

                try {
                    const res = await fetch('{{ route('negocios.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            nombre: this.nombre,
                            direccion: this.direccion,
                            telefono: this.telefono,
                        }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                this.errores[key] = data.errors[key][0];
                            });
                        } else {
                            this.errores.general = data.error || 'Ocurrió un error al registrar el negocio.';
                        }
                        return;
                    }

                    window.location.href = '{{ route('dashboard.index') }}';
                } catch (e) {
                    this.errores.general = 'Error de conexión. Intenta nuevamente.';
                } finally {
                    this.cargando = false;
                }
            }
        }"
    >

        {{-- Encabezado --}}
        <div class="flex flex-col items-center mb-8 sm:mb-10 text-center animate-fade-in-down">
            <div class="mb-4 text-primary">
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


        {{-- Formulario de Negocio --}}
        <div
            class="w-full max-w-[460px] bg-surface rounded-[28px] sm:rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-black/60 border border-slate-800/30"
        >

            <h2 class="text-2xl sm:text-[28px] font-bold text-white text-center mb-8 tracking-tight">
                Registra tu Negocio
            </h2>

            <div class="mb-6 p-3 rounded-xl bg-primary/10 border border-primary/30 text-center">
                <p class="text-text-secondary text-sm">
                    Empezás con <span class="text-white font-bold">{{ config('mercadopago.trial_days', 30) }} días de prueba gratis</span>.
                </p>
            </div>

            <div x-show="errores.general" class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-center">
                <p class="text-red-400 text-sm font-medium" x-text="errores.general"></p>
            </div>

            <div class="space-y-5">

                {{-- Campo: Tu Negocio --}}
                <div>
                    <label for="nombre" class="block text-white font-bold text-base mb-2">
                        Tu Negocio
                    </label>
                    <input
                        type="text"
                        x-model="nombre"
                        id="nombre"
                        placeholder="Nombre de tu negocio"
                        required
                        autofocus
                        class="w-full h-12 px-4 rounded-xl bg-[#566170] text-white placeholder-[#98a6b5] font-normal text-base outline-none focus:ring-2 focus:ring-primary focus:bg-[#5f6b7c] transition-all duration-200"
                    >
                    <p x-show="errores.nombre" x-text="errores.nombre" class="text-red-400 text-xs mt-1.5 px-1 font-medium"></p>
                </div>

                {{-- Campo: Dirección --}}
                <div>
                    <label for="direccion" class="block text-white font-bold text-base mb-2">
                        Dirección
                    </label>
                    <input
                        type="text"
                        x-model="direccion"
                        id="direccion"
                        placeholder="Dirección"
                        class="w-full h-12 px-4 rounded-xl bg-[#566170] text-white placeholder-[#98a6b5] font-normal text-base outline-none focus:ring-2 focus:ring-primary focus:bg-[#5f6b7c] transition-all duration-200"
                    >
                </div>

                {{-- Campo: Teléfono --}}
                <div>
                    <label for="telefono" class="block text-white font-bold text-base mb-2">
                        Teléfono
                    </label>
                    <input
                        type="tel"
                        x-model="telefono"
                        id="telefono"
                        placeholder="Teléfono"
                        class="w-full h-12 px-4 rounded-xl bg-[#566170] text-white placeholder-[#98a6b5] font-normal text-base outline-none focus:ring-2 focus:ring-primary focus:bg-[#5f6b7c] transition-all duration-200"
                    >
                </div>

                {{-- Botón de envío --}}
                <div class="pt-3">
                    <button
                        @click="validar()"
                        :disabled="cargando"
                        class="w-full h-12 rounded-xl bg-primary hover:bg-primary-hover active:scale-[0.99] text-white font-bold text-base sm:text-lg shadow-lg hover:shadow-cyan-500/20 transition-all duration-200 cursor-pointer flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!cargando">Continuar</span>
                        <span x-show="cargando" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Registrando...
                        </span>
                    </button>
                </div>
            </div>

        </div>


        </main>
@endsection
