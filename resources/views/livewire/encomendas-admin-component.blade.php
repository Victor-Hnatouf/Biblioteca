<div class="py-12">
    <div class="max-w-[95rem] mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-[#8b5a2b]/30">
            
            <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
                <h2 class="font-semibold text-2xl text-[#d4af37] font-cinzel leading-tight tracking-wide">🛍️ Registro das Encomendas de Livros</h2>
                
                <div class="flex items-center gap-3 flex-wrap">
                    
                    <div class="join">
                        <button wire:click="$set('filterEstado', 'todas')" class="btn btn-sm join-item {{ $filterEstado === 'todas' ? 'btn-primary' : 'btn-ghost' }}">Todas</button>
                        <button wire:click="$set('filterEstado', 'paga')" class="btn btn-sm join-item {{ $filterEstado === 'paga' ? 'btn-primary' : 'btn-ghost' }}">Pagas</button>
                        <button wire:click="$set('filterEstado', 'pendente')" class="btn btn-sm join-item {{ $filterEstado === 'pendente' ? 'btn-primary' : 'btn-ghost' }}">Pendentes</button>
                    </div>

                    
                    <input wire:model.live="search" type="text" placeholder="🔍 Procurar por ID ou Cidadão…" class="input input-bordered w-full max-w-xs btn-sm" />
                </div>
            </div>

            
            @if(session()->has('message'))
                <div class="alert alert-success mb-6 shadow">
                    <div>
                        <span>{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="w-16">ID</th>
                            <th>Cidadão</th>
                            <th>Morada de Entrega</th>
                            <th>Data do Registo</th>
                            <th class="text-right">Total da Venda</th>
                            <th class="text-center">Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encomendas as $e)
                            <tr class="{{ $expandedOrderId === $e->id ? 'active-row' : '' }}">
                                <td class="font-bold text-[#d4af37]">#{{ $e->id }}</td>
                                <td>
                                    <div class="font-bold text-[#e8dcca]">{{ $e->user?->name ?? 'Nobre Desconhecido' }}</div>
                                    <div class="text-xs text-base-content/60">{{ $e->user?->email }}</div>
                                </td>
                                <td class="max-w-xs truncate italic" title="{{ $e->morada }}">
                                    {{ $e->morada }}
                                </td>
                                <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-right text-[#d4af37] font-bold">
                                    €{{ number_format($e->total, 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($e->estado === 'paga')
                                        <span class="badge badge-success">Paga</span>
                                    @else
                                        <span class="badge badge-warning">Pendente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="toggleExpandOrder({{ $e->id }})" class="btn btn-xs btn-info">
                                            {{ $expandedOrderId === $e->id ? 'Ocultar' : 'Detalhes' }}
                                        </button>
                                        
                                        
                                        @if($e->estado === 'pendente')
                                            <button wire:click="alterarEstado({{ $e->id }}, 'paga')" class="btn btn-xs btn-success btn-outline">
                                                Confirmar Pago
                                            </button>
                                        @else
                                            <button wire:click="alterarEstado({{ $e->id }}, 'pendente')" class="btn btn-xs btn-error btn-outline">
                                                Reverter Pago
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            
                            @if($expandedOrderId === $e->id)
                                <tr>
                                    <td colspan="7" class="bg-[#120e0a] border-l-2 border-[#d4af37] p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                                            
                                            <div class="md:col-span-1 space-y-2">
                                                <h4 class="font-cinzel text-[#d4af37] text-md border-b border-[#8b5a2b]/20 pb-1 font-bold">Informação de Envio</h4>
                                                <p><span class="font-semibold text-base-content/60">Cidadão:</span> {{ $e->user?->name }}</p>
                                                <p><span class="font-semibold text-base-content/60">Email:</span> {{ $e->user?->email }}</p>
                                                <p class="whitespace-pre-wrap leading-relaxed"><span class="font-semibold text-base-content/60 block">Morada completa:</span> <span class="italic">{{ $e->morada }}</span></p>
                                                @if($e->stripe_session_id)
                                                    <p class="text-xs text-base-content/40"><span class="font-semibold">Stripe ID:</span> {{ $e->stripe_session_id }}</p>
                                                @endif
                                            </div>

                                            
                                            <div class="md:col-span-2 space-y-2">
                                                <h4 class="font-cinzel text-[#d4af37] text-md border-b border-[#8b5a2b]/20 pb-1 font-bold">Tomos Escolhidos ({{ $e->items->count() }})</h4>
                                                <div class="overflow-x-auto bg-[#1c1815] rounded border border-[#8b5a2b]/10 p-3">
                                                    <table class="table table-compact w-full text-xs">
                                                        <thead>
                                                            <tr class="border-b border-[#8b5a2b]/20">
                                                                <th>Manuscrito</th>
                                                                <th class="text-right">Unitário</th>
                                                                <th class="text-center">Qtd.</th>
                                                                <th class="text-right">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($e->items as $item)
                                                                <tr class="border-b border-[#8b5a2b]/10 last:border-0">
                                                                    <td class="font-bold text-[#e8dcca]">{{ $item->nome_livro }}</td>
                                                                    <td class="text-right">€{{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                                                    <td class="text-center font-bold">{{ $item->quantidade }}</td>
                                                                    <td class="text-right text-[#d4af37] font-bold">€{{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 italic text-base-content/60">
                                    Não foram encontradas encomendas nos arquivos da Biblioteca Medieval.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
