<div class="px-12">

    {{-- =========================================
        GRID SUPERIOR: Perfil + Negocio
    ========================================== --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mb-6">

        {{-- ─── CARD: Datos personales ─── --}}
        <div class="rounded-2xl bg-surface-hover p-8">

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-text-primary">Datos personales</h2>
                    <p class="text-xs text-text-secondary">Tu nombre e información de contacto</p>
                </div>
            </div>

            <form x-data="{
                    original: {
                        nombre: '{{ $user->name }}',
                        telefono: '{{ $user->telefono }}'
                    },
                    form: {
                        nombre: '{{ $user->name }}',
                        telefono: '{{ $user->telefono }}'
                    },
                    get hasChanges() {
                        return this.form.nombre !== this.original.nombre ||
                            this.form.telefono !== this.original.telefono
                    }
                }"
                id="form-perfil" method="POST" action="{{ route('cuenta.update-perfil') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Nombre --}}
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Nombre completo
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        x-model="form.nombre"
                        placeholder="Tu nombre completo"
                        autocomplete="name"
                        class="h-12 w-full rounded-xl border-0 bg-surface px-4 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('name') ring-2 ring-danger @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label for="telefono" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Teléfono personal
                        <span class="ml-1 font-normal text-text-secondary">(opcional)</span>
                    </label>
                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        value="{{ old('telefono', $user->telefono) }}"
                        x-model="form.telefono"
                        placeholder="Ej: +54 9 11 1234-5678"
                        autocomplete="tel"
                        class="h-12 w-full rounded-xl border-0 bg-surface px-4 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('telefono') ring-2 ring-danger @enderror"
                    >
                    @error('telefono')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (solo lectura) --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            readonly
                            class="h-12 w-full rounded-xl border-0 bg-surface/50 px-4 pr-28 text-sm font-medium text-text-secondary outline-none cursor-not-allowed"
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-success/15 px-3 py-1 text-xs font-semibold text-success">
                            Verificado
                        </span>
                    </div>
                    <p class="mt-1.5 text-xs text-text-secondary">Para cambiar tu correo, contactá al soporte.</p>
                </div>

                <div class="pt-1">
                    <button
                        type="button"
                        @click="if (hasChanges) openConfirmModal = true"
                        :disabled="form.nombre === original.nombre && form.telefono === original.telefono"
                        :class="(form.nombre !== original.nombre || form.telefono !== original.telefono) ? 'bg-primary transition-opacity hover:opacity-90 text-white cursor-pointer' : 'bg-white/20 text-white/30 cursor-not-allowed'"
                        class="h-11 rounded-xl px-6 text-sm font-semibold"
                    >
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
        {{-- =========================================
            CARD: Seguridad — Cambiar contraseña
        ========================================== --}}
        <div class="rounded-2xl bg-surface-hover p-8">

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-text-primary">Seguridad</h2>
                    <p class="text-xs text-text-secondary">Cambiá tu contraseña de acceso</p>
                </div>
            </div>

            <form method="POST" action="{{ route('cuenta.update-password') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Contraseña actual --}}
                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Contraseña actual
                    </label>
                    <div class="relative">
                        <input
                            :type="showCurrentPassword ? 'text' : 'password'"
                            id="current_password"
                            name="current_password"
                            placeholder="Tu contraseña actual"
                            autocomplete="current-password"
                            class="h-12 w-full rounded-xl border-0 bg-surface px-4 pr-12 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('current_password') ring-2 ring-danger @enderror"
                        >
                        <button
                            type="button"
                            @click="showCurrentPassword = !showCurrentPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-primary transition-colors cursor-pointer"
                            tabindex="-1"
                        >
                            <svg x-show="!showCurrentPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showCurrentPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nueva contraseña --}}
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Nueva contraseña
                    </label>
                    <div class="relative">
                        <input
                            :type="showNewPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            placeholder="Mínimo 6 caracteres"
                            autocomplete="new-password"
                            class="h-12 w-full rounded-xl border-0 bg-surface px-4 pr-12 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all @error('password') ring-2 ring-danger @enderror"
                        >
                        <button
                            type="button"
                            @click="showNewPassword = !showNewPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-primary transition-colors cursor-pointer"
                            tabindex="-1"
                        >
                            <svg x-show="!showNewPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showNewPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar nueva contraseña --}}
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-text-primary">
                        Confirmar nueva contraseña
                    </label>
                    <div class="relative">
                        <input
                            :type="showConfirmPassword ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repetí la nueva contraseña"
                            autocomplete="new-password"
                            class="h-12 w-full rounded-xl border-0 bg-surface px-4 pr-12 text-sm font-medium text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary transition-all"
                        >
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-text-disabled hover:text-text-primary transition-colors cursor-pointer"
                            tabindex="-1"
                        >
                            <svg x-show="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showConfirmPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Botón --}}
                <div class="md:col-span-3 pt-1">
                    <button
                        type="submit"
                        class="h-11 rounded-xl bg-primary px-6 text-sm font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                    >
                        Cambiar contraseña
                    </button>
                </div>

            </form>
        </div>
    </div>{{-- /grid --}}
    {{-- /card seguridad --}}
    <x-modal-confirmacion
        form="form-perfil"
        tit="¿Guardar cambios?"
        des="Se actualizarán tus datos personales."
        botcancel="Cancelar"
        botconfirm="Guardar cambios"
    />
</div>