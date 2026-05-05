<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <h2 class="text-center mb-4" style="font-family: 'Cinzel Decorative', serif; color: #d4af37; font-size: 1.3rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
            Junte-se à Guilda
        </h2>

        <div class="ornate-divider" style="margin: 0.8rem auto 1.5rem;"></div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="Nome do Guardião" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="Correio Arcano" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Palavra Secreta" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmar Palavra Secreta" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2" style="font-family: 'Cormorant Garamond', serif; color: #c9bcae; font-size: 0.95rem;">
                                {!! __('Aceito os :terms_of_service e a :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm rounded-md" style="color: #8b5a2b;">'.__('Termos do Reino').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm rounded-md" style="color: #8b5a2b;">'.__('Política do Salão').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm rounded-md" href="{{ route('login') }}" style="color: #8b5a2b; font-family: 'Cormorant Garamond', serif; font-size: 0.95rem;">
                    Já pertences à guilda?
                </a>

                <x-button class="ms-4">
                    Selar Inscrição
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
