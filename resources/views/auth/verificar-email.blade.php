@extends('layout')

@section('main')
    <main class="min-h-screen w-full flex flex-col items-center justify-center p-4 sm:p-6 bg-background">
        
        {{-- Encabezado: Icono de correo y título --}}
        <div class="flex flex-col items-center mb-8 sm:mb-10 text-center animate-fade-in-down">
            <div class="mb-4 text-primary flex justify-center">
                {{-- Mail / Envelope Icon --}}
                <svg
                    class="w-12 h-12 sm:w-14 sm:h-14"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3.75 3.75 0 0 1-3.644 0L1.5 8.67Z" />
                    <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                    <circle cx="19.5" cy="4.5" r="2.5" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                Verifica tu Email
            </h1>
        </div>

        {{-- Tarjeta de Verificación --}}
        <div class="w-full max-w-[460px] bg-surface rounded-[28px] sm:rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-black/60 border border-slate-800/30 animate-fade-in-scale">
            
            <h2 class="text-2xl sm:text-[28px] font-bold text-white text-center mb-2 tracking-tight">
                Te enviamos un codigo
            </h2>

            <p class="text-text-secondary text-center text-sm leading-relaxed mb-8 max-w-[320px] mx-auto">
                Vas a recibir un correo a tu email con el codigo de verificacion para ingresarlo aqui
            </p>

            <form
                method="POST"
                action="{{ Route::has('verificar-email.store') ? route('verificar-email.store') : (Route::has('verificar-email') ? route('verificar-email') : url('/verificar-email')) }}"
                x-data="{
                    digits: ['', '', '', '', ''],
                    get codeString() {
                        return this.digits.join('');
                    },
                    handleInput(index, event) {
                        const val = event.target.value.replace(/[^0-9a-zA-Z]/g, '');
                        if (val.length > 0) {
                            this.digits[index] = val.slice(-1);
                            if (index < 4) {
                                this.$refs['digit' + (index + 1)].focus();
                                this.$refs['digit' + (index + 1)].select();
                            }
                        } else {
                            this.digits[index] = '';
                        }
                    },
                    handleKeydown(index, event) {
                        if (event.key === 'Backspace') {
                            if (!this.digits[index] && index > 0) {
                                this.$refs['digit' + (index - 1)].focus();
                            } else {
                                this.digits[index] = '';
                            }
                        } else if (event.key === 'ArrowLeft' && index > 0) {
                            event.preventDefault();
                            this.$refs['digit' + (index - 1)].focus();
                        } else if (event.key === 'ArrowRight' && index < 4) {
                            event.preventDefault();
                            this.$refs['digit' + (index + 1)].focus();
                        }
                    },
                    handlePaste(event) {
                        event.preventDefault();
                        const text = (event.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9a-zA-Z]/g, '');
                        if (!text) return;
                        const chars = text.slice(0, 5).split('');
                        chars.forEach((c, i) => {
                            if (i < 5) this.digits[i] = c;
                        });
                        const focusIdx = Math.min(chars.length, 4);
                        this.$nextTick(() => {
                            this.$refs['digit' + focusIdx].focus();
                        });
                    }
                }"
                class="space-y-6"
            >
                @csrf

                {{-- Campo oculto para enviar el código completo concatenado --}}
                <input type="hidden" name="code" :value="codeString">

                {{-- Contenedor de los 5 casilleros de código --}}
                <div class="flex items-center justify-center gap-2.5 sm:gap-3" @paste="handlePaste($event)">
                    @for ($i = 0; $i < 5; $i++)
                        <input
                            type="text"
                            name="digits[]"
                            x-ref="digit{{ $i }}"
                            x-model="digits[{{ $i }}]"
                            @input="handleInput({{ $i }}, $event)"
                            @keydown="handleKeydown({{ $i }}, $event)"
                            @focus="$event.target.select()"
                            maxlength="1"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            class="w-12 h-14 sm:w-14 sm:h-14 text-center text-xl sm:text-2xl font-bold text-white rounded-xl sm:rounded-2xl bg-[#566170] focus:bg-[#5f6b7c] focus:ring-2 focus:ring-primary outline-none transition-all duration-200 uppercase selection:bg-transparent shadow-inner"
                            {{ $i === 0 ? 'autofocus' : '' }}
                            required
                        >
                    @endfor
                </div>

                {{-- Manejo de errores de validación --}}
                @error('code')
                    <p class="text-red-400 text-xs text-center font-medium -mt-2">{{ $message }}</p>
                @enderror
                @error('digits')
                    <p class="text-red-400 text-xs text-center font-medium -mt-2">{{ $message }}</p>
                @enderror

                {{-- Botón de envío --}}
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full h-12 sm:h-14 rounded-xl sm:rounded-2xl bg-primary hover:bg-primary-hover active:scale-[0.99] text-white font-bold text-base sm:text-lg shadow-lg hover:shadow-cyan-500/20 transition-all duration-200 cursor-pointer flex items-center justify-center"
                    >
                        Registra Negocio
                    </button>
                </div>
            </form>

        </div>

    </main>
@endsection