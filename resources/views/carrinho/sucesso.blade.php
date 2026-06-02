<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            📜 Compra Concluída com Sucesso!
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-8 border-2 border-[#d4af37] relative">
                
                <div class="absolute top-4 right-4 text-5xl opacity-40 select-none">🛡️</div>
                
                <div class="text-center mb-8 border-b border-[#8b5a2b]/30 pb-6">
                    <span class="text-6xl block mb-2">🎉</span>
                    <h2 class="text-3xl font-cinzel text-[#d4af37] font-bold tracking-wide">Tomo Adquirido!</h2>
                    <p class="text-sm italic text-base-content/70 mt-1">O pergaminho de transação foi selado com sucesso na Biblioteca de Alcantâra.</p>
                </div>

                <div class="space-y-6">
                    <div class="bg-[#120e0a] border border-[#8b5a2b]/30 p-5 rounded-lg">
                        <h3 class="font-cinzel text-[#d4af37] text-lg mb-3 border-b border-[#8b5a2b]/10 pb-2">Detalhes do Pergaminho</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-base-content/60">Número da Encomenda:</span>
                                <span class="block font-bold text-lg text-[#e8dcca]">#{{ $encomenda->id }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-base-content/60">Estado de Pagamento:</span>
                                <span class="block font-bold mt-0.5"><span class="badge badge-success">Paga & Confirmada</span></span>
                            </div>
                            <div>
                                <span class="font-semibold text-base-content/60">Data da Selagem:</span>
                                <span class="block text-[#e8dcca]">{{ $encomenda->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-base-content/60">Valor Total Pago:</span>
                                <span class="block font-bold text-[#d4af37] text-lg">€{{ number_format($encomenda->total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-[#120e0a] border border-[#8b5a2b]/30 p-5 rounded-lg">
                        <h3 class="font-cinzel text-[#d4af37] text-lg mb-3 border-b border-[#8b5a2b]/10 pb-2">Volumes a Serem Entregues</h3>
                        <div class="space-y-3">
                            @foreach($encomenda->items as $item)
                                <div class="flex justify-between items-center text-sm py-2 border-b border-[#8b5a2b]/10 last:border-0">
                                    <div>
                                        <div class="font-bold text-[#e8dcca]">{{ $item->nome_livro }}</div>
                                        <div class="text-xs text-base-content/50">Quantidade: {{ $item->quantidade }} x €{{ number_format($item->preco_unitario, 2, ',', '.') }}</div>
                                    </div>
                                    <div class="font-bold text-[#d4af37]">
                                        €{{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    
                    <div class="bg-[#120e0a] border border-[#8b5a2b]/30 p-5 rounded-lg">
                        <h3 class="font-cinzel text-base-content/80 text-md mb-2">Destino de Entrega</h3>
                        <p class="text-sm text-base-content/80 whitespace-pre-wrap italic">{{ $encomenda->morada }}</p>
                    </div>

                    <div class="text-center pt-4">
                        <p class="text-sm text-base-content/70 mb-6">Os nossos escribas já estão a preparar os manuscritos físicos para serem enviados pelo mensageiro real.</p>
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('catalogo') }}" class="btn btn-primary font-cinzel">📖 Voltar ao Catálogo</a>
                            <a href="{{ route('dashboard') }}" class="btn font-cinzel">📜 Ir ao Painel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
