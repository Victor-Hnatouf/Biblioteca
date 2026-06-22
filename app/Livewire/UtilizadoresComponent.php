<?php
namespace App\Livewire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
class UtilizadoresComponent extends Component
{
    public $search = '';
    public $isModalOpen = false;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = '';
    public ?int $selectedUserId = null;
    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $users = User::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->get();
        $selectedUser = null;
        $selectedUserSessao = null;
        if ($this->selectedUserId) {
            $selectedUser = User::query()
                ->with(['requisicoes.livro'])
                ->find($this->selectedUserId);
            if ($selectedUser) {
                $lastActivity = DB::table('sessions')
                    ->where('user_id', $selectedUser->id)
                    ->max('last_activity');
                $threshold = now()->subMinutes(5)->getTimestamp();
                $selectedUserSessao = [
                    'online' => (bool) ($lastActivity && (int) $lastActivity >= $threshold),
                    'ultima_atividade' => $lastActivity
                        ? Carbon::createFromTimestamp((int) $lastActivity)
                        : null,
                ];
            } else {
                $this->selectedUserId = null;
            }
        }
        return view('livewire.utilizadores-component', [
            'users' => $users,
            'selectedUser' => $selectedUser,
            'selectedUserSessao' => $selectedUserSessao,
            'adminCount' => User::query()->where('role', User::ROLE_ADMIN)->count(),
        ])->layout('layouts.app');
    }
    public function openModal(): void
    {
        $this->reset(['name', 'email', 'password', 'role']);
        $this->role = \App\Models\User::ROLE_CIDADAO;
        $this->resetValidation();
        $this->isModalOpen = true;
    }
    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }
    public function createUser(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in([User::ROLE_ADMIN, User::ROLE_CIDADAO])],
        ]);
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);
        session()->flash('message', 'Utilizador criado com sucesso.');
        $this->closeModal();
    }
    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
    }
    public function atribuirAdmin(int $userId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $user = User::query()->findOrFail($userId);
        if ($user->isAdmin()) {
            session()->flash('error', 'Este utilizador já é administrador. Se pretendes retirar-lhe o atributo de admin, usa o botão «Remover admin» abaixo.');
            return;
        }
        $user->update(['role' => User::ROLE_ADMIN]);
        session()->flash('message', 'O utilizador passou a administrador.');
    }
    public function removerAdmin(int $userId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $user = User::query()->findOrFail($userId);
        if (! $user->isAdmin()) {
            session()->flash('error', 'Este utilizador não é administrador.');
            return;
        }
        $outrosAdmins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->where('id', '!=', $user->id)
            ->count();
        if ($outrosAdmins < 1) {
            session()->flash('error', 'Não é possível remover o último administrador do sistema.');
            return;
        }
        $user->update(['role' => User::ROLE_CIDADAO]);
        session()->flash('message', 'O utilizador deixou de ser administrador.');
    }
    public function eliminarConta(int $userId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        if ($userId === auth()->id()) {
            session()->flash('error', 'Não podes apagar a tua própria conta neste painel.');
            return;
        }
        $user = User::query()->findOrFail($userId);
        if ($user->isAdmin()) {
            $restantes = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->where('id', '!=', $user->id)
                ->count();
            if ($restantes < 1) {
                session()->flash('error', 'Não é possível apagar o último administrador.');
                return;
            }
        }
        $user->delete();
        if ($this->selectedUserId === $userId) {
            $this->selectedUserId = null;
        }
        session()->flash('message', 'Conta eliminada com sucesso.');
    }
}
