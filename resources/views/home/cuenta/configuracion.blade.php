@extends('layout')

@section('main')
    <main
        class="lg:ml-56 pt-14 lg:pt-0 min-h-screen pb-16"
        x-data="{
            showCurrentPassword: false,
            showNewPassword: false,
            showConfirmPassword: false,
            openCancelModal: false,
            openConfirmModal: false,
            tab: 'cuenta',
        }"
    >
        @include('components.sidebar')
        {{-- =========================================
            HEADER
        ========================================== --}}
        <div class="flex sm:justify-between sm:flex-row flex-col sm:items-center px-12 pt-10 pb-2">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-text-primary">Mi cuenta</h1>
                <p class="mt-1 text-sm text-text-secondary">Gestioná tu información personal y la de tu negocio.</p>
            </div>

            {{-- =========================================
                BOTONES SELECTOR
            ========================================== --}}   
            <nav class="flex gap-2">
                <button
                @click="tab = 'cuenta'"
                :class="tab === 'cuenta'
                    ? 'bg-[#0081CC] text-white shadow-sm'
                    : 'text-[#AAB6C4] hover:bg-[#273240] hover:text-white'"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 cursor-pointer"
                >
                    Cuenta
                </button>

                <button
                @click="tab = 'negocio'"
                :class="tab === 'negocio'
                    ? 'bg-[#0081CC] text-white shadow-sm'
                    : 'text-[#AAB6C4] hover:bg-[#273240] hover:text-white'"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 cursor-pointer"
                >
                    Negocio
                </button>
            </nav>
        </div>

        {{-- =========================================
            CONTENIDO PRINCIPAL SELECCIONADO
        ========================================== --}}  
        
        <section x-show="tab === 'cuenta'">
            @include('home.cuenta.cuenta')
        </section>

        <section x-show="tab === 'negocio'">
            @include('home.cuenta.negocio')
        </section>
    </main>
@endsection
