@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out';

$activeStyle = ($active ?? false)
            ? 'border-color: #d4af37; color: #d4af37; background: rgba(139,90,43,0.1); font-family: "Cinzel", serif;'
            : 'color: #c9bcae; font-family: "Cinzel", serif;';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="{{ $activeStyle }}" onmouseover="this.style.color='#d4af37'; this.style.background='rgba(139,90,43,0.08)'; this.style.borderColor='rgba(212,175,55,0.4)';" onmouseout="@if(!($active ?? false))this.style.color='#c9bcae'; this.style.background='transparent'; this.style.borderColor='transparent';@endif">
    {{ $slot }}
</a>
