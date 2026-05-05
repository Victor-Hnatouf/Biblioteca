<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }} style="background: linear-gradient(180deg, #8b5a2b, #4a0e0e); border-color: rgba(212,175,55,0.5); color: #e8dcca; font-family: 'Cinzel', serif; box-shadow: 0 4px 12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05); --tw-ring-color: #8b5a2b;">
    {{ $slot }}
</button>
