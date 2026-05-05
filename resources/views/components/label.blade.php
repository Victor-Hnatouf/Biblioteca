@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm']) }} style="color: #c9bcae; font-family: 'Cinzel', serif; letter-spacing: 0.03em; font-size: 0.85rem;">
    {{ $value ?? $slot }}
</label>
