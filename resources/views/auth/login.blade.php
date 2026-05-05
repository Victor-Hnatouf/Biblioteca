<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm" style="color: #86efac;">
                {{ $value }}
            </div>
        @endsession

        <h2 class="text-center mb-4" style="font-family: 'Cinzel Decorative', serif; color: #d4af37; font-size: 1.3rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
            Identificação do Guardião
        </h2>

        <div class="ornate-divider" style="margin: 0.8rem auto 1.5rem;"></div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="Correio Arcano" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Palavra Secreta" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm" style="color: #c9bcae; font-family: 'Cormorant Garamond', serif; font-size: 1rem;">Manter-me reconhecido</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm rounded-md" href="{{ route('password.request') }}" style="color: #8b5a2b; font-family: 'Cormorant Garamond', serif; font-size: 0.95rem;">
                        Esqueceste a tua palavra secreta?
                    </a>
                @endif

                <x-button class="ms-4">
                    Entrar no Salão
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
