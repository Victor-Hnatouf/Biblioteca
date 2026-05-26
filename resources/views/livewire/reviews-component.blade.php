<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="font-semibold text-xl text-base-content leading-tight">⭐ Reviews</h2>
            </div>

            @if(auth()->user()?->isAdmin())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="stat bg-base-200 rounded-lg shadow">
                        <div class="stat-title">Suspensas</div>
                        <div class="stat-value text-warning">{{ $suspensoCount }}</div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if(session()->has('message'))
                    <div class="alert alert-success mb-4">
                        {{ session()->get('message') }}
                    </div>
                @endif

                @if(auth()->user()?->isAdmin())
                    <div class="tabs tabs-boxed mb-6">
                        <button 
                            wire:click="setAdminVista('suspenso')"
                            class="tab {{ $adminVista === 'suspenso' ? 'tab-active' : '' }}"
                        >
                            Suspensas
                        </button>
                        <button 
                            wire:click="setAdminVista('ativo')"
                            class="tab {{ $adminVista === 'ativo' ? 'tab-active' : '' }}"
                        >
                            Ativas
                        </button>
                        <button 
                            wire:click="setAdminVista('recusado')"
                            class="tab {{ $adminVista === 'recusado' ? 'tab-active' : '' }}"
                        >
                            Recusadas
                        </button>
                        <button 
                            wire:click="setAdminVista('todas')"
                            class="tab {{ $adminVista === 'todas' ? 'tab-active' : '' }}"
                        >
                            Todas
                        </button>
                    </div>
                @endif

                @if($reviews->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th>Cidadão</th>
                                    <th>Classificação</th>
                                    <th>Comentário</th>
                                    <th>Estado</th>
                                    <th>Data</th>
                                    @if(auth()->user()?->isAdmin())
                                        <th>Ações</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                    <tr>
                                        <td>
                                            <div class="font-semibold">{{ $review->livro->nome }}</div>
                                            <div class="text-xs text-gray-500">{{ $review->livro->isbn }}</div>
                                        </td>
                                        <td>
                                            <div class="font-semibold">{{ $review->cidadao_nome }}</div>
                                            <div class="text-xs text-gray-500">{{ $review->cidadao_email }}</div>
                                        </td>
                                        <td>
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->classificacao)
                                                        ⭐
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w-xs truncate">{{ $review->comentario }}</div>
                                        </td>
                                        <td>
                                            @if($review->estado === 'suspenso')
                                                <span class="badge badge-warning">Suspenso</span>
                                            @elseif($review->estado === 'ativo')
                                                <span class="badge badge-success">Ativo</span>
                                            @elseif($review->estado === 'recusado')
                                                <span class="badge badge-error">Recusado</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                        @if(auth()->user()?->isAdmin())
                                            <td>
                                                <button 
                                                    wire:click="openDetailModal({{ $review->id }})"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Ver Detalhes
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        Nenhuma review encontrada.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para criar review -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="medieval-modal-container relative p-6 sm:p-8 rounded-lg max-w-lg w-full pointer-events-auto">
                <div class="gothic-corner gothic-corner-tl"></div>
                <div class="gothic-corner gothic-corner-tr"></div>
                <div class="gothic-corner gothic-corner-bl"></div>
                <div class="gothic-corner gothic-corner-br"></div>

                <button type="button" wire:click="closeModal" class="btn btn-sm modal-close-btn" aria-label="Fechar">✕</button>

                <h3 class="text-center uppercase tracking-widest mb-4">Nova Review</h3>
                
                <form wire:submit.prevent="criarReview">
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Livro</span>
                        </label>
                        <select wire:model="livro_id" class="select select-bordered w-full" required>
                            <option value="">Selecione um livro</option>
                            @foreach(\App\Models\Livro::all() as $livro)
                                <option value="{{ $livro->id }}">{{ $livro->nome }}</option>
                            @endforeach
                        </select>
                        @error('livro_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Classificação</span>
                        </label>
                        <div
                            x-data="{ hovered: 0, selected: {{ (int)($classificacao ?? 0) }} }"
                            x-on:mouseleave="hovered = 0"
                            class="flex gap-1 mt-1"
                        >
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    x-on:mouseenter="hovered = {{ $i }}"
                                    x-on:click="selected = {{ $i }}; $wire.set('classificacao', {{ $i }})"
                                    class="text-4xl leading-none transition-all duration-150 cursor-pointer focus:outline-none"
                                    :style="(hovered >= {{ $i }} || selected >= {{ $i }}) ? 'color:#d4af37; filter:drop-shadow(0 0 6px rgba(212,175,55,0.8)); transform:scale(1.2);' : 'color:rgba(212,175,55,0.22);'"
                                    title="{{ $i }} estrela{{ $i > 1 ? 's' : '' }}"
                                >★</button>
                            @endfor
                        </div>
                        @error('classificacao') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Comentário</span>
                        </label>
                        <textarea 
                            wire:model="comentario" 
                            class="textarea textarea-bordered h-24 w-full" 
                            placeholder="Escreve a tua review..."
                            required
                        ></textarea>
                        @error('comentario') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Submeter Review</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal para detalhes da review (admin) -->
    @if($isDetailModalOpen && $reviewDetail)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="medieval-modal-container relative p-6 sm:p-8 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto pointer-events-auto">
                <div class="gothic-corner gothic-corner-tl"></div>
                <div class="gothic-corner gothic-corner-tr"></div>
                <div class="gothic-corner gothic-corner-bl"></div>
                <div class="gothic-corner gothic-corner-br"></div>

                <button type="button" wire:click="closeDetailModal" class="btn btn-sm modal-close-btn" aria-label="Fechar">✕</button>

                <h3 class="text-center uppercase tracking-widest mb-6">Detalhes da Review</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-semibold">Livro:</span>
                            <p>{{ $reviewDetail->livro->nome }}</p>
                            <p class="text-sm text-gray-500">{{ $reviewDetail->livro->isbn }}</p>
                        </div>
                        <div>
                            <span class="font-semibold">Cidadão:</span>
                            <p>{{ $reviewDetail->cidadao_nome }}</p>
                            <p class="text-sm text-gray-500">{{ $reviewDetail->cidadao_email }}</p>
                        </div>
                    </div>

                    <div>
                        <span class="font-semibold">Classificação:</span>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $reviewDetail->classificacao)
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                    </div>

                    <div>
                        <span class="font-semibold">Comentário:</span>
                        <p class="bg-[#120e0a] border border-[#8b5a2b]/30 p-3 rounded text-[#e8dcca]">{{ $reviewDetail->comentario }}</p>
                    </div>

                    <div>
                        <span class="font-semibold">Estado:</span>
                        @if($reviewDetail->estado === 'suspenso')
                            <span class="badge badge-warning">Suspenso</span>
                        @elseif($reviewDetail->estado === 'ativo')
                            <span class="badge badge-success">Ativo</span>
                        @elseif($reviewDetail->estado === 'recusado')
                            <span class="badge badge-error">Recusado</span>
                        @endif
                    </div>

                    @if($reviewDetail->estado === 'recusado' && $reviewDetail->justificacao_recusa)
                        <div>
                            <span class="font-semibold">Justificação da recusa:</span>
                            <p class="bg-red-950/20 border border-red-500/30 p-3 rounded text-red-200">{{ $reviewDetail->justificacao_recusa }}</p>
                        </div>
                    @endif

                    @if($reviewDetail->aprovadoPorAdmin)
                        <div>
                            <span class="font-semibold">Aprovado/Recusado por:</span>
                            <p>{{ $reviewDetail->aprovadoPorAdmin->name }}</p>
                            <p class="text-sm text-gray-500">{{ $reviewDetail->aprovado_em->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                    @if($reviewDetail->estado === 'suspenso')
                        <div class="border-t pt-4">
                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text">Justificação (para recusar)</span>
                                </label>
                                <textarea 
                                    wire:model="justificacao_recusa" 
                                    class="textarea textarea-bordered h-20 w-full" 
                                    placeholder="Justificação para recusar a review..."
                                ></textarea>
                                @error('justificacao_recusa') <span class="text-error text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end gap-2">
                                <button 
                                    wire:click="aprovarReview({{ $reviewDetail->id }})"
                                    class="btn btn-success"
                                >
                                    Aprovar
                                </button>
                                <button 
                                    wire:click="recusarReview({{ $reviewDetail->id }})"
                                    class="btn btn-error"
                                >
                                    Recusar
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="closeDetailModal" class="btn">Fechar</button>
                </div>
            </div>
        </div>
    @endif
</div>
