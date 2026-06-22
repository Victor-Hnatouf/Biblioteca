<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">💬 Chat da Biblioteca</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        @if (session()->has('message'))
            <div class="alert alert-success mb-2 shadow-lg rounded-xl border border-wood-dark">
                <span>{{ session('message') }}</span>
            </div>
        @endif
        <div class="chat-panel shadow-2xl rounded-xl border border-wood-dark overflow-hidden flex flex-row" wire:poll.3s style="height: calc(100vh - 16rem); min-height: 500px;">
            
            <aside class="chat-sidebar w-72 lg:w-80 border-r border-wood-dark flex flex-col flex-shrink-0 bg-ink z-20">
                <div class="p-4 border-b border-wood-dark flex-shrink-0">
                    <h3 class="font-bold text-sm uppercase tracking-wider opacity-80">Salas, Grupos &amp; DMs</h3>
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-4">
                    <div>
                        <p class="px-3 py-1 text-xs font-bold uppercase tracking-wider opacity-50">Mensagens directas</p>
                        <ul class="menu menu-sm rounded-box gap-1">
                            @foreach($teamMembers as $member)
                                @php
                                    $isDmActive = $activeRoom && $activeRoom->is_dm && $activeRoom->users->contains('id', $member->id);
                                @endphp
                                <li>
                                    <button type="button" wire:click="startDM({{ $member->id }})" class="{{ $isDmActive ? 'active' : '' }}">
                                        <div class="avatar online">
                                            <div class="w-8 rounded-full">
                                                <img src="{{ $member->chat_avatar_url }}" alt="{{ $member->chat_display_name }}" />
                                            </div>
                                        </div>
                                        <span class="truncate">{{ $member->chat_display_name }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <div class="flex items-center justify-between px-3 py-1">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-50">Grupos</p>
                            <button type="button" wire:click="openCreateGroupModal" class="btn btn-ghost btn-xs btn-square" title="Criar grupo">+</button>
                        </div>
                        <ul class="menu menu-sm rounded-box gap-1">
                            @forelse($groups as $group)
                                <li class="flex flex-row items-center gap-0">
                                    <button type="button" wire:click="selectRoom({{ $group->id }})" class="flex-1 {{ (int) $activeRoomId === (int) $group->id ? 'active' : '' }}">
                                        @if($group->is_admin_only)
                                            <svg class="w-3 h-3 text-error mr-1 inline opacity-70" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 mr-1 inline opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @endif
                                        <span class="truncate">{{ $group->nome }}</span>
                                        <span class="text-xs opacity-40 ml-1">({{ $group->users_count }})</span>
                                    </button>
                                    @if(auth()->user()->isAdmin() || (int) $group->created_by === (int) auth()->id())
                                        <button type="button" wire:click="deleteRoom({{ $group->id }})" class="btn btn-ghost btn-xs btn-square text-error opacity-40 hover:opacity-100 hover:bg-error hover:text-white transition-all ml-1" title="Eliminar grupo" onclick="confirm('Tens a certeza que queres eliminar este grupo?') || event.stopImmediatePropagation()">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    @endif
                                </li>
                            @empty
                                <li class="px-3 py-2 text-xs opacity-40 italic">Ainda não pertences a nenhum grupo.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div>
                        <div class="flex items-center justify-between px-3 py-1">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-50">Canais</p>
                            @if(auth()->user()->isAdmin())
                                <button type="button" wire:click="openCreateRoomModal" class="btn btn-ghost btn-xs btn-square" title="Criar sala">+</button>
                            @endif
                        </div>
                        <ul class="menu menu-sm rounded-box gap-1">
                            @foreach($rooms as $room)
                                <li class="flex flex-row items-center gap-0">
                                    <button type="button" wire:click="selectRoom({{ $room->id }})" class="flex-1 {{ (int) $activeRoomId === (int) $room->id ? 'active' : '' }}">
                                        @if($room->is_admin_only)
                                            <svg class="w-3 h-3 text-error mr-1 inline opacity-70" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <span class="opacity-60">#</span>
                                        @endif
                                        <span class="truncate">{{ $room->nome }}</span>
                                    </button>
                                    @if(auth()->user()->isAdmin())
                                        <button type="button" wire:click="deleteRoom({{ $room->id }})" class="btn btn-ghost btn-xs btn-square text-error opacity-40 hover:opacity-100 hover:bg-error hover:text-white transition-all ml-1" title="Eliminar sala" onclick="confirm('Tens a certeza que queres eliminar esta sala?') || event.stopImmediatePropagation()">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="p-3 border-t border-wood-dark flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="avatar online">
                            <div class="w-10 rounded-full">
                                <img src="{{ auth()->user()->chat_avatar_url }}" alt="{{ auth()->user()->chat_display_name }}" />
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold truncate">{{ auth()->user()->chat_display_name }}</p>
                            <p class="text-xs opacity-60 truncate">{{ auth()->user()->estado ?? 'Disponível' }}</p>
                        </div>
                        <button type="button" wire:click="openSettingsModal" class="btn btn-ghost btn-sm btn-square shrink-0" title="Configurações">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                </div>
            </aside>

            <div class="flex-1 flex flex-col min-w-0 relative z-10 bg-ink-dark">
                <div class="chat-panel-header navbar min-h-0 px-3 sm:px-4 py-3 border-b border-wood-dark gap-2 shadow-sm flex-shrink-0 bg-ink-dark">
                    <div class="flex-1 flex items-center gap-3 min-w-0">
                        <div class="avatar">
                            <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1">
                                @if($activeRoom && $activeRoom->isGroup())
                                    <img src="{{ $activeRoom->avatar_url }}" alt="{{ $activeRoom->nome }}" />
                                @else
                                    <img src="{{ asset('images/logo.jpg') }}" alt="Biblioteca" />
                                @endif
                            </div>
                        </div>
                        <div class="min-w-0">
                            <p class="text-base font-bold text-gold truncate font-cinzel">
                                @if($activeRoom)
                                    @if($activeRoom->is_dm)
                                        {{ $activeRoom->users->firstWhere('id', '!=', auth()->id())?->chat_display_name ?? 'Conversa Privada' }}
                                    @elseif($activeRoom->isGroup())
                                        {{ $activeRoom->nome }}
                                    @else
                                        {{ $activeRoom->nome }}
                                    @endif
                                @else
                                    Sem sala activa
                                @endif
                            </p>
                            <p class="text-xs text-parchment-dim hidden sm:block">
                                @if($activeRoom && $activeRoom->isGroup())
                                    Grupo · {{ $activeRoom->users->count() }} membros
                                    @if($activeRoom->is_admin_only)
                                        · <span class="text-error opacity-80">Apenas admins</span>
                                    @endif
                                @elseif($activeRoom && $activeRoom->is_dm)
                                    Mensagem directa
                                @else
                                    Canal público
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex-none flex items-center gap-2">
                        <div class="form-control">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 opacity-50">
                                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" wire:model.live="searchQuery" placeholder="Pesquisar mensagens..." class="input input-bordered input-sm w-36 sm:w-56 pl-9 bg-ink-light border-gold-dim focus:border-gold focus:ring focus:ring-gold-dim focus:ring-opacity-50 transition-all rounded-full" />
                            </div>
                        </div>
                        <button type="button" wire:click="openSettingsModal" class="btn btn-ghost btn-sm btn-square hover:bg-wood hover:text-gold transition-colors" title="Configurações do chat">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                </div>

                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" style="min-height: 0;">
                    @forelse($groupedMessages as $date => $dayMessages)
                        <div class="divider divider-neutral text-xs font-cinzel text-gold-dim my-6 opacity-80">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('d \d\e F \d\e Y') }}
                        </div>

                        @foreach($dayMessages as $msg)
                            @if($msg->user_id === auth()->id())
                                <div class="chat chat-end mb-2">
                                    <div class="chat-header text-xs text-parchment-dim mb-1">
                                        {{ $msg->user->chat_display_name }}
                                        <time class="opacity-50 ml-1">{{ $msg->created_at->format('H:i') }}</time>
                                    </div>
                                    <div class="chat-image avatar">
                                        <div class="w-10 rounded-full border border-wood shadow-md">
                                            <img src="{{ $msg->user->chat_avatar_url }}" alt="{{ $msg->user->chat_display_name }}" />
                                        </div>
                                    </div>
                                    <div class="chat-bubble chat-bubble-primary text-parchment shadow-lg">
                                        <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->conteudo }}</p>
                                        @if($msg->attachment_path)
                                            <div class="mt-2 pt-2 border-t border-gold-dim border-opacity-30">
                                                <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank" class="flex items-center gap-1 text-sm text-gold-light hover:text-gold transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    {{ basename($msg->attachment_path) }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="chat chat-start mb-2">
                                    <div class="chat-image avatar">
                                        <div class="w-10 rounded-full border border-gold-dim shadow-md">
                                            <img src="{{ $msg->user->chat_avatar_url }}" alt="{{ $msg->user->chat_display_name }}" />
                                        </div>
                                    </div>
                                    <div class="chat-header text-xs text-parchment-dim mb-1">
                                        {{ $msg->user->chat_display_name }}
                                        <time class="opacity-50 ml-1">{{ $msg->created_at->format('H:i') }}</time>
                                    </div>
                                    <div class="chat-bubble bg-ink-light border border-wood text-parchment shadow-lg">
                                        <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->conteudo }}</p>
                                        @if($msg->attachment_path)
                                            <div class="mt-2 pt-2 border-t border-wood-dark">
                                                <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank" class="flex items-center gap-1 text-sm text-gold hover:text-gold-light transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    {{ basename($msg->attachment_path) }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center p-8 opacity-60">
                            <div class="w-16 h-16 rounded-full bg-ink border border-gold-dim flex items-center justify-center mb-4 shadow-[0_0_15px_rgba(212,175,55,0.1)]">
                                <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <h3 class="font-cinzel text-lg text-gold mb-1">O silêncio reina nesta sala</h3>
                            <p class="text-sm font-cormorant">Que a tua voz seja a primeira a ecoar nestas paredes.</p>
                        </div>
                    @endforelse
                </div>

                <div class="chat-panel-input p-3 sm:p-4 flex-shrink-0">
                    <form wire:submit.prevent="sendMessage" class="chat-compose-form max-w-4xl mx-auto relative w-full">
                        @if($newAttachment)
                            <div class="chat-compose-attachment">
                                <span class="text-gold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg></span>
                                <span class="text-parchment-dim font-medium truncate">{{ Str::limit($newAttachment->getClientOriginalName(), 28) }}</span>
                                <button type="button" wire:click="$set('newAttachment', null)" class="chat-compose-attachment-remove" title="Remover anexo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif

                        <div class="chat-compose-bar">
                            <label class="chat-compose-action" title="Anexar ficheiro">
                                <input type="file" wire:model="newAttachment" class="hidden" />
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            </label>

                            <input
                                type="text"
                                wire:model.live="newMessage"
                                placeholder="Escreve a tua mensagem..."
                                class="chat-compose-input"
                                autocomplete="off"
                            />

                            <button type="submit" class="chat-compose-send" title="Enviar mensagem">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($isCreateGroupModalOpen)
        <dialog class="modal modal-open">
            <div class="modal-box medieval-modal-container max-w-lg">
                <form method="dialog">
                    <button type="button" wire:click="closeCreateGroupModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="font-bold text-lg mb-1">Criar Novo Grupo</h3>
                <p class="text-sm opacity-70 mb-4">Convida membros para uma conversa privada em grupo.</p>

                <form wire:submit.prevent="createGroup" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nome do Grupo</span></label>
                        <input type="text" wire:model="newGroupName" placeholder="Ex: Equipa de Design, Projecto X..." class="input input-bordered w-full" autocomplete="off" />
                        @error('newGroupName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div class="form-control">
                            <label class="cursor-pointer label justify-start gap-3">
                                <input type="checkbox" wire:model.live="isGroupAdminOnly" class="checkbox checkbox-error" />
                                <span class="label-text font-bold text-error">Apenas para Admins (grupo exclusivo de administradores)</span>
                            </label>
                        </div>
                    @endif

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Membros</span>
                            <span class="label-text-alt opacity-60">{{ count($selectedGroupMembers) }} selecionado(s)</span>
                        </label>
                        <div class="max-h-48 overflow-y-auto border border-wood-dark rounded-lg p-2 space-y-1 bg-ink-light">
                            @forelse($groupMemberCandidates as $candidate)
                                <label class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-wood/20 cursor-pointer">
                                    <input type="checkbox" wire:model="selectedGroupMembers" value="{{ $candidate->id }}" class="checkbox checkbox-primary checkbox-xs border-wood" />
                                    <div class="avatar">
                                        <div class="w-7 rounded-full">
                                            <img src="{{ $candidate->chat_avatar_url }}" alt="{{ $candidate->chat_display_name }}" />
                                        </div>
                                    </div>
                                    <span class="text-sm truncate">{{ $candidate->chat_display_name }}</span>
                                    @if($candidate->isAdmin())
                                        <span class="badge badge-xs badge-error ml-auto">Admin</span>
                                    @endif
                                </label>
                            @empty
                                <p class="text-sm opacity-50 italic px-2 py-3">
                                    @if($isGroupAdminOnly)
                                        Não existem outros administradores disponíveis.
                                    @else
                                        Não existem outros utilizadores disponíveis.
                                    @endif
                                </p>
                            @endforelse
                        </div>
                        @error('selectedGroupMembers') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeCreateGroupModal" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Grupo</button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button type="button" wire:click="closeCreateGroupModal">fechar</button>
            </form>
        </dialog>
    @endif

    @if($isCreateRoomModalOpen)
        <dialog class="modal modal-open">
            <div class="modal-box medieval-modal-container">
                <form method="dialog">
                    <button type="button" wire:click="closeCreateRoomModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="font-bold text-lg mb-4">Criar Nova Sala</h3>
                <form wire:submit.prevent="createRoom" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nome da Sala</span></label>
                        <input type="text" wire:model="newRoomName" placeholder="Ex: Marketing, Viagens..." class="input input-bordered w-full" autocomplete="off" />
                        @error('newRoomName') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="cursor-pointer label justify-start gap-3">
                            <input type="checkbox" wire:model="isAdminOnly" class="checkbox checkbox-error" />
                            <span class="label-text font-bold text-error">Apenas para Admins (Cidadãos não terão acesso)</span>
                        </label>
                    </div>
                    <div class="modal-action">
                        <button type="button" wire:click="closeCreateRoomModal" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Sala</button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button type="button" wire:click="closeCreateRoomModal">fechar</button>
            </form>
        </dialog>
    @endif

    @if($isSettingsModalOpen)
        <dialog class="modal modal-open">
            <div class="modal-box medieval-modal-container">
                <form method="dialog">
                    <button type="button" wire:click="closeSettingsModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="font-bold text-lg mb-1">Configurações do Chat</h3>
                <p class="text-sm opacity-70 mb-4">Apenas para o chat — não altera a tua conta da biblioteca.</p>

                <form wire:submit.prevent="saveSettings" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Foto de perfil (chat)</span></label>
                        <div class="flex items-center gap-4">
                            <div class="avatar">
                                <div class="w-16 rounded-full ring ring-primary">
                                    @if($chatPhoto)
                                        <img src="{{ $chatPhoto->temporaryUrl() }}" alt="Pré-visualização" />
                                    @else
                                        <img src="{{ auth()->user()->chat_avatar_url }}" alt="{{ auth()->user()->chat_display_name }}" />
                                    @endif
                                </div>
                            </div>
                            <input type="file" wire:model="chatPhoto" accept="image/*" class="file-input file-input-bordered file-input-sm w-full max-w-xs" />
                        </div>
                        @error('chatPhoto') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Nickname (chat)</span></label>
                        <input type="text" wire:model="chatNickname" placeholder="{{ auth()->user()->name }}" class="input input-bordered w-full" autocomplete="off" />
                        @error('chatNickname') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Estado</span></label>
                        <input type="text" wire:model="userStatus" placeholder="Disponível" class="input input-bordered w-full" autocomplete="off" />
                        @error('userStatus') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeSettingsModal" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button type="button" wire:click="closeSettingsModal">fechar</button>
            </form>
        </dialog>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            const scrollToBottom = () => {
                const chatBox = document.getElementById('chat-messages');
                if (chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            };

            scrollToBottom();

            Livewire.on('message-sent', () => {
                setTimeout(scrollToBottom, 50);
            });
        });
    </script>
</div>
