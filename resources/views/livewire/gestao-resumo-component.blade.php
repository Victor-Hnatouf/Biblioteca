<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight font-cinzel">
            ⚙️ Gestão do Scriptorium
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-base-200 rounded-lg border border-[#8b5a2b]/30 p-6 shadow-xl">
                <p class="text-base-content/80 text-lg leading-relaxed" style="font-family: 'Cormorant Garamond', serif;">
                    Visão rápida do reino literário: encomendas, acervo e cidadãos. Usa o botão ☰ Menu junto ao brasão para aceder às ferramentas de gestão.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="stat bg-base-200 border border-[#8b5a2b]/25 rounded-lg shadow">
                    <div class="stat-title font-cinzel text-[#8b5a2b]">No catálogo</div>
                    <div class="stat-value text-[#d4af37]">{{ $livrosNoCatalogo }}</div>
                    <div class="stat-desc">de {{ $totalLivros }} tomos registados</div>
                </div>
                <div class="stat bg-base-200 border border-[#8b5a2b]/25 rounded-lg shadow">
                    <div class="stat-title font-cinzel text-[#8b5a2b]">Vendidos</div>
                    <div class="stat-value text-[#d4af37]">{{ $livrosVendidos }}</div>
                    <div class="stat-desc">já não aparecem no catálogo</div>
                </div>
                <div class="stat bg-base-200 border border-[#8b5a2b]/25 rounded-lg shadow">
                    <div class="stat-title font-cinzel text-[#8b5a2b]">Requisições ativas</div>
                    <div class="stat-value text-[#d4af37]">{{ $requisicoesAtivas }}</div>
                    <div class="stat-desc">empréstimos em curso</div>
                </div>
                <div class="stat bg-base-200 border border-warning/40 rounded-lg shadow">
                    <div class="stat-title font-cinzel text-warning">Encomendas pendentes</div>
                    <div class="stat-value text-warning">{{ $encomendasPendentes }}</div>
                    <div class="stat-desc">
                        <a href="{{ route('admin.encomendas') }}" class="link link-hover text-[#d4af37]">Ver encomendas →</a>
                    </div>
                </div>
                <div class="stat bg-base-200 border border-success/30 rounded-lg shadow">
                    <div class="stat-title font-cinzel text-success">Encomendas pagas</div>
                    <div class="stat-value text-success">{{ $encomendasPagas }}</div>
                    <div class="stat-desc">
                        <a href="{{ route('admin.encomendas') }}" class="link link-hover text-[#d4af37]">Ver todas →</a>
                    </div>
                </div>
                <div class="stat bg-base-200 border border-[#8b5a2b]/25 rounded-lg shadow">
                    <div class="stat-title font-cinzel text-[#8b5a2b]">Cidadãos</div>
                    <div class="stat-value text-[#d4af37]">{{ $totalCidadaos }}</div>
                    <div class="stat-desc">contas registadas</div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('livros') }}" class="btn btn-primary btn-sm font-cinzel">📚 Acervo</a>
                <a href="{{ route('admin.encomendas') }}" class="btn btn-primary btn-sm font-cinzel">🛍️ Encomendas</a>
                <a href="{{ route('utilizadores') }}" class="btn btn-secondary btn-sm font-cinzel">👤 Utilizadores</a>
            </div>
        </div>
    </div>
</div>
