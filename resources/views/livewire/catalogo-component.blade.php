<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6">
            @if (session()->has('message'))
                <div class="alert alert-success mb-6 shadow-lg">
                    <div>
                        <span>{{ session('message') }}</span>
                    </div>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-error mb-6 shadow-lg">
                    <div>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
                <h2 class="font-semibold text-xl text-base-content leading-tight">📖 Catálogo Público</h2>
                <input wire:model.live="search" type="text" placeholder="🔍 Procurar por ISBN ou título…" class="input input-bordered w-full max-w-xs" />
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Capa</th>
                            <th>Título</th>
                            <th>ISBN</th>
                            <th>Editora</th>
                            <th>Disponibilidade</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livros as $livro)
                            @php
                                $disponivel = $livro->requisicaoAtiva === null;
                            @endphp
                            <tr>
                                <td>
                                    @if($livro->imagem_capa)
                                        <div class="avatar">
                                            <div class="w-10 h-10 rounded">
                                                <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa" />
                                            </div>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="font-semibold">{{ $livro->nome }}</td>
                                <td>{{ $livro->isbn }}</td>
                                <td>{{ $livro->editora?->nome ?? '—' }}</td>
                                <td>
                                    @if($disponivel)
                                        <span class="badge badge-success">Disponível</span>
                                    @else
                                        <span class="badge badge-error">Indisponível</span>
                                    @endif
                                </td>
                                <td class="flex flex-wrap gap-1">
                                    <button wire:click="openLivroDetail({{ $livro->id }})" class="btn btn-sm btn-info">Detalhes</button>
                                    @auth
                                        @if($livro->temPrecoVenda())
                                            <button
                                                type="button"
                                                wire:click="adicionarAoCarrinho({{ $livro->id }})"
                                                class="btn btn-sm btn-primary"
                                            >
                                                🛒 Carrinho
                                            </button>
                                        @endif
                                        @if($disponivel)
                                            <a class="btn btn-sm btn-secondary" href="{{ route('requisicoes', ['livro' => $livro->id]) }}">Requisitar</a>
                                        @else
                                            @php
                                                $temAlerta = \App\Models\AlertaDisponibilidade::where('livro_id', $livro->id)->where('cidadao_id', auth()->id())->where('notificado', false)->exists();
                                            @endphp
                                            @if($temAlerta)
                                                <button wire:click="cancelarAlerta({{ $livro->id }})" class="btn btn-sm btn-warning">Cancelar Alerta</button>
                                            @else
                                                <button wire:click="solicitarAlerta({{ $livro->id }})" class="btn btn-sm btn-secondary">🔔 Notificar-me</button>
                                            @endif
                                        @endif
                                    @else
                                        <a class="btn btn-sm" href="{{ route('login') }}">Login</a>
                                    @endauth
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    @if($isDetailModalOpen && $selectedLivro)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-base-200 rounded-lg shadow-xl p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">{{ $selectedLivro->nome }}</h3>
                    <button wire:click="closeLivroDetail()" class="btn btn-sm btn-circle">✕</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-1">
                        @if($selectedLivro->imagem_capa)
                            <img src="{{ asset('storage/'.$selectedLivro->imagem_capa) }}" alt="Capa" class="w-full rounded-lg shadow" />
                        @else
                            <div class="w-full h-64 bg-gray-300 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500">Sem capa</span>
                            </div>
                        @endif

                        <div class="mt-4 space-y-2">
                            <div><strong>ISBN:</strong> {{ $selectedLivro->isbn }}</div>
                            <div><strong>Editora:</strong> {{ $selectedLivro->editora?->nome ?? '—' }}</div>
                            @if($selectedLivro->preco)
                                <div class="text-[#d4af37] font-bold text-lg my-1">💰 Preço: €{{ $selectedLivro->preco }}</div>
                            @endif
                            <div><strong>Autores:</strong></div>
                            @foreach($selectedLivro->autores as $autor)
                                <div class="ml-2">• {{ $autor->nome }}</div>
                            @endforeach
                            @if($selectedLivro->bibliografia)
                                <div class="mt-4">
                                    <strong>Descrição:</strong>
                                    <p class="text-sm mt-1">{{ $selectedLivro->bibliografia }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    
                    <div class="md:col-span-2 space-y-6">
                        
                        <div>
                            <h4 class="text-lg font-semibold mb-3">⭐ Reviews</h4>
                            @if($selectedLivro->reviewsAtivos->count() > 0)
                                <div class="space-y-3 max-h-60 overflow-y-auto">
                                    @foreach($selectedLivro->reviewsAtivos as $review)
                                        <div class="bg-[#120e0a] border border-[#8b5a2b]/30 p-3 rounded-lg shadow">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->classificacao)
                                                            ⭐
                                                        @else
                                                            ☆
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="font-semibold">{{ $review->cidadao_nome }}</span>
                                                <span class="text-xs text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <p class="text-sm">{{ $review->comentario }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">Ainda não há reviews para este livro.</p>
                            @endif
                        </div>

                        
                        @if(!empty($relatedLivros))
                            <div class="mt-6 pt-6 border-t border-base-300">
                                <h4 class="text-lg font-bold mb-4 flex items-center gap-2 text-base-content">
                                    <span class="text-primary">✨</span> Sugestões Relacionadas
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($relatedLivros as $related)
                                        @php
                                            $relLivro = $related['livro'];
                                            $similarityPercent = round($related['similarity'] * 100);
                                            $hasSharedAuthor = $related['shared_authors'] > 0;
                                            $isSamePublisher = $relLivro->editora_id && $relLivro->editora_id === $selectedLivro->editora_id;

                                            if ($similarityPercent >= 80) {
                                                $badgeColor = 'bg-success/15 text-success';
                                                $progressColor = 'bg-success';
                                                $affinityText = 'Afinidade Máxima';
                                            } elseif ($similarityPercent >= 50) {
                                                $badgeColor = 'bg-info/15 text-info';
                                                $progressColor = 'bg-info';
                                                $affinityText = 'Alta Afinidade';
                                            } else {
                                                $badgeColor = 'bg-primary/10 text-primary';
                                                $progressColor = 'bg-primary';
                                                $affinityText = 'Recomendado';
                                            }
                                        @endphp
                                        <div class="bg-[#120e0a] border border-[#8b5a2b]/30 hover:border-primary/40 rounded-xl p-3 shadow-sm cursor-pointer hover:shadow-md transition-all duration-300 hover:-translate-y-1 transform flex gap-3 group" 
                                             wire:click="openLivroDetail({{ $relLivro->id }})">
                                            
                                            
                                            <div class="w-16 h-22 flex-shrink-0 overflow-hidden rounded-lg shadow-sm bg-base-300 relative group-hover:shadow-md transition-all duration-300">
                                                @if($relLivro->imagem_capa)
                                                    <img src="{{ asset('storage/'.$relLivro->imagem_capa) }}" alt="Capa" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center">
                                                        <span class="text-lg">📖</span>
                                                    </div>
                                                @endif
                                            </div>

                                            
                                            <div class="flex-1 flex flex-col justify-between min-w-0">
                                                <div>
                                                    <div class="font-bold text-sm text-base-content line-clamp-1 group-hover:text-primary transition-colors duration-200" title="{{ $relLivro->nome }}">
                                                        {{ $relLivro->nome }}
                                                    </div>
                                                    <div class="text-xs text-base-content/60 mt-0.5 truncate">
                                                        @if($relLivro->autores->isNotEmpty())
                                                            de {{ $relLivro->autores->pluck('nome')->implode(', ') }}
                                                        @else
                                                            Autor desconhecido
                                                        @endif
                                                    </div>
                                                </div>

                                                
                                                <div class="mt-2">
                                                    <div class="flex items-center justify-between text-[10px] mb-1 font-semibold">
                                                        <span class="text-base-content/70">{{ $affinityText }}</span>
                                                        <span class="text-primary font-bold">{{ $similarityPercent }}%</span>
                                                    </div>
                                                    <div class="w-full bg-base-300 h-1.5 rounded-full overflow-hidden">
                                                        <div class="{{ $progressColor }} h-full rounded-full transition-all duration-500" style="width: {{ $similarityPercent }}%"></div>
                                                    </div>
                                                </div>

                                                
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @if($hasSharedAuthor)
                                                        <span class="badge badge-xs bg-primary/10 text-primary border-none text-[9px] py-1 px-1.5 font-bold">✍️ Autor Comum</span>
                                                    @endif
                                                    @if($isSamePublisher)
                                                        <span class="badge badge-xs bg-secondary/10 text-secondary border-none text-[9px] py-1 px-1.5 font-bold">🏢 Mesma Editora</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap justify-end items-center gap-3 mt-6 border-t border-[#8b5a2b]/20 pt-4">
                    @auth
                        @if($selectedLivro->temPrecoVenda())
                            <button
                                type="button"
                                wire:click="adicionarAoCarrinho({{ $selectedLivro->id }})"
                                class="btn btn-primary"
                            >
                                🛒 Adicionar ao Carrinho (€{{ $selectedLivro->preco }})
                            </button>
                        @else
                            <p class="text-sm text-warning italic mr-auto">
                                Este volume ainda não tem preço de venda — pede ao guardião para o definir no acervo.
                            </p>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Entrar para comprar</a>
                    @endauth
                    <button type="button" wire:click="closeLivroDetail()" class="btn">Fechar</button>
                </div>
            </div>
        </div>
    @endif
</div>

