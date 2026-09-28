{{-- =========================================
    Modo de uso del modal:

========================================== --}}

@props([
    'tit',
    'des',
    'botcancel',
    'botconfirm',
    'nombre',
    'colorbotconfirm',
    'form'
])

<div
    x-show="open{{ $nombre ?? 'Confirm' }}Modal"
    x-data="{
        loader: false,

        confirmData(){
            this.loader = true;
            return document.getElementById('{{ $form ?? '' }}').submit();
        }
    }"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    @keydown.escape.window="open{{ $nombre ?? 'Confirm' }}Modal = false"
    @click.self="open{{ $nombre ?? 'Confirm' }}Modal = false"
>
    <div class="w-full max-w-md rounded-2xl bg-surface-hover p-8">

        <h3 class="text-lg font-bold text-text-primary">
            {{ $tit ?? '¿Estás seguro?' }}
        </h3>

        <p class="mt-2 text-sm text-text-secondary">
            {{ $des ?? '' }}
        </p>

        <div class="mt-6 flex flex-wrap gap-3">

            <button
                type="button"
                @click="open{{ $nombre ?? 'Confirm' }}Modal = false"
                class="h-11 rounded-xl bg-surface px-6 text-sm font-semibold text-text-primary transition-opacity hover:opacity-90 cursor-pointer"
            >
                {{ $botcancel ?? 'Cancelar' }}
            </button>

            <button
                type="button"
                @click="confirmData()"
                :class="loader ? 'bg-white/20 text-white/30 cursor-not-allowed' : '{{ $colorbotconfirm ?? 'bg-primary' }} text-white cursor-pointer'"
                class="h-11 rounded-xl px-6 text-sm font-semibold transition-opacity hover:opacity-90"
            >
                <span x-show="!loader">{{ $botconfirm ?? 'Ok' }}</span>
                <div x-show="loader" class="w-8 h-8 rounded-full border-4 border-gray-600 border-t-blue-600 animate-spin"></div>
            </button>
        </div>
    </div>
</div>