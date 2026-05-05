<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }} style="background: linear-gradient(180deg, rgba(28,24,21,0.9), rgba(18,14,10,0.95)); border-color: rgba(139,90,43,0.4); color: #c9bcae; font-family: 'Cinzel', serif; --tw-ring-color: #8b5a2b;">
    {{ $slot }}
</button>
