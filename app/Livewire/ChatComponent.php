<?php
namespace App\Livewire;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
class ChatComponent extends Component
{
    use WithFileUploads;
    public $activeRoomId = null;
    public $newMessage = '';
    public $newAttachment = null;
    public $isCreateRoomModalOpen = false;
    public $newRoomName = '';
    public $isAdminOnly = false;
    public $isCreateGroupModalOpen = false;
    public $newGroupName = '';
    public $selectedGroupMembers = [];
    public $isGroupAdminOnly = false;
    public $isSettingsModalOpen = false;
    public $chatNickname = '';
    public $userStatus = '';
    public $chatPhoto = null;
    public $searchQuery = '';
    protected $rules = [
        'newMessage' => 'required_without:newAttachment|string|max:1000',
        'newAttachment' => 'nullable|file|max:5120',
    ];
    public function mount()
    {
        $user = Auth::user();
        $this->chatNickname = $user->chat_nickname ?? '';
        $this->userStatus = $user->estado ?? 'Disponível';
        $defaultRoom = Room::channels()
            ->when(!$user->isAdmin(), function ($q) {
                $q->where('is_admin_only', false);
            })->orderBy('id')->first();
        if ($defaultRoom) {
            $this->activeRoomId = $defaultRoom->id;
        } else {
            $room = Room::create([
                'nome' => 'All Talk',
                'is_dm' => false,
                'is_group' => false,
            ]);
            $this->activeRoomId = $room->id;
        }
        $this->ensureUserInRoom($this->activeRoomId);
    }
    private function userCanAccessRoom(?Room $room): bool
    {
        if (! $room) {
            return false;
        }
        if ($room->is_admin_only && ! Auth::user()->isAdmin()) {
            return false;
        }
        if ($room->is_group || $room->is_dm) {
            return $room->users()->where('user_id', Auth::id())->exists();
        }
        return true;
    }
    public function selectRoom($roomId)
    {
        $room = Room::find($roomId);
        if (! $this->userCanAccessRoom($room)) {
            return;
        }
        $this->activeRoomId = $roomId;
        $this->newMessage = '';
        $this->newAttachment = null;
        $this->searchQuery = '';
        if ($room->isChannel()) {
            $this->ensureUserInRoom($roomId);
        }
    }
    private function ensureUserInRoom($roomId)
    {
        $room = Room::find($roomId);
        if ($room && ! $room->users()->where('user_id', Auth::id())->exists()) {
            $room->users()->attach(Auth::id());
        }
    }
    public function startDM($userId)
    {
        if ($userId == Auth::id()) {
            return;
        }
        $dmRoom = Room::where('is_dm', true)
            ->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
        if ($dmRoom) {
            $this->selectRoom($dmRoom->id);
        } else {
            $targetUser = User::find($userId);
            $newRoom = Room::create([
                'nome' => 'DM: ' . Auth::user()->chat_display_name . ' & ' . $targetUser->chat_display_name,
                'is_dm' => true,
                'is_group' => false,
            ]);
            $newRoom->users()->attach([Auth::id(), $userId]);
            $this->selectRoom($newRoom->id);
        }
    }
    public function sendMessage()
    {
        if (empty(trim($this->newMessage)) && ! $this->newAttachment) {
            return;
        }
        $room = Room::find($this->activeRoomId);
        if (! $this->userCanAccessRoom($room)) {
            return;
        }
        $attachmentPath = null;
        if ($this->newAttachment) {
            $attachmentPath = $this->newAttachment->store('chat-attachments', 'public');
        }
        Message::create([
            'room_id' => $this->activeRoomId,
            'user_id' => Auth::id(),
            'conteudo' => $this->newMessage,
            'attachment_path' => $attachmentPath,
        ]);
        $this->newMessage = '';
        $this->newAttachment = null;
        $this->dispatch('message-sent');
    }
    public function openCreateRoomModal()
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $this->newRoomName = '';
        $this->isAdminOnly = false;
        $this->isCreateRoomModalOpen = true;
    }
    public function closeCreateRoomModal()
    {
        $this->isCreateRoomModalOpen = false;
    }
    public function createRoom()
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $this->validate([
            'newRoomName' => 'required|string|min:2|max:50|unique:rooms,nome',
            'isAdminOnly' => 'boolean',
        ]);
        $room = Room::create([
            'nome' => $this->newRoomName,
            'is_dm' => false,
            'is_group' => false,
            'is_admin_only' => $this->isAdminOnly,
            'created_by' => Auth::id(),
        ]);
        $room->users()->attach(Auth::id());
        $this->isCreateRoomModalOpen = false;
        $this->selectRoom($room->id);
        session()->flash('message', 'Sala criada com sucesso!');
    }
    public function openCreateGroupModal()
    {
        $this->newGroupName = '';
        $this->selectedGroupMembers = [];
        $this->isGroupAdminOnly = false;
        $this->isCreateGroupModalOpen = true;
    }
    public function closeCreateGroupModal()
    {
        $this->isCreateGroupModalOpen = false;
    }
    public function updatedIsGroupAdminOnly($value)
    {
        if ($value) {
            $this->selectedGroupMembers = User::whereIn('id', $this->selectedGroupMembers)
                ->where('role', User::ROLE_ADMIN)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();
        }
    }
    public function createGroup()
    {
        if ($this->isGroupAdminOnly) {
            abort_unless(Auth::user()->isAdmin(), 403);
        }
        $this->validate([
            'newGroupName' => 'required|string|min:2|max:50|unique:rooms,nome',
            'selectedGroupMembers' => 'required|array|min:1',
            'selectedGroupMembers.*' => 'exists:users,id',
            'isGroupAdminOnly' => 'boolean',
        ], [
            'selectedGroupMembers.required' => 'Seleciona pelo menos um membro para o grupo.',
            'selectedGroupMembers.min' => 'Seleciona pelo menos um membro para o grupo.',
        ]);
        $memberIds = array_map('intval', $this->selectedGroupMembers);
        if ($this->isGroupAdminOnly) {
            $hasNonAdmin = User::whereIn('id', $memberIds)
                ->where('role', '!=', User::ROLE_ADMIN)
                ->exists();
            if ($hasNonAdmin) {
                $this->addError('selectedGroupMembers', 'Em grupos exclusivos para admins, só podes adicionar administradores.');
                return;
            }
        }
        $memberIds = array_values(array_unique(array_merge($memberIds, [Auth::id()])));
        $room = Room::create([
            'nome' => $this->newGroupName,
            'is_dm' => false,
            'is_group' => true,
            'is_admin_only' => $this->isGroupAdminOnly,
            'created_by' => Auth::id(),
        ]);
        $room->users()->attach($memberIds);
        $this->isCreateGroupModalOpen = false;
        $this->selectRoom($room->id);
        session()->flash('message', 'Grupo criado com sucesso!');
    }
    public function deleteRoom($roomId)
    {
        $room = Room::find($roomId);
        if (! $room || $room->is_dm) {
            return;
        }
        if ($room->isChannel()) {
            abort_unless(Auth::user()->isAdmin(), 403);
        } elseif ($room->isGroup()) {
            $canDelete = Auth::user()->isAdmin() || (int) $room->created_by === (int) Auth::id();
            abort_unless($canDelete, 403);
        }
        foreach ($room->messages as $msg) {
            if ($msg->attachment_path) {
                Storage::disk('public')->delete($msg->attachment_path);
            }
        }
        $isGroup = $room->is_group;
        $room->messages()->delete();
        $room->users()->detach();
        $room->delete();
        if ($this->activeRoomId == $roomId) {
            $this->mount();
        }
        session()->flash('message', $isGroup ? 'Grupo eliminado com sucesso!' : 'Sala eliminada com sucesso!');
    }
    public function openSettingsModal()
    {
        $user = Auth::user();
        $this->chatNickname = $user->chat_nickname ?? '';
        $this->userStatus = $user->estado ?? 'Disponível';
        $this->chatPhoto = null;
        $this->isSettingsModalOpen = true;
    }
    public function closeSettingsModal()
    {
        $this->isSettingsModalOpen = false;
        $this->chatPhoto = null;
    }
    public function saveSettings()
    {
        $user = Auth::user();
        $this->validate([
            'chatNickname' => 'nullable|string|max:50',
            'userStatus' => 'nullable|string|max:100',
            'chatPhoto' => 'nullable|image|max:2048',
        ]);
        $updateData = [
            'chat_nickname' => $this->chatNickname ?: null,
            'estado' => $this->userStatus ?: 'Disponível',
        ];
        if ($this->chatPhoto) {
            if ($user->chat_photo_path) {
                Storage::disk('public')->delete($user->chat_photo_path);
            }
            $updateData['chat_photo_path'] = $this->chatPhoto->store('chat-photos', 'public');
        }
        $user->update($updateData);
        $this->chatPhoto = null;
        $this->isSettingsModalOpen = false;
        session()->flash('message', 'Perfil de chat atualizado com sucesso!');
    }
    public function render()
    {
        $rooms = Room::channels()
            ->when(!Auth::user()->isAdmin(), function ($q) {
                $q->where('is_admin_only', false);
            })
            ->orderBy('nome')
            ->get();
        $groups = Room::groups()
            ->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when(! Auth::user()->isAdmin(), function ($q) {
                $q->where('is_admin_only', false);
            })
            ->withCount('users')
            ->orderBy('nome')
            ->get();
        $groupMemberCandidates = User::where('id', '!=', Auth::id())
            ->when($this->isGroupAdminOnly, function ($q) {
                $q->where('role', User::ROLE_ADMIN);
            })
            ->orderBy('name')
            ->get();
        $dmRooms = Room::where('is_dm', true)
            ->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['users' => function ($q) {
                $q->where('users.id', '!=', Auth::id());
            }])
            ->get();
        $teamMembers = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        $activeRoom = Room::with('users')->find($this->activeRoomId);
        $messagesQuery = Message::where('room_id', $this->activeRoomId)
            ->with('user')
            ->orderBy('created_at', 'asc');
        if (! empty($this->searchQuery)) {
            $messagesQuery->where(function ($q) {
                $q->where('conteudo', 'like', '%' . $this->searchQuery . '%')
                    ->orWhereHas('user', function ($qu) {
                        $qu->where('name', 'like', '%' . $this->searchQuery . '%')
                            ->orWhere('chat_nickname', 'like', '%' . $this->searchQuery . '%');
                    });
            });
        }
        $messages = $messagesQuery->get();
        $groupedMessages = $messages->groupBy(function ($msg) {
            return $msg->created_at->format('Y-m-d');
        });
        return view('livewire.chat-component', [
            'rooms' => $rooms,
            'groups' => $groups,
            'groupMemberCandidates' => $groupMemberCandidates,
            'dmRooms' => $dmRooms,
            'teamMembers' => $teamMembers,
            'activeRoom' => $activeRoom,
            'groupedMessages' => $groupedMessages,
        ])->layout('layouts.app');
    }
}
