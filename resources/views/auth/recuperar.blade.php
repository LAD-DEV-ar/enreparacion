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
                Cambia tu Contraseña
            </h1>
        </div>

        {{-- Tarjeta de Verificación --}}
        <div class="w-full max-w-[460px] bg-surface rounded-[28px] sm:rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-black/60 border border-slate-800/30 animate-fade-in-scale">
            
            <h2 class="text-2xl sm:text-[28px] font-bold text-white text-center mb-2 tracking-tight">
                Ingresa tu nueva contraseña
            </h2>

            <form
                action="{{ route('password.store') }}"
                method="POST"
                x-data="{
                    password: '',
                    password2: '',
                    showPassword: false,
                    showPassword2: false,
                    get passwordsMatch() {
                        return this.password.length > 0 && this.password === this.password2;
                    },
                    get showMismatch() {
                        return this.password2.length > 0 && this.password !== this.password2;
                    }
                }"
                @submit="if (!passwordsMatch) $event.preventDefault()"
            >
                @csrf

                {{-- Campos requeridos por Password::reset() --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-2">
                    <label for="password" class="text-lg text-text-primary font-semibold">Contraseña</label>

                    <div class="relative">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            x-model="password"
                            class="w-full h-14 px-6 pr-14 rounded-2xl bg-[#e6f7ff] text-[#1e293b] placeholder-[#64748b] font-medium text-base md:text-lg outline-none focus:ring-2 focus:ring-[#0081cc] focus:bg-white transition-all duration-200 @error('password') border-2 border-red-500 @enderror"
                            required
                            placeholder="Tu Contraseña"
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[#64748b] hover:text-[#1e293b] transition-colors cursor-pointer"
                            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                        >
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-1 px-3 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="password_confirmation" class="text-lg text-text-primary font-semibold">Repetir Contraseña</label>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            :type="showPassword2 ? 'text' : 'password'"
                            name="password_confirmation"
                            x-model="password2"
                            class="w-full h-14 px-6 pr-14 rounded-2xl bg-[#e6f7ff] text-[#1e293b] placeholder-[#64748b] font-medium text-base md:text-lg outline-none focus:ring-2 focus:bg-white transition-all duration-200 @error('password2') border-2 border-red-500 @enderror"
                            :class="showMismatch ? 'border-2 border-red-500 focus:ring-2 focus:ring-red-400' : (passwordsMatch ? 'border-2 border-green-500 focus:ring-2 focus:ring-green-400' : 'focus:ring-2 focus:ring-[#0081cc]')"
                            required
                            placeholder="Repite tu contraseña"
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showPassword2 = !showPassword2"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[#64748b] hover:text-[#1e293b] transition-colors cursor-pointer"
                            :aria-label="showPassword2 ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                        >
                            <svg x-show="!showPassword2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showPassword2" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <p x-show="showMismatch" x-cloak class="text-red-400 text-xs mt-1 px-3 font-medium">
                        Las contraseñas no coinciden.
                    </p>
                    <p x-show="passwordsMatch" x-cloak class="text-green-400 text-xs mt-1 px-3 font-medium">
                        Las contraseñas coinciden.
                    </p>
                    @error('password_confirmation')
                        <p class="text-red-400 text-xs mt-1 px-3 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="!passwordsMatch"
                        class="w-full h-14 rounded-2xl bg-[#0081cc] hover:bg-[#33b4ff] active:scale-[0.99] text-white font-bold text-lg shadow-lg hover:shadow-cyan-500/20 transition-all duration-200 cursor-pointer flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#0081cc]"
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