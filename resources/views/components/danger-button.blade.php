<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150']) }} style="background: linear-gradient(180deg, #8b2020, #6b1010); color: #fca5a5; border: 1px solid rgba(220,38,38,0.4); font-family: 'Cinzel', serif; --tw-ring-color: #991b1b;">
    {{ $slot }}
</button>
