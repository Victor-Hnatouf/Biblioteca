@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-md shadow-sm']) !!} style="background: rgba(18,14,10,0.8); border: 1px solid rgba(139,90,43,0.35); color: #e8dcca; font-family: 'Cormorant Garamond', serif; font-size: 1.05rem; transition: border-color 0.3s ease, box-shadow 0.3s ease;" onfocus="this.style.borderColor='#d4af37'; this.style.boxShadow='0 0 8px rgba(212,175,55,0.12)'" onblur="this.style.borderColor='rgba(139,90,43,0.35)'; this.style.boxShadow='none'">
