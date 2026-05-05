<div>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-base-content leading-tight">
        📚 Acervo de Livros
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 relative">
            {{-- Corner ornaments --}}
            <div class="gothic-corner gothic-corner-tl"></div>
            <div class="gothic-corner gothic-corner-tr"></div>
            <div class="gothic-corner gothic-corner-bl"></div>
            <div class="gothic-corner gothic-corner-br"></div>

            <div class="flex justify-between mb-6 items-center flex-wrap gap-3">
                <div class="flex gap-2 flex-wrap">
                    <button wire:click="create()" class="btn btn-primary">✦ Registar Novo Tomo</button>
                    <button wire:click="exportExcel()" class="btn btn-success">📜 Exportar Registos</button>
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
                            <td style="font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 1.1rem;">{{ $livro->nome }}</td>
                            <td>{{ $livro->editora ? $livro->editora->nome : '—' }}</td>
                            <td style="font-style: italic;">{{ $livro->autores->pluck('nome')->implode(', ') }}</td>
                            <td>{{ $livro->preco ? '€' . $livro->preco : '—' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <button wire:click="edit({{ $livro->id }})" class="btn btn-sm btn-accent">Editar</button>
                                    <button wire:click="delete({{ $livro->id }})" class="btn btn-sm btn-error">Apagar</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-between items-center" style="border-top: 1px solid rgba(139,90,43,0.15); padding-top: 1rem;">
                <button wire:click="previousPage" class="btn btn-sm" @if($livros->currentPage() == 1) disabled @endif>← Anterior</button>
                <span style="font-family: 'Cinzel', serif; color: #8b5a2b; font-size: 0.85rem;">Pergaminho {{ $livros->currentPage() }} de {{ max(1, $livros->lastPage()) }}</span>
                <button wire:click="nextPage" class="btn btn-sm" @if($livros->currentPage() >= $livros->lastPage()) disabled @endif>Próxima →</button>
            </div>


        </div>
    </div>

    @if($isModalOpen)
    {{-- Solid Backdrop (No Blur) --}}
    <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeModalPopover()"></div>
    
    {{-- themed Modal Container --}}
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
        <div wire:key="livro-modal-final" class="medieval-modal-container relative p-8 rounded-lg max-w-3xl w-full pointer-events-auto overflow-visible" style="display: block !important;">
            {{-- Decorative corner ornaments --}}
            <div class="gothic-corner gothic-corner-tl"></div>
            <div class="gothic-corner gothic-corner-tr"></div>
            <div class="gothic-corner gothic-corner-bl"></div>
            <div class="gothic-corner gothic-corner-br"></div>

            <button wire:click="closeModalPopover()" class="btn btn-sm btn-circle absolute right-[-15px] top-[-15px] bg-[#6b1010] border-[#d4af37] text-[#e8dcca] hover:bg-[#8b2020] z-50 shadow-lg">✕</button>
            
            <h3 class="text-center uppercase tracking-widest mb-6">
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
        </div>
    </div>
    @endif

</div>
</div>
