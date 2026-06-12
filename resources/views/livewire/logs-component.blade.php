<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">📋 Logs de Auditoria</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[100rem] mx-auto sm:px-6 lg:px-8">

            
            <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                <p class="text-sm opacity-80">Registo completo de todas as acções efectuadas na aplicação.</p>
                @if($filtroModulo !== '' || $filtroUtilizador !== '' || $filtroEvento !== '' || $filtroDataInicio !== '' || $filtroDataFim !== '' || $filtroIp !== '')
                    <button type="button" wire:click="limparFiltros" class="btn btn-sm btn-outline btn-warning">
                        ✕ Limpar filtros
                    </button>
                @endif
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success mb-4">{{ session('message') }}</div>
            @endif

            
            <div class="bg-base-200 shadow-xl sm:rounded-lg p-5 mb-6 border border-base-300">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs font-semibold uppercase tracking-wide opacity-70">Módulo</span></label>
                        <select wire:model.live="filtroModulo" class="select select-bordered select-sm">
                            <option value="">Todos</option>
                            @foreach($modulos as $mod)
                                <option value="{{ $mod }}">{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs font-semibold uppercase tracking-wide opacity-70">Evento</span></label>
                        <select wire:model.live="filtroEvento" class="select select-bordered select-sm">
                            <option value="">Todos</option>
                            <option value="criado">✅ Criado</option>
                            <option value="atualizado">✏️ Atualizado</option>
                            <option value="eliminado">🗑️ Eliminado</option>
                        </select>
                    </div>

                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs font-semibold uppercase tracking-wide opacity-70">Utilizador</span></label>
                        <input wire:model.live.debounce.400ms="filtroUtilizador"
                               type="text"
                               placeholder="Nome ou email…"
                               class="input input-bordered input-sm" />
                    </div>

                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs font-semibold uppercase tracking-wide opacity-70">IP</span></label>
                        <input wire:model.live.debounce.400ms="filtroIp"
                               type="text"
                               placeholder="Ex: 192.168…"
                               class="input input-bordered input-sm" />
                    </div>

                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs font-semibold uppercase tracking-wide opacity-70">Data início</span></label>
                        <input wire:model.live="filtroDataInicio"
                               type="date"
                               class="input input-bordered input-sm" />
                    </div>

                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs font-semibold uppercase tracking-wide opacity-70">Data fim</span></label>
                        <input wire:model.live="filtroDataFim"
                               type="date"
                               class="input input-bordered input-sm" />
                    </div>
                </div>
            </div>

            
            <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg border border-base-300">
                <div class="overflow-x-auto">
                    <table class="table w-full text-sm">
                        <thead>
                            <tr style="font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.06em; color: #d4af37; border-bottom: 1px solid rgba(212,175,55,0.3);">
                                <th class="whitespace-nowrap">Data / Hora</th>
                                <th class="whitespace-nowrap">Utilizador</th>
                                <th class="whitespace-nowrap">Módulo</th>
                                <th class="whitespace-nowrap">ID Objeto</th>
                                <th class="whitespace-nowrap">Evento</th>
                                <th>Alterações</th>
                                <th class="whitespace-nowrap">IP</th>
                                <th>Browser</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr class="hover:bg-base-300/40 transition-colors border-b border-base-300/40">
                                    
                                    <td class="whitespace-nowrap">
                                        <div class="font-mono text-xs">
                                            <div style="color: var(--gold-light, #e2c96a);">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="opacity-60">{{ $log->created_at->format('H:i:s') }}</div>
                                        </div>
                                    </td>

                                    
                                    <td class="max-w-[10rem]">
                                        @if($log->user_nome)
                                            <div class="font-medium truncate text-xs">{{ $log->user_nome }}</div>
                                            <div class="text-xs opacity-50 truncate">{{ $log->user_email }}</div>
                                        @else
                                            <span class="text-xs opacity-40 italic">Sistema</span>
                                        @endif
                                    </td>

                                    
                                    <td>
                                        <span class="badge badge-sm badge-ghost font-mono text-[10px] tracking-wider">{{ $log->modulo }}</span>
                                    </td>

                                    
                                    <td class="text-center font-mono text-xs opacity-70">
                                        {{ $log->objeto_id ?? '—' }}
                                    </td>

                                    
                                    <td class="whitespace-nowrap">
                                        @if($log->evento === 'criado')
                                            <span class="badge badge-success badge-sm gap-1">✅ Criado</span>
                                        @elseif($log->evento === 'atualizado')
                                            <span class="badge badge-warning badge-sm gap-1">✏️ Atualizado</span>
                                        @elseif($log->evento === 'eliminado')
                                            <span class="badge badge-error badge-sm gap-1">🗑️ Eliminado</span>
                                        @else
                                            <span class="badge badge-ghost badge-sm">{{ $log->evento }}</span>
                                        @endif
                                    </td>

                                    
                                    <td class="max-w-[18rem]">
                                        @if($log->alteracoes && count($log->alteracoes) > 0)
                                            <details class="cursor-pointer">
                                                <summary class="text-xs opacity-70 hover:opacity-100 transition-opacity select-none">
                                                    {{ count($log->alteracoes) }} campo(s) alterado(s)
                                                </summary>
                                                <div class="mt-2 space-y-1">
                                                    @foreach($log->alteracoes as $campo => $vals)
                                                        <div class="text-[11px] bg-base-300/60 rounded p-1.5">
                                                            <span class="font-semibold font-mono" style="color: #d4af37;">{{ $campo }}</span>
                                                            <div class="flex gap-2 mt-0.5 flex-wrap">
                                                                <span class="line-through opacity-50 break-all">{{ is_array($vals['antes']) ? json_encode($vals['antes']) : ($vals['antes'] ?? '∅') }}</span>
                                                                <span class="opacity-30">→</span>
                                                                <span class="break-all" style="color: #86efac;">{{ is_array($vals['depois']) ? json_encode($vals['depois']) : ($vals['depois'] ?? '∅') }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </details>
                                        @else
                                            <span class="text-xs opacity-30">—</span>
                                        @endif
                                    </td>

                                    
                                    <td class="font-mono text-xs opacity-70 whitespace-nowrap">
                                        {{ $log->ip ?? '—' }}
                                    </td>

                                    
                                    <td class="max-w-[12rem]">
                                        @if($log->user_agent)
                                            <span class="text-[11px] opacity-60 break-all leading-tight block" title="{{ $log->user_agent }}">
                                                {{ Str::limit($log->user_agent, 60) }}
                                            </span>
                                        @else
                                            <span class="text-xs opacity-30">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center py-16 opacity-50">
                                            <div class="text-4xl mb-3">📋</div>
                                            <p>Nenhum registo encontrado com os filtros actuais.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                
                @if($logs->hasPages() || $logs->total() > 0)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-base-300/50">
                        <div class="text-xs opacity-60">
                            A mostrar {{ $logs->firstItem() }}–{{ $logs->lastItem() }} de {{ number_format($logs->total()) }} registo(s)
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
