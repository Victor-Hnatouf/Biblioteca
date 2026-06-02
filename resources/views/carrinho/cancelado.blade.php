<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            ❌ Pagamento Interrompido
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-8 border-2 border-red-500/40 relative">
                
                <div class="absolute top-4 right-4 text-5xl opacity-30 select-none">⚠️</div>

                <div class="text-center mb-8 border-b border-[#8b5a2b]/30 pb-6">
                    <span class="text-6xl block mb-2">⚠️</span>
                    <h2 class="text-3xl font-cinzel text-red-400 font-bold tracking-wide">Jornada Interrompida</h2>
                    <p class="text-sm italic text-base-content/70 mt-1">O selo de pagamento não pôde ser verificado ou foi cancelado.</p>
                </div>

                <div class="space-y-6">
                    <p class="text-base leading-relaxed text-center">
                        Nobre Guardião, detetámos que a transação de pagamento do Stripe não foi concluída. Não se preocupe! A sua encomenda foi guardada com o estado de **Pendente** e os seus livros continuam no carrinho de compras à sua espera.
                    </p>

                    <div class="bg-[#120e0a] border border-[#8b5a2b]/30 p-5 rounded-lg text-sm">
                        <h3 class="font-cinzel text-[#d4af37] text-lg mb-3 border-b border-[#8b5a2b]/10 pb-2">Detalhes da Encomenda Pendente</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="font-semibold text-base-content/60">Número:</span>
                                <span class="block font-bold text-[#e8dcca]">#{{ $encomenda->id }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-base-content/60">Estado atual:</span>
                                <span class="block mt-0.5"><span class="badge badge-warning">Pendente de Pagamento</span></span>
                            </div>
                            <div>
                                <span class="font-semibold text-base-content/60">Total em Aberto:</span>
                                <span class="block font-bold text-[#d4af37] text-lg">€{{ number_format($encomenda->total, 2, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-base-content/60">Morada registada:</span>
                                <span class="block italic text-[#e8dcca]">{{ $encomenda->morada }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-4">
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('carrinho') }}" class="btn btn-primary font-cinzel">🛒 Voltar ao Carrinho / Tentar Novamente</a>
                            <a href="{{ route('catalogo') }}" class="btn font-cinzel">📖 Voltar ao Catálogo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
