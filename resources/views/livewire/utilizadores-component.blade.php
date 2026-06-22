<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">👤 Utilizadores</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                <p class="text-sm opacity-80">Gere contas, administradores e consulta o estado de sessão de cada perfil.</p>
                <button type="button" wire:click="openModal" class="btn btn-primary">➕ Criar Utilizador</button>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success mb-4">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-error mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <input wire:model.live="search" type="text" placeholder="🔍 Procurar por nome ou email…" class="input input-bordered w-full mb-4" />

                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Role</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $u)
                                    <tr class="{{ (int) $selectedUserId === (int) $u->id ? 'active-row' : '' }}">
                                        <td>
                                            {{ $u->name }}
                                            <div class="text-xs opacity-60">{{ $u->email }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $u->isAdmin() ? 'badge-primary' : 'badge-ghost' }}">
                                                {{ $u->isAdmin() ? 'Admin' : 'Cidadão' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <button type="button" wire:click="selectUser({{ $u->id }})" class="btn btn-sm btn-accent">DETALHES</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:col-span-2">
                    @if($selectedUser)
                        @php
                            $eProprio = (int) $selectedUser->id === (int) auth()->id();
                        @endphp

                        <div class="profile-panel">
                            <div class="profile-header">
                                <div class="avatar shrink-0 mx-auto sm:mx-0">
                                    <div class="w-20 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                        <img src="{{ $selectedUser->profile_photo_url }}" alt="{{ $selectedUser->name }}" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0 profile-meta">
                                    <h3 class="text-xl font-semibold mb-1" style="color: var(--gold-light); font-family: 'Cinzel', serif;">{{ $selectedUser->name }}</h3>
                                    <div class="text-sm break-all mb-3" style="color: var(--parchment-dim);">{{ $selectedUser->email }}</div>
                                    <div class="mb-4">
                                        <span class="badge {{ $selectedUser->isAdmin() ? 'badge-primary' : 'badge-neutral' }}">
                                            {{ $selectedUser->isAdmin() ? 'Administrador' : 'Cidadão' }}
                                        </span>
                                    </div>
                                    <dl>
                                        <div>
                                            <dt>Conta criada</dt>
                                            <dd>{{ $selectedUser->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Email verificado</dt>
                                            <dd>{{ $selectedUser->email_verified_at ? $selectedUser->email_verified_at->format('d/m/Y H:i') : 'Não' }}</dd>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <dt>Presença (sessão)</dt>
                                            <dd class="flex flex-wrap items-center gap-2 mt-1">
                                                @if($selectedUserSessao && $selectedUserSessao['online'])
                                                    <span class="badge badge-success">Ativo (online)</span>
                                                @else
                                                    <span class="badge badge-neutral">Offline</span>
                                                @endif
                                                @if($selectedUserSessao && $selectedUserSessao['ultima_atividade'])
                                                    <span class="text-xs" style="color: var(--parchment-dim);">Última actividade: {{ $selectedUserSessao['ultima_atividade']->format('d/m/Y H:i') }}</span>
                                                @elseif($selectedUserSessao)
                                                    <span class="text-xs" style="color: var(--parchment-dim);">Sem sessão registada na base de dados.</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>

                                    <div class="profile-actions">
                                        @if($selectedUser->isAdmin())
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                                wire:click="removerAdmin({{ $selectedUser->id }})"
                                                wire:confirm="Tens a certeza de que queres retirar o atributo de administrador a {{ $selectedUser->name }}? Passará a cidadão."
                                                @if($adminCount <= 1) disabled title="É o último administrador" @endif
                                            >Remover admin</button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" wire:click="atribuirAdmin({{ $selectedUser->id }})">
                                                Tornar administrador
                                            </button>
                                        @endif

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-error btn-outline"
                                            wire:click="eliminarConta({{ $selectedUser->id }})"
                                            wire:confirm="Eliminar definitivamente a conta de {{ $selectedUser->name }}? Esta acção não pode ser anulada."
                                            @if($eProprio) disabled title="Não podes apagar a tua própria conta aqui" @endif
                                        >Apagar conta</button>
                                    </div>

                                    @if($eProprio && $selectedUser->isAdmin())
                                        <p class="text-xs mt-3" style="color: var(--parchment-dim);">Para alterar o teu próprio cargo de admin, outro administrador tem de efetuar a alteração.</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold mb-3" style="color: var(--gold); font-family: 'Cinzel', serif;">Histórico de Requisições</h3>
                                <div class="overflow-x-auto">
                                    <table class="table w-full">
                                        <thead>
                                            <tr>
                                                <th>Nº</th>
                                                <th>Livro</th>
                                                <th>Previsto</th>
                                                <th>Devolução (cidadão)</th>
                                                <th>Concluída</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($selectedUser->requisicoes->sortByDesc('id') as $r)
                                                <tr>
                                                    <td>#{{ $r->numero }}</td>
                                                    <td class="book-title">{{ $r->livro?->nome ?? '—' }}</td>
                                                    <td>{{ optional($r->previsto_entrega_em)->format('d/m/Y') }}</td>
                                                    <td>{{ $r->cidadao_entregou_em ? optional($r->cidadao_entregou_em)->format('d/m/Y H:i') : '—' }}</td>
                                                    <td>{{ $r->entregue_em ? optional($r->entregue_em)->format('d/m/Y') : '—' }}</td>
                                                    <td>
                                                        @if($r->entregue_em)
                                                            <span class="badge badge-success">Concluída</span>
                                                            @if($r->condicao_na_devolucao)
                                                                <div class="text-xs mt-1" style="color: var(--parchment-dim);">{{ \App\Models\Requisicao::labelCondicao($r->condicao_na_devolucao) }}</div>
                                                            @endif
                                                        @elseif($r->cidadao_entregou_em)
                                                            <span class="badge badge-warning">Por relatar</span>
                                                        @else
                                                            <span class="badge badge-neutral">Ativa</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6"><div class="empty-state">Sem requisições.</div></td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">Seleciona um utilizador na lista e clica em <strong style="color: var(--gold-light);">Detalhes</strong> para ver o perfil, estado de sessão e acções.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-[9998] modal-backdrop-solid" wire:click="closeModal"></div>
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-base-200 p-6 rounded-lg w-full max-w-lg pointer-events-auto shadow-xl border border-base-300" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Criar Utilizador</h3>
                    <button type="button" wire:click="closeModal" class="btn btn-sm btn-circle">✕</button>
                </div>

                <form wire:submit.prevent="createUser" class="space-y-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nome</span></label>
                        <input type="text" wire:model="name" class="input input-bordered" autocomplete="name" />
                        @error('name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Email</span></label>
                        <input type="email" wire:model="email" class="input input-bordered" autocomplete="off" />
                        @error('email') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Password</span></label>
                        <input type="password" wire:model="password" class="input input-bordered" autocomplete="new-password" />
                        @error('password') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Cargo / Permissão</span></label>
                        <select wire:model="role" class="select select-bordered w-full">
                            <option value="admin">Admin</option>
                            <option value="cidadao">User</option>
                        </select>
                        @error('role') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
