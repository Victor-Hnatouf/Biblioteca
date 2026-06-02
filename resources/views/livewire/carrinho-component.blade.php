<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-8 bg-base-200 p-4 rounded-lg shadow border border-[#8b5a2b]/30">
            <div class="flex items-center gap-2 {{ $step >= 1 ? 'text-[#d4af37]' : 'text-base-content/40' }} font-bold">
                <span class="w-8 h-8 rounded-full border border-current flex items-center justify-center font-cinzel">I</span>
                <span class="font-cinzel text-xs sm:text-sm uppercase tracking-wide">Carrinho</span>
            </div>
            <div class="flex-1 h-0.5 bg-base-300 mx-4 relative overflow-hidden">
                <div class="absolute left-0 top-0 h-full bg-[#d4af37] transition-all duration-300" style="width: {{ $step == 1 ? '0%' : ($step == 2 ? '50%' : '100%') }}"></div>
            </div>
            <div class="flex items-center gap-2 {{ $step >= 2 ? 'text-[#d4af37]' : 'text-base-content/40' }} font-bold">
                <span class="w-8 h-8 rounded-full border border-current flex items-center justify-center font-cinzel">II</span>
                <span class="font-cinzel text-xs sm:text-sm uppercase tracking-wide">Morada</span>
            </div>
            <div class="flex-1 h-0.5 bg-base-300 mx-4 relative overflow-hidden">
                <div class="absolute left-0 top-0 h-full bg-[#d4af37] transition-all duration-300" style="width: {{ $step <= 2 ? '0%' : '100%' }}"></div>
            </div>
            <div class="flex items-center gap-2 {{ $step >= 3 ? 'text-[#d4af37]' : 'text-base-content/40' }} font-bold">
                <span class="w-8 h-8 rounded-full border border-current flex items-center justify-center font-cinzel">III</span>
                <span class="font-cinzel text-xs sm:text-sm uppercase tracking-wide">Pagamento</span>
            </div>
        </div>

        
        @if(session()->has('message'))
            <div class="alert alert-success mb-6 shadow-lg">
                <div>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-error mb-6 shadow-lg">
                <div>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session()->has('stripe_error'))
            <div class="alert alert-warning mb-6 shadow-lg">
                <div>
                    <span>{{ session('stripe_error') }}</span>
                </div>
            </div>
        @endif

        
        @if($step == 1)
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-[#8b5a2b]/30">
                <h2 class="font-semibold text-2xl text-[#d4af37] font-cinzel mb-6 tracking-wide text-shadow">🛒 Carrinho de Compras</h2>

                @if(count($cartItems) === 0)
                    <div class="text-center py-12">
                        <span class="text-6xl block mb-4">🔮</span>
                        <p class="text-lg text-base-content/70 italic">O teu carrinho está vazio e silencioso. Explora o catálogo para adicionar novos volumes.</p>
                        <a href="{{ route('catalogo') }}" class="btn btn-primary mt-6">📖 Ir ao Catálogo</a>
                    </div>
                @else
                    <div class="overflow-x-auto mb-6">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Capa</th>
                                    <th>Volume</th>
                                    <th>Preço Unitário</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-right">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    @if($item->livro)
                                        <tr>
                                            <td>
                                                @if($item->livro->imagem_capa)
                                                    <div class="avatar">
                                                        <div class="w-12 h-16 rounded shadow border border-[#8b5a2b]/20">
                                                            <img src="{{ asset('storage/'.$item->livro->imagem_capa) }}" alt="Capa" />
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="w-12 h-16 bg-base-300 rounded border border-[#8b5a2b]/20 flex items-center justify-center">
                                                        <span class="text-xl">📖</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="font-bold text-[#e8dcca]">{{ $item->livro->nome }}</div>
                                                <div class="text-xs text-base-content/60">ISBN: {{ $item->livro->isbn }}</div>
                                            </td>
                                            <td class="text-[#d4af37] font-semibold">
                                                €{{ number_format((float)$item->livro->preco, 2, ',', '.') }}
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-center gap-2">
                                                    <button wire:click="diminuirQuantidade({{ $item->id }})" class="btn btn-xs btn-outline">-</button>
                                                    <span class="font-bold px-2">{{ $item->quantidade }}</span>
                                                    <button wire:click="aumentarQuantidade({{ $item->id }})" class="btn btn-xs btn-outline">+</button>
                                                </div>
                                            </td>
                                            <td class="text-right text-[#d4af37] font-bold">
                                                €{{ number_format((float)$item->livro->preco * $item->quantidade, 2, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <button wire:click="removerItem({{ $item->id }})" class="btn btn-xs btn-error btn-outline" title="Remover item">✕</button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mt-8 pt-6 border-t border-[#8b5a2b]/30">
                        <a href="{{ route('catalogo') }}" class="btn btn-ghost font-cinzel">📖 Continuar a Procurar</a>
                        <div class="flex flex-col items-end w-full md:w-auto">
                            <div class="flex items-center gap-6 mb-4">
                                <span class="font-cinzel text-lg text-base-content/70">Subtotal:</span>
                                <span class="text-2xl font-bold text-[#d4af37] font-cinzel">€{{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                            <button wire:click="avancarParaMorada" class="btn btn-primary w-full md:w-auto font-cinzel px-8">
                                Avançar para Morada 📜
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        
        @if($step == 2)
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-[#8b5a2b]/30 relative">
                
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#d4af37] to-transparent"></div>

                <h2 class="font-semibold text-2xl text-[#d4af37] font-cinzel mb-4 tracking-wide text-shadow">📜 Morada de Entrega</h2>
                <p class="text-sm italic text-base-content/70 mb-6">
                    Invoque as rotas de entrega espirituais fornecendo a morada completa onde os manuscritos físicos serão depositados em segurança.
                </p>

                <form wire:submit.prevent="submitMorada" class="space-y-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-[#d4af37] font-cinzel">Morada de Entrega</span>
                        </label>
                        <textarea wire:model="morada" rows="4" class="textarea textarea-bordered w-full p-4 bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]" placeholder="Ex: Rua de D. Afonso Henriques, Nº 12, 3º Esq, 1000-001 Lisboa"></textarea>
                        @error('morada') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-between items-center mt-8 pt-4 border-t border-[#8b5a2b]/30">
                        <button type="button" wire:click="retroceder(1)" class="btn btn-ghost font-cinzel">Retroceder</button>
                        <button type="submit" class="btn btn-primary font-cinzel px-8">Avançar para Pagamento 💳</button>
                    </div>
                </form>
            </div>
        @endif

        
        @if($step == 3)
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-[#8b5a2b]/30">
                <h2 class="font-semibold text-2xl text-[#d4af37] font-cinzel mb-6 tracking-wide text-shadow">🛡️ Selar Encomenda e Efetuar Pagamento</h2>

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <div class="md:col-span-2 space-y-4">
                        <h3 class="font-cinzel text-lg text-base-content/80 border-b border-[#8b5a2b]/20 pb-2">Manuscritos Escolhidos</h3>
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            @foreach($cartItems as $item)
                                @if($item->livro)
                                    <div class="flex justify-between items-center bg-[#120e0a] p-3 rounded-lg border border-[#8b5a2b]/10">
                                        <div>
                                            <div class="font-bold text-sm text-[#e8dcca]">{{ $item->livro->nome }}</div>
                                            <div class="text-xs text-base-content/50">Quantidade: {{ $item->quantidade }}</div>
                                        </div>
                                        <div class="text-[#d4af37] font-bold text-sm">
                                            €{{ number_format((float)$item->livro->preco * $item->quantidade, 2, ',', '.') }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    
                    <div class="bg-[#120e0a] border border-[#8b5a2b]/20 p-4 rounded-lg flex flex-col justify-between">
                        <div>
                            <h3 class="font-cinzel text-lg text-[#d4af37] border-b border-[#8b5a2b]/20 pb-2 mb-3 text-center">Resumo</h3>
                            
                            <div class="space-y-2 text-sm mb-4">
                                <div class="font-semibold text-[#8b5a2b]">Morada de Envio:</div>
                                <div class="text-base-content/80 whitespace-pre-wrap leading-relaxed">{{ $morada }}</div>
                            </div>
                        </div>

                        <div class="border-t border-[#8b5a2b]/30 pt-3 mt-3">
                            <div class="flex justify-between items-center mb-4">
                                <span class="font-cinzel font-bold text-base-content/70">Total Sagrado:</span>
                                <span class="text-xl font-bold text-[#d4af37] font-cinzel">€{{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="flex flex-col items-center gap-4 border-t border-[#8b5a2b]/30 pt-6">
                    <button wire:click="pagarEncomenda" class="btn btn-primary w-full max-w-md font-cinzel text-base py-3">
                        💳 Ir para Pagamento Stripe Sandbox
                    </button>
                    <button wire:click="retroceder(2)" class="btn btn-ghost font-cinzel text-sm">Alterar Morada</button>
                </div>

                
                <div x-data="{ openSim: false }" 
                     x-on:abrir-simulacao-local.window="openSim = true; $wire.set('step', 3)"
                     x-show="openSim"
                     x-cloak
                     class="mt-10 p-6 bg-[#241f1c] border-2 border-dashed border-[#d4af37] rounded-xl shadow-inner relative">
                    
                    <div class="absolute top-[-12px] left-6 px-3 bg-[#241f1c] text-[#d4af37] font-cinzel font-bold text-xs uppercase tracking-widest border border-[#d4af37]/50 rounded">
                        Simulador Sandbox Local
                    </div>

                    <p class="text-sm text-base-content/80 leading-relaxed mb-4">
                        ⚠️ **Nota do Escriba-Mor:** Detetámos que a aplicação está sem chaves Stripe configuradas no `.env` ou a ligação falhou. 
                        Para garantir que consegue validar e classificar o fluxo da encomenda na sandbox local, criámos este painel de simulação.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center mt-6">
                        <button wire:click="simularPagamentoSucesso({{ session('last_encomenda_id') ?? 1 }})" 
                                class="btn btn-success font-cinzel text-xs py-2 w-full sm:w-auto"
                                @click="let el = document.querySelector('[wire\\:click=\'pagarEncomenda\']');">
                            ✅ Simular Sucesso
                        </button>
                        <button wire:click="simularPagamentoCancelado({{ session('last_encomenda_id') ?? 1 }})" 
                                class="btn btn-error font-cinzel text-xs py-2 w-full sm:w-auto">
                            ❌ Simular Cancelamento
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('abrir-simulacao-local', (event) => {
            // Guardar id da encomenda no session storage do browser para garantir resiliência
            sessionStorage.setItem('last_encomenda_id', event.encomendaId);
        });
    });
</script>
