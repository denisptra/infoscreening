@props([
    'variant' => 'white',
    'type' => 'full',
    'class' => 'h-7 sm:h-8 w-auto'
])

@php
    if ($type === 'emblem') {
        $src = $variant === 'purple' ? asset('images/emblem-purple.png') : asset('images/emblem-white.png');
    } else {
        $src = $variant === 'purple' ? asset('images/logo-purple.png') : asset('images/logo-white.png');
    }
@endphp

<img
    src="{{ $src }}"
    alt="Infoscreening"
    {{ $attributes->merge(['class' => $class . ' object-contain select-none transition-all duration-200']) }}
    loading="eager"
>
