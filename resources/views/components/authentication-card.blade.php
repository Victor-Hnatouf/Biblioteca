<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: radial-gradient(ellipse at 30% 10%, rgba(107,16,16,0.08), transparent 50%), radial-gradient(ellipse at 70% 90%, rgba(212,175,55,0.05), transparent 50%), #120e0a;">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-8 overflow-hidden sm:rounded-lg relative" style="background: linear-gradient(135deg, rgba(28,24,21,0.97), rgba(20,17,15,0.99)); border: 2px solid rgba(139,90,43,0.3); box-shadow: inset 0 0 30px rgba(0,0,0,0.5), 0 8px 32px rgba(0,0,0,0.6), 0 0 1px rgba(212,175,55,0.3);">
        {{-- Corner ornaments --}}
        <div class="gothic-corner gothic-corner-tl"></div>
        <div class="gothic-corner gothic-corner-tr"></div>
        <div class="gothic-corner gothic-corner-bl"></div>
        <div class="gothic-corner gothic-corner-br"></div>

        {{ $slot }}
    </div>
</div>
