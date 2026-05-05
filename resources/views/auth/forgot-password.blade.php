<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-center mb-4" style="font-family: 'Cinzel Decorative', serif; color: #d4af37; font-size: 1.2rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
            Palavra Secreta Esquecida
        </h2>

        <div class="ornate-divider" style="margin: 0.8rem auto 1.5rem;"></div>

        <div class="mb-4 text-sm" style="color: #c9bcae; font-family: 'Cormorant Garamond', serif; font-size: 1.05rem; line-height: 1.7;">
            Perdeste a tua palavra secreta, viajante? Não te preocupes — indica o teu correio arcano e enviar-te-emos um pergaminho encantado para redefinires as tuas credenciais.
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm" style="color: #86efac;">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="Correio Arcano" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    Enviar Pergaminho
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
