@props([
    'action' => 'abrirModalNuevaReparacion()',
    'class'  => '',
])

<button
    type="button"
    @click="{{ $action }}"
    {{ $attributes->merge([
        'class' => "h-16 rounded-2xl bg-primary px-8 text-lg font-bold text-white transition-all hover:bg-primary-hover active:scale-[0.98] shadow-md flex items-center gap-2.5 cursor-pointer shrink-0 {$class}"
    ]) }}
>
    <span>+ Nueva Reparación</span>
</button>
