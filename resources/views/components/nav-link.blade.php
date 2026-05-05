@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';

$activeStyle = ($active ?? false)
            ? 'border-color: #d4af37; color: #d4af37; font-family: "Cinzel", serif; text-shadow: 0 0 8px rgba(212,175,55,0.15); letter-spacing: 0.04em;'
            : 'color: #c9bcae; font-family: "Cinzel", serif; letter-spacing: 0.04em;';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="{{ $activeStyle }}" onmouseover="this.style.color='#d4af37'; this.style.textShadow='0 0 8px rgba(212,175,55,0.15)'; this.style.borderColor='rgba(212,175,55,0.5)';" onmouseout="@if(!($active ?? false))this.style.color='#c9bcae'; this.style.textShadow='none'; this.style.borderColor='transparent';@endif">
    {{ $slot }}
</a>
