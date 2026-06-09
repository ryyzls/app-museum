
@props([

    'type' => 'default'

])

@php

    $styles = match($type) {

        'success' =>
            'bg-green-100 text-green-600',

        'danger' =>
            'bg-red-100 text-red-500',

        'warning' =>
            'bg-yellow-100 text-yellow-600',

        'info' =>
            'bg-blue-100 text-blue-600',

        default =>
            'bg-gray-100 text-gray-600'

    };

@endphp

<span
    {{ $attributes->merge([

        'class' =>
            "inline-flex items-center px-4 py-2
            rounded-full text-sm whitespace-nowrap {$styles}"

    ]) }}
>

    {{ $slot }}

</span>

