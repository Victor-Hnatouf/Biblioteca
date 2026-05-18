<div>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-base-content leading-tight">
        📚 Acervo de Livros
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 relative">
            <div class="gothic-corner gothic-corner-tl"></div>
            <div class="gothic-corner gothic-corner-tr"></div>
            <div class="gothic-corner gothic-corner-bl"></div>
            <div class="gothic-corner gothic-corner-br"></div>

            <div class="page-toolbar">
                <div class="page-toolbar-actions">
                    @if(auth()->user()?->isAdmin())
                        <button wire:click="create()" class="btn btn-primary">✦ Registar Novo Tomo</button>
                        <button type="button" wire:click="openGoogleImport" class="btn btn-secondary">📖 Importar da Google Books</button>
                        <button wire:click="exportExcel()" class="btn btn-success">📜 Exportar Registos</button>
                    @endif
                </div>
                <input wire:model.live="search" type="text" placeholder="🔍 Buscar por ISBN ou título..." class="input input-bordered w-full max-w-xs" />
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success mb-4">
                    ✦ {{ session('message') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="cursor-pointer" wire:click="sortBy('id')">Nº</th>
                            <th>Capa</th>
                            <th class="cursor-pointer" wire:click="sortBy('isbn')">ISBN</th>
                            <th class="cursor-pointer" wire:click="sortBy('nome')">Título da Obra</th>
                            <th>Casa Editora</th>
                            <th>Escribas</th>
                            <th class="cursor-pointer" wire:click="sortBy('preco')">Valor</th>
                            <th>Acções</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livros as $livro)
                        <tr>
                            <td>{{ $livro->id }}</td>
                            <td>
                                @if($livro->imagem_capa)
                                    <div class="avatar">
                                        <div class="w-12 h-12 rounded" style="border: 1px solid rgba(139,90,43,0.3); box-shadow: 0 0 6px rgba(0,0,0,0.4);">
                                            <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa" />
                                        </div>
                                    </div>
                                @else
                                    <span style="color: rgba(139,90,43,0.4); font-size: 1.5rem;">📖</span>
                                @endif
                            </td>
                            <td>{{ $livro->isbn }}</td>
                            <td class="book-title">{{ $livro->nome }}</td>
                            <td>{{ $livro->editora ? $livro->editora->nome : '—' }}</td>
                            <td class="book-authors">{{ $livro->autores->pluck('nome')->implode(', ') ?: '—' }}</td>
                            <td>{{ $livro->preco ? '€' . $livro->preco : '—' }}</td>
                            <td class="align-top">
                                @if(auth()->user()?->isAdmin())
                                    <div class="table-actions-row">
                                        <button wire:click="edit({{ $livro->id }})" class="btn btn-sm btn-accent">Editar</button>
                                        <button wire:click="delete({{ $livro->id }})" class="btn btn-sm btn-error">Apagar</button>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-bar">
                <button wire:click="previousPage" class="btn btn-sm" @if($livros->currentPage() == 1) disabled @endif>← Anterior</button>
                <span>Pergaminho {{ $livros->currentPage() }} de {{ max(1, $livros->lastPage()) }}</span>
                <button wire:click="nextPage" class="btn btn-sm" @if($livros->currentPage() >= $livros->lastPage()) disabled @endif>Próxima →</button>
            </div>


        </div>
    </div>

    @if($googlePanelOpen)
        <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeGoogleImport"></div>
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
            <div class="medieval-modal-container relative p-6 sm:p-8 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto pointer-events-auto" wire:click.stop>
                <div class="gothic-corner gothic-corner-tl"></div>
                <div class="gothic-corner gothic-corner-tr"></div>
                <div class="gothic-corner gothic-corner-bl"></div>
                <div class="gothic-corner gothic-corner-br"></div>

                <button type="button" wire:click="closeGoogleImport" class="btn btn-sm modal-close-btn" aria-label="Fechar">✕</button>

                <h3 class="text-center uppercase tracking-widest mb-2 pr-10">Pesquisar na Google Books</h3>
                <p class="modal-lead text-center text-sm mb-5 max-w-2xl mx-auto">
                    Os resultados ficam apenas em ecrã até escolher <strong>Gravar no acervo</strong>. A gravação cria ou reutiliza editoras e autores pelo nome e descarrega a capa para o armazenamento local quando possível.
                </p>

                @if(!$googleConfigured)
                    <div class="alert alert-warning mb-4">
                        Defina <code>GOOGLE_BOOKS_API_KEY</code> no ficheiro <code>.env</code>
                        (<a class="link underline" href="https://developers.google.com/books/docs/v1/using" target="_blank" rel="noopener">documentação</a>).
                    </div>
                @endif

                @if($googleMessage)
                    <div class="alert alert-error mb-4">{{ $googleMessage }}</div>
                @endif

                <form wire:submit.prevent="searchGoogleBooks" class="google-search-form mb-5">
                    <input type="text" wire:model="googleQuery" class="input input-bordered flex-1" placeholder="Título, autor, ISBN ou palavras-chave…" @if(!$googleConfigured) disabled @endif />
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" @if(!$googleConfigured) disabled @endif>
                        <span wire:loading.remove wire:target="searchGoogleBooks">Pesquisar</span>
                        <span wire:loading wire:target="searchGoogleBooks" class="loading loading-spinner loading-sm"></span>
                    </button>
                </form>

                @if($googleSearching && $googleResults === [])
                    <div class="flex justify-center py-10"><span class="loading loading-dots loading-lg"></span></div>
                @endif

                @if($googleResults !== [])
                    <p class="text-sm mb-3" style="color: var(--parchment-dim);">Total aproximado na API: {{ number_format($googleTotal) }} · Mostrados: {{ count($googleResults) }}</p>
                    <div class="space-y-3">
                        @foreach($googleResults as $row)
                            <div class="google-result-card">
                                <div class="google-cover">
                                    @if(!empty($row['thumbnail_url']))
                                        <img
                                            src="{{ $row['thumbnail_url'] }}"
                                            alt="Capa de {{ $row['title'] }}"
                                            loading="lazy"
                                            referrerpolicy="no-referrer"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        />
                                        <span class="google-cover-fallback" style="display:none;">📖</span>
                                    @else
                                        <span class="google-cover-fallback">📖</span>
                                    @endif
                                </div>
                                <div class="google-result-meta">
                                    <div class="google-result-title">{{ $row['title'] }}</div>
                                    <div class="google-result-sub">{{ $row['authors_label'] }}</div>
                                    <div class="google-result-sub">
                                        @if(!empty($row['publisher'])){{ $row['publisher'] }}@endif
                                        @if(!empty($row['isbn'])) · ISBN {{ $row['isbn'] }}@endif
                                    </div>
                                    @if(!empty($row['description_preview']))
                                        <p class="google-result-desc">{{ $row['description_preview'] }}</p>
                                    @endif
                                </div>
                                <div class="google-result-action">
                                    <button type="button"
                                        class="btn btn-sm btn-accent whitespace-nowrap"
                                        wire:click="importGoogleVolume(@js($row['volume_id']))"
                                        wire:loading.attr="disabled"
                                        wire:target="importGoogleVolume"
                                        @if(!$googleConfigured) disabled @endif
                                    >Gravar no acervo</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($googleNextStart !== null)
                        <div class="mt-5 flex justify-center">
                            <button type="button" class="btn btn-outline btn-sm" wire:click="loadMoreGoogleBooks" wire:loading.attr="disabled" @if(!$googleConfigured) disabled @endif>
                                <span wire:loading.remove wire:target="loadMoreGoogleBooks">Carregar mais</span>
                                <span wire:loading wire:target="loadMoreGoogleBooks" class="loading loading-spinner loading-sm"></span>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if($isModalOpen)
    <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeModalPopover()"></div>
    
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
        <div wire:key="livro-modal-final" class="medieval-modal-container relative p-8 rounded-lg max-w-3xl w-full pointer-events-auto overflow-visible" style="display: block !important;">
            <div class="gothic-corner gothic-corner-tl"></div>
            <div class="gothic-corner gothic-corner-tr"></div>
            <div class="gothic-corner gothic-corner-bl"></div>
            <div class="gothic-corner gothic-corner-br"></div>

            <button wire:click="closeModalPopover()" class="btn btn-sm modal-close-btn" aria-label="Fechar">✕</button>
            
            <h3 class="text-center uppercase tracking-widest mb-6 pr-10">
                {{ $livro_id ? '✦ Editar Tomo Antigo' : '✦ Registar Novo Tomo' }}
            </h3>

            <div class="ornate-divider" style="margin: 1.5rem auto; opacity: 0.3;"></div>

            <form wire:submit.prevent="store" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label"><span class="label-text uppercase text-xs tracking-widest">ISBN</span></label>
                        <div class="join w-full">
                            <input type="text" wire:model="isbn" class="input input-bordered join-item w-full bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]" placeholder="978-X-XXXX-XXXX-X" />
                            <button type="button" wire:click="generateISBN" class="btn btn-accent join-item border-[#8b5a2b] hover:bg-[#d4af37] hover:text-black" title="Gerar código aleatório">🎲</button>
                        </div>
                        @error('isbn') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text uppercase text-xs tracking-widest">Título da Obra</span></label>
                        <input type="text" wire:model="nome" class="input input-bordered bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]" placeholder="Nome do pergaminho..." />
                        @error('nome') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label"><span class="label-text uppercase text-xs tracking-widest">Casa Editora</span></label>
                        <select wire:model="editora_id" class="select select-bordered bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]">
                            <option value="">Selecione o selo editorial...</option>
                            @foreach($editoras_list as $ed)
                                <option value="{{ $ed->id }}">{{ $ed->nome }}</option>
                            @endforeach
                        </select>
                        @error('editora_id') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text uppercase text-xs tracking-widest">Valor do Tributo (€)</span></label>
                        <input type="number" step="0.01" wire:model="preco" class="input input-bordered bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]" placeholder="0.00" />
                        @error('preco') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text uppercase text-xs tracking-widest">Escribas & Autores</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-[#120e0a]/80 p-4 rounded border border-[#8b5a2b]/40 max-h-32 overflow-y-auto custom-scrollbar">
                        @foreach($autores_list as $au)
                        <label class="cursor-pointer flex items-center space-x-2 p-1 rounded hover:bg-[#8b5a2b]/20 transition-colors">
                            <input type="checkbox" wire:model="autores_selecionados" value="{{ $au->id }}" class="checkbox checkbox-primary checkbox-xs border-[#8b5a2b]" />
                            <span class="label-text text-[#c9bcae] text-[11px]">{{ $au->nome }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text uppercase text-xs tracking-widest">Sinopse & Bibliografia</span></label>
                    <textarea wire:model="bibliografia" class="textarea textarea-bordered h-20 bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]" placeholder="Breve descrição do conteúdo sagrado..."></textarea>
                    @error('bibliografia') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text uppercase text-xs tracking-widest">Iluminura da Capa</span></label>
                    <div class="flex items-center gap-4 bg-[#120e0a]/50 p-2 rounded border border-[#8b5a2b]/30">
                        <input type="file" wire:model="new_imagem_capa" class="file-input file-input-bordered flex-1 bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] file-input-sm" />
                        @if ($new_imagem_capa)
                            <div class="w-12 h-12 rounded border border-[#d4af37] overflow-hidden shadow-lg">
                                <img src="{{ $new_imagem_capa->temporaryUrl() }}" class="object-cover w-full h-full">
                            </div>
                        @elseif($imagem_capa)
                            <div class="w-12 h-12 rounded border border-[#d4af37] overflow-hidden shadow-lg">
                                <img src="{{ asset('storage/'.$imagem_capa) }}" class="object-cover w-full h-full">
                            </div>
                        @endif
                    </div>
                    @error('new_imagem_capa') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit" class="btn btn-primary btn-lg w-full shadow-[0_4px_20px_rgba(107,16,16,0.6)] border-2 border-[#d4af37]/30 hover:border-[#d4af37] uppercase tracking-widest text-sm">
                        ✦ Selar o Registo Eterno
                    </button>
                </div>
            </form>

            @if($livro_id && !empty($historico_requisicoes))
                <div class="ornate-divider" style="margin: 2rem auto; opacity: 0.25;"></div>
                <h4 class="text-center uppercase tracking-widest mb-4">Histórico de Requisições</h4>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Cidadão</th>
                                <th>Requisitado</th>
                                <th>Previsto</th>
                                <th>Devolução (cidadão)</th>
                                <th>Concluída</th>
                                <th>Condição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historico_requisicoes as $h)
                                <tr>
                                    <td>#{{ $h['numero'] }}</td>
                                    <td>{{ $h['cidadao_nome'] }}<div class="text-xs opacity-60">{{ $h['cidadao_email'] }}</div></td>
                                    <td>{{ $h['requisitado_em'] }}</td>
                                    <td>{{ $h['previsto_entrega_em'] }}</td>
                                    <td>{{ $h['cidadao_entregou_em'] ?: '—' }}</td>
                                    <td>
                                        @if($h['entregue_em'])
                                            {{ $h['entregue_em'] }}
                                            @if($h['dias_decorridos'] !== null)
                                                <div class="text-xs opacity-60">{{ $h['dias_decorridos'] }} dias</div>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $h['condicao_na_devolucao'] ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>
</div>
