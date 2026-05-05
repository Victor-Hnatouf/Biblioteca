<x-action-section>
    <x-slot name="title">
        {{ __('Autenticação de Dois Fatores') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Adiciona segurança extra à tua conta usando autenticação de dois fatores.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-base-content">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Conclui a ativação da autenticação de dois fatores.') }}
                @else
                    {{ __('A autenticação de dois fatores está ativada.') }}
                @endif
            @else
                {{ __('A autenticação de dois fatores não está ativada.') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-base-content/80">
            <p>
                {{ __('Quando a autenticação de dois fatores estiver ativa, será pedido um código seguro durante o login. Podes obter esse código na app Google Authenticator do teu telemóvel.') }}
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-base-content/80">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            {{ __('Para concluir, lê o QR code abaixo com a app autenticadora (ou introduz a chave de configuração) e coloca o código OTP gerado.') }}
                        @else
                            {{ __('A autenticação de dois fatores está ativa. Lê o QR code abaixo com a app autenticadora ou introduz a chave de configuração.') }}
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block bg-base-200">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-base-content/80">
                    <p class="font-semibold">
                        {{ __('Chave de configuração') }}: {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <x-label for="code" value="{{ __('Código') }}" />

                        <x-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />

                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-base-content/80">
                    <p class="font-semibold">
                        {{ __('Guarda estes códigos de recuperação num gestor de palavras-passe seguro. Podes usá-los para recuperar o acesso à conta caso percas o dispositivo de autenticação.') }}
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-base-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled">
                        {{ __('Ativar') }}
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button class="me-3">
                            {{ __('Regenerar códigos de recuperação') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" class="me-3" wire:loading.attr="disabled">
                            {{ __('Confirmar') }}
                        </x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button class="me-3">
                            {{ __('Mostrar códigos de recuperação') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled">
                            {{ __('Cancelar') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled">
                            {{ __('Desativar') }}
                        </x-danger-button>
                    </x-confirms-password>
                @endif

            @endif
        </div>
    </x-slot>
</x-action-section>
