@extends('layout')

@section('main')
    <main
        class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6 bg-background"
        x-data="{
            step: 1,
            nombre: '',
            direccion: '',
            telefono: '',
            errores: {},
            negocioId: null,
            cargando: false,
            planSeleccionado: null,

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

                    this.negocioId = data.negocio_id;
                    this.step = 2;
                } catch (e) {
                    this.errores.general = 'Error de conexión. Intenta nuevamente.';
                } finally {
                    this.cargando = false;
                }
            },

            confirmarPlan(plan) {
                this.planSeleccionado = plan;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('negocios.suscribir') }}';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                const negocio = document.createElement('input');
                negocio.type = 'hidden';
                negocio.name = 'negocios_id';
                negocio.value = this.negocioId;
                form.appendChild(negocio);

                const planId = document.createElement('input');
                planId.type = 'hidden';
                planId.name = 'plan_id';
                planId.value = plan;
                form.appendChild(planId);

                document.body.appendChild(form);
                form.submit();
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


        {{-- ======================== PASO 1: Formulario de Negocio ======================== --}}
        <div
            x-show="step === 1"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-[460px] bg-surface rounded-[28px] sm:rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-black/60 border border-slate-800/30"
        >

            <h2 class="text-2xl sm:text-[28px] font-bold text-white text-center mb-8 tracking-tight">
                Registra tu Negocio
            </h2>

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


        {{-- ======================== PASO 2: Selección de Plan ======================== --}}
        <div
            x-show="step === 2"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-[460px]"
        >

            {{-- Botón volver --}}
            <button
                @click="step = 1"
                class="flex items-center gap-2 text-text-secondary hover:text-white text-sm font-medium mb-6 transition-colors cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
                Volver
            </button>

            {{-- Resumen del negocio --}}
            <div class="mb-6 text-center">
                <p class="text-text-secondary text-sm">Negocio registrado:</p>
                <p class="text-white font-bold text-lg" x-text="nombre"></p>
            </div>

            {{-- Error general --}}
            <div x-show="errores.general" class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-center">
                <p class="text-red-400 text-sm font-medium" x-text="errores.general"></p>
            </div>

            {{-- Card de Plan --}}
            <div
                class="w-full rounded-[32px] border-2 border-border bg-surface px-10 py-5 text-text-primary shadow-lg"
            >

                {{-- Header --}}
                <div class="text-center">
                    <h2 class="text-5xl font-bold tracking-tight">
                        Plan Inicial
                    </h2>
                    <div class="mt-2 h-px w-full bg-border"></div>
                </div>

                {{-- Precio --}}
                <div class="mt-8 flex items-center justify-center gap-7">
                    <div class="flex items-start">
                        <span class="mt-1 text-5xl font-medium leading-none">
                            $
                        </span>
                        <span class="text-8xl font-medium leading-[0.8] tracking-tight">
                            0
                        </span>
                    </div>

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
                    <span class="text-base font-medium text-text-secondary line-through decoration-danger">
                        $22.999/ARS
                    </span>
                </div>

                {{-- Descripción --}}
                <p class="mt-3 text-[17px] leading-[1.45] text-text-secondary">
                    Luego del primer mes, la suscripción
                    pasa a costar $22.999 por mes
                </p>

                {{-- CTA --}}
                <button
                    @click="confirmarPlan(1)"
                    class="mt-6 w-full rounded-2xl bg-primary px-6 py-4 text-2xl font-bold text-white transition-all duration-200 hover:bg-primary-hover hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
                >
                    Suscribirse
                </button>

                {{-- Beneficios --}}
                <ul class="mt-8 space-y-4">

                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">
                            Gestionar tus reparaciones
                        </span>
                    </li>

                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">
                            Notificar a tus clientes por Email
                        </span>
                    </li>

                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">
                            Soporte Técnico
                        </span>
                    </li>

                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L20 7"/>
                        </svg>
                        <span class="text-lg leading-tight">
                            Comprobantes de tus reparaciones
                        </span>
                    </li>

                </ul>

            </div>

        </div>

    </main>
@endsection
