<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="font-semibold text-xl text-base-content leading-tight">📦 Requisições</h2>
                <div x-data="{ open: false }">
                    <template x-teleport="body">
                        <div>
                            <button
                                type="button"
                                @click="open = !open"
                                class="fixed z-[9999] w-12 h-12 rounded-full flex items-center justify-center border-2 shadow-xl"
                                style="right: 1.5rem; bottom: 1.5rem; background: #5b351a; border-color: #d4af37; color: #d4af37;"
                                aria-label="Informação sobre requisições"
                                title="Informação"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm9.75-4.5a.75.75 0 0 0 0 1.5h.007a.75.75 0 0 0 0-1.5H12Zm.75 3.75a.75.75 0 0 0-1.5 0v6a.75.75 0 0 0 1.5 0v-6Z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div
                                x-cloak
                                x-show="open"
                                x-transition
                                @click.outside="open = false"
                                class="fixed z-[9999] w-[30rem] max-w-[92vw] max-h-[75vh] overflow-y-auto rounded-xl shadow-2xl p-4 border-2"
                                style="right: 1.5rem; bottom: 5.25rem; background: #2b1a0f; border-color: #d4af37; color: #e8dcca;"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="font-semibold tracking-wide text-center flex-1" style="color:#d4af37; font-family: 'Cinzel', serif;">
                                        Como requisitar (regras rápidas)
                                    </div>
                                    <button
                                        type="button"
                                        class="w-9 h-9 rounded-full flex items-center justify-center border shrink-0"
                                        style="border-color:#d4af37; color:#d4af37; background: rgba(212,175,55,0.08);"
                                        @click="open = false"
                                        aria-label="Fechar"
                                    >✕</button>
                                </div>

                                <div class="mt-3 space-y-3 text-sm leading-relaxed">
                                    <div class="p-3 rounded-lg border" style="border-color: rgba(212,175,55,0.35); background: rgba(91,53,26,0.18);">
                                        <div class="font-semibold mb-2 text-center" style="color:#d4af37; font-family: 'Cinzel', serif;">Passos</div>
                                        <ol class="list-decimal list-inside space-y-1 text-center" style="color:#e8dcca;">
                                            <li>Escolhe um livro disponível (no catálogo público ou aqui na lista).</li>
                                            <li>Clica em “Requisitar” e confirma.</li>
                                            <li>A data prevista de entrega é sempre 5 dias após a requisição.</li>
                                        </ol>
                                    </div>

                                    <div class="p-3 rounded-lg border" style="border-color: rgba(212,175,55,0.35); background: rgba(91,53,26,0.18);">
                                        <div class="font-semibold mb-2 text-center" style="color:#d4af37; font-family: 'Cinzel', serif;">Regras</div>
                                        <ul class="list-disc list-inside space-y-1 text-center" style="color:#e8dcca;">
                                            <li>Um cidadão pode ter no máximo <span class="font-semibold" style="color:#f3e6c7;">3</span> requisições ativas.</li>
                                            <li>Um livro só pode estar em <span class="font-semibold" style="color:#f3e6c7;">uma</span> requisição ativa de cada vez.</li>
                                            <li>Quando devolveres o exemplar, usa <strong>Indicar devolução na biblioteca</strong>; depois a equipa relata o estado do livro (boas, medianas ou más condições) e fecha a requisição.</li>
                                        </ul>
                                    </div>

                                    <div class="p-3 rounded-lg border" style="border-color: rgba(212,175,55,0.35); background: rgba(91,53,26,0.18);">
                                        <div class="font-semibold mb-2 text-center" style="color:#d4af37; font-family: 'Cinzel', serif;">Atalho</div>
                                        <div class="text-center" style="color:#e8dcca;">
                                            Se vieste do Catálogo, a página pode abrir já com o modal preparado.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 {{ auth()->user()?->isAdmin() ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-3">
                <div class="stat bg-base-200 rounded-lg shadow">
                    <div class="stat-title">Requisições Ativas</div>
                    <div class="stat-value text-primary">{{ $requisicoesAtivas }}</div>
                </div>
                <div class="stat bg-base-200 rounded-lg shadow">
                    <div class="stat-title">Últimos 30 dias</div>
                    <div class="stat-value text-accent">{{ $ultimos30Dias }}</div>
                </div>
                @if(auth()->user()?->isAdmin())
                    <div class="stat bg-base-200 rounded-lg shadow">
                        <div class="stat-title">Por relatar</div>
                        <div class="stat-value text-warning">{{ $porRelatarCount }}</div>
                    </div>
                @endif
                <div class="stat bg-base-200 rounded-lg shadow">
                    <div class="stat-title">Concluídas hoje</div>
                    <div class="stat-value text-success">{{ $entreguesHoje }}</div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if(auth()->user()?->isAdmin())
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" wire:click="setAdminVista('todas')" class="btn btn-sm {{ $adminVista === 'todas' ? 'btn-primary' : 'btn-ghost' }}">Todas as requisições</button>
                        <button type="button" wire:click="setAdminVista('por_relatar')" class="btn btn-sm {{ $adminVista === 'por_relatar' ? 'btn-primary' : 'btn-ghost' }}">
                            Por relatar
                            @if(($porRelatarCount ?? 0) > 0)
                                <span class="badge badge-warning badge-sm ml-1">{{ $porRelatarCount }}</span>
                            @endif
                        </button>
                    </div>
                    @if($adminVista === 'por_relatar')
                        <p class="text-sm opacity-80 mb-4">O cidadão já indicou que entregou o livro na biblioteca. Escolhe a condição do exemplar para concluir a requisição.</p>
                    @endif
                @endif

                @if (session()->has('message'))
                    <div class="alert alert-success mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Livro</th>
                                @if(auth()->user()?->isAdmin())
                                    <th>Cidadão</th>
                                @endif
                                <th>Requisitado</th>
                                <th>Previsto</th>
                                <th>Devolução (cidadão)</th>
                                <th>Concluída</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requisicoes as $r)
                                <tr>
                                    <td>#{{ $r->numero }}</td>
                                    <td class="font-semibold">{{ $r->livro?->nome ?? '—' }}</td>
                                    @if(auth()->user()?->isAdmin())
                                        <td>{{ $r->cidadao_nome }}<div class="text-xs opacity-60">{{ $r->cidadao_email }}</div></td>
                                    @endif
                                    <td>{{ optional($r->requisitado_em)->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($r->previsto_entrega_em)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($r->cidadao_entregou_em)
                                            {{ optional($r->cidadao_entregou_em)->format('d/m/Y H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($r->entregue_em)
                                            {{ optional($r->entregue_em)->format('d/m/Y') }}
                                            @if($r->dias_decorridos !== null)
                                                <div class="text-xs opacity-60">{{ $r->dias_decorridos }} dias</div>
                                            @endif
                                            @if($r->condicao_na_devolucao)
                                                <div class="text-xs opacity-70 mt-1">{{ \App\Models\Requisicao::labelCondicao($r->condicao_na_devolucao) }}</div>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="align-top min-w-[11rem]">
                                        @if($r->entregue_em)
                                            <span class="badge badge-success">Concluída</span>
                                        @elseif($r->cidadao_entregou_em)
                                            @if(auth()->user()?->isAdmin())
                                                <span class="badge badge-warning">Por relatar</span>
                                            @else
                                                <span class="badge badge-info">Aguarda biblioteca</span>
                                            @endif
                                        @else
                                            <span class="badge badge-neutral">Ativa</span>
                                        @endif
                                    </td>
                                    <td class="align-top min-w-[12rem]">
                                        <div class="table-actions">
                                            <div class="table-actions-row">
                                                <button type="button" wire:click="openHistoricoLivro({{ $r->livro_id }})" class="btn btn-sm btn-info">Histórico</button>
                                                @if(!$r->entregue_em && !$r->cidadao_entregou_em && $r->cidadao_id === auth()->id())
                                                    <button type="button" wire:click="marcarEntregaNaBiblioteca({{ $r->id }})" class="btn btn-sm btn-success">Indicar devolução</button>
                                                @endif
                                                @if($r->entregue_em && $r->cidadao_id === auth()->id())
                                                    @php
                                                        $jaReview = \App\Models\Review::where('livro_id', $r->livro_id)->where('cidadao_id', auth()->id())->exists();
                                                    @endphp
                                                    @if(!$jaReview)
                                                        <button type="button" wire:click="openReviewModal({{ $r->livro_id }})" class="btn btn-sm btn-warning">⭐ Review</button>
                                                    @endif
                                                @endif
                                            </div>
                                            @if(auth()->user()?->isAdmin() && $r->cidadao_entregou_em && !$r->entregue_em)
                                                <div class="condition-picker">
                                                    <span class="condition-picker-label">Condição do livro</span>
                                                    <div class="table-actions-row">
                                                        <button type="button" wire:click="registarRelatorioDevolucao({{ $r->id }}, 'boas')" class="btn btn-xs btn-success">Boas</button>
                                                        <button type="button" wire:click="registarRelatorioDevolucao({{ $r->id }}, 'medianas')" class="btn btn-xs btn-warning">Medianas</button>
                                                        <button type="button" wire:click="registarRelatorioDevolucao({{ $r->id }}, 'mas')" class="btn btn-xs btn-error">Más</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()?->isAdmin() ? 9 : 8 }}">
                                        @if(auth()->user()?->isAdmin() && $adminVista === 'por_relatar')
                                            Nenhuma requisição por relatar.
                                        @else
                                            Sem requisições.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    @if($historicoModalOpen)
        <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeHistoricoModal()"></div>
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
            <div wire:key="requisicao-historico-modal" class="bg-base-200 relative p-6 rounded-lg w-full max-w-4xl pointer-events-auto shadow-xl border border-base-300 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-start gap-3 mb-4">
                    <div class="flex-1 text-center">
                        <h3 class="text-lg font-semibold tracking-wide">
                            @if(auth()->user()?->isAdmin())
                                Histórico do livro
                            @else
                                As tuas requisições deste livro
                            @endif
                        </h3>
                        <p class="text-sm opacity-80 mt-1 font-medium">{{ $historico_livro_nome }}</p>
                        @if(!auth()->user()?->isAdmin())
                            <p class="text-xs opacity-80 mt-1">{{ auth()->user()->name }} · {{ auth()->user()->email }}</p>
                            <p class="text-xs opacity-70 mt-1">Inclui requisições já concluídas e a ativa, se existir.</p>
                        @endif
                    </div>
                    <button type="button" wire:click="closeHistoricoModal()" class="btn btn-sm btn-circle shrink-0">✕</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                @if(auth()->user()?->isAdmin())
                                    <th>Quem requisitou</th>
                                @endif
                                <th>Quando</th>
                                <th>Previsto</th>
                                <th>Devolução (cidadão)</th>
                                <th>Concluída</th>
                                <th>Condição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historico_requisicoes as $h)
                                <tr>
                                    <td>#{{ $h['numero'] }}</td>
                                    @if(auth()->user()?->isAdmin())
                                        <td>
                                            {{ $h['cidadao_nome'] }}
                                            <div class="text-xs opacity-60">{{ $h['cidadao_email'] }}</div>
                                        </td>
                                    @endif
                                    <td>{{ $h['requisitado_em'] ?: '—' }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()?->isAdmin() ? 7 : 6 }}" class="text-center opacity-70 py-6">Sem registos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($isModalOpen)
        <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeModal()"></div>
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-base-200 p-6 rounded-lg w-full max-w-xl pointer-events-auto shadow-xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Nova Requisição</h3>
                    <button wire:click="closeModal()" class="btn btn-sm btn-circle">✕</button>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Livro disponível</span></label>
                    <select wire:model="livro_id" class="select select-bordered">
                        <option value="">Selecione…</option>
                        @foreach($livrosDisponiveis as $l)
                            <option value="{{ $l->id }}">{{ $l->nome }} ({{ $l->isbn }})</option>
                        @endforeach
                    </select>
                    @error('livro_id') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button wire:click="closeModal()" class="btn">Cancelar</button>
                    <button wire:click="criarRequisicao" class="btn btn-primary">Requisitar</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para criar review -->
    @if($reviewModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-base-200 p-6 rounded-lg w-full max-w-lg pointer-events-auto shadow-xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">⭐ Nova Review</h3>
                    <button wire:click="closeReviewModal()" class="btn btn-sm btn-circle">✕</button>
                </div>

                <form wire:submit.prevent="submeterReview">
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text">Classificação</span></label>
                        <div
                            x-data="{ hovered: 0, selected: {{ (int)($review_classificacao ?? 0) }} }"
                            x-on:mouseleave="hovered = 0"
                            class="flex gap-1 mt-1"
                        >
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    x-on:mouseenter="hovered = {{ $i }}"
                                    x-on:click="selected = {{ $i }}; $wire.set('review_classificacao', {{ $i }})"
                                    class="text-4xl leading-none transition-all duration-150 cursor-pointer focus:outline-none"
                                    :style="(hovered >= {{ $i }} || selected >= {{ $i }}) ? 'color:#d4af37; filter:drop-shadow(0 0 6px rgba(212,175,55,0.8)); transform:scale(1.2);' : 'color:rgba(212,175,55,0.22);'"
                                    title="{{ $i }} estrela{{ $i > 1 ? 's' : '' }}"
                                >★</button>
                            @endfor
                        </div>
                        @error('review_classificacao') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text">Comentário</span></label>
                        <textarea 
                            wire:model="review_comentario" 
                            class="textarea textarea-bordered h-24 w-full" 
                            placeholder="Escreve a tua review..."
                            required
                        ></textarea>
                        @error('review_comentario') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="closeReviewModal()" class="btn">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Submeter Review</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

