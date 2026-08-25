@props([
    'class'     => '',
    'maxHeight' => null,
    'axis'      => 'y',
    'size'      => 'sm',
    'variant'   => 'dark',
    'rounded'   => true,
    'hover'     => true,
])

@php
    $axisClasses = match($axis) {
        'x'    => 'overflow-x-auto overflow-y-hidden',
        'both' => 'overflow-auto',
        default  => 'overflow-y-auto overflow-x-hidden',
    };

    $sizeClass = match($size) {
        'xs'  => 'scrollbar-xs',
        'md'  => 'scrollbar-md',
        'lg'  => 'scrollbar-lg',
        default => 'scrollbar-sm',
    };

    $variantClass = match($variant) {
        'light'   => 'scrollbar-light',
        'primary' => 'scrollbar-primary',
        default     => 'scrollbar-dark',
    };

    $roundedClass = $rounded ? 'scrollbar-rounded' : '';
    $hoverClass   = $hover   ? 'scrollbar-hover'   : '';

    $inlineStyle = $maxHeight ? "max-height: {$maxHeight};" : '';
@endphp

<div
    {{ $attributes->merge([
        'class' => "custom-scrollbar {$axisClasses} {$sizeClass} {$variantClass} {$roundedClass} {$hoverClass} {$class}",
        'style'  => $inlineStyle,
    ]) }}
>
    {{ $slot }}
</div>
