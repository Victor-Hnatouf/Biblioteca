<div>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-base-content leading-tight">
        🏰 Casas Editoras
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 relative">
            <div class="gothic-corner gothic-corner-tl"></div>
            <div class="gothic-corner gothic-corner-tr"></div>
            <div class="gothic-corner gothic-corner-bl"></div>
            <div class="gothic-corner gothic-corner-br"></div>

            <div class="flex justify-between mb-6 items-center flex-wrap gap-3">
                <button wire:click="create()" class="btn btn-primary">✦ Registar Nova Casa</button>
                <input wire:model.live="search" type="text" placeholder="🔍 Buscar casa editora..." class="input input-bordered w-full max-w-xs" />
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
                            <th>Brasão</th>
                            <th class="cursor-pointer" wire:click="sortBy('nome')">Nome da Casa</th>
                            <th>Acções</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($editoras as $editora)
                        <tr>
                            <td>{{ $editora->id }}</td>
                            <td>
                                @if($editora->logotipo)
                                    <div class="avatar">
                                        <div class="w-12 h-12 rounded" style="border: 1px solid rgba(139,90,43,0.3); box-shadow: 0 0 6px rgba(0,0,0,0.4);">
                                            <img src="{{ asset('storage/'.$editora->logotipo) }}" alt="Brasão" />
                                        </div>
                                    </div>
                                @else
                                    <span style="color: rgba(139,90,43,0.4); font-size: 1.5rem;">🏛️</span>
                                @endif
                            </td>
                            <td style="font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 1.1rem;">{{ $editora->nome }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <button wire:click="edit({{ $editora->id }})" class="btn btn-sm btn-accent">Editar</button>
                                    <button wire:click="delete({{ $editora->id }})" class="btn btn-sm btn-error">Apagar</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-between items-center" style="border-top: 1px solid rgba(139,90,43,0.15); padding-top: 1rem;">
                <button wire:click="previousPage" class="btn btn-sm" @if($editoras->currentPage() == 1) disabled @endif>← Anterior</button>
                <span style="font-family: 'Cinzel', serif; color: #8b5a2b; font-size: 0.85rem;">Pergaminho {{ $editoras->currentPage() }} de {{ max(1, $editoras->lastPage()) }}</span>
                <button wire:click="nextPage" class="btn btn-sm" @if($editoras->currentPage() >= $editoras->lastPage()) disabled @endif>Próxima →</button>
            </div>
        </div>

      @if($isModalOpen)
    <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeModalPopover()"></div>
    
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
        <div wire:key="editora-modal-improved" class="medieval-modal-container relative p-8 rounded-lg max-w-xl w-full pointer-events-auto overflow-visible" style="display: block !important;">
            <div class="gothic-corner gothic-corner-tl"></div>
            <div class="gothic-corner gothic-corner-tr"></div>
            <div class="gothic-corner gothic-corner-bl"></div>
            <div class="gothic-corner gothic-corner-br"></div>

            <button wire:click="closeModalPopover()" class="btn btn-sm btn-circle absolute right-[-15px] top-[-15px] bg-[#6b1010] border-[#d4af37] text-[#e8dcca] hover:bg-[#8b2020] z-50 shadow-lg">✕</button>
            <h3 class="text-center uppercase tracking-widest">
                {{ $editora_id ? '✦ Editar Casa Editora' : '✦ Nova Casa Editora' }}
            </h3>

            <div class="ornate-divider" style="margin: 1.5rem auto; opacity: 0.3;"></div>

            <form wire:submit.prevent="store" class="space-y-6">
                <div class="form-control">
                    <label class="label"><span class="label-text uppercase text-xs tracking-widest">Nome da Casa</span></label>
                    <input type="text" wire:model="nome" class="input input-bordered bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] focus:border-[#d4af37]" placeholder="O nome desta nobre casa..." />
                    @error('nome') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text uppercase text-xs tracking-widest">Brasão da Casa</span></label>
                    <div class="flex items-center gap-4 bg-[#120e0a]/50 p-2 rounded border border-[#8b5a2b]/30">
                        <input type="file" wire:model="new_logotipo" class="file-input file-input-bordered flex-1 bg-[#120e0a] border-[#8b5a2b] text-[#e8dcca] file-input-sm" />
                        @if ($new_logotipo)
                            <div class="w-16 h-16 rounded border-2 border-[#d4af37] overflow-hidden shadow-lg">
                                <img src="{{ $new_logotipo->temporaryUrl() }}" class="object-cover w-full h-full">
                            </div>
                        @elseif($logotipo)
                            <div class="w-16 h-16 rounded border-2 border-[#d4af37] overflow-hidden shadow-lg">
                                <img src="{{ asset('storage/'.$logotipo) }}" class="object-cover w-full h-full">
                            </div>
                        @endif
                    </div>
                    @error('new_logotipo') <span class="text-[#f87272] text-xs mt-1">{{ $message }}</span>@enderror
                </div>
                <div class="flex justify-center mt-8">
                    <button type="submit" class="btn btn-primary px-12 shadow-[0_4_20_rgba(107,16,16,0.5)] uppercase tracking-widest text-sm">
                        ✦ Selar Registo
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    </div>
    </div>
</div>
