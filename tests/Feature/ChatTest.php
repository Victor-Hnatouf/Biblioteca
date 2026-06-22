<?php

namespace Tests\Feature;

use App\Livewire\ChatComponent;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_verifica_se_utilizador_autenticado_consegue_aceder_ao_chat(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chat');

        $response->assertStatus(200);
    }

    public function test_verifica_se_utilizador_consegue_enviar_mensagem(): void
    {
        $user = User::factory()->create();
        $room = Room::create(['nome' => 'All Talk', 'is_dm' => false]);

        Livewire::actingAs($user)
            ->test(ChatComponent::class)
            ->set('activeRoomId', $room->id)
            ->set('newMessage', 'Olá mundo chat')
            ->call('sendMessage')
            ->assertSet('newMessage', '');

        $this->assertDatabaseHas('messages', [
            'room_id' => $room->id,
            'user_id' => $user->id,
            'conteudo' => 'Olá mundo chat',
        ]);
    }

    public function test_apenas_administradores_podem_criar_salas_de_chat(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        Livewire::actingAs($admin)
            ->test(ChatComponent::class)
            ->set('newRoomName', 'Sala Secreta')
            ->call('createRoom')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rooms', ['nome' => 'Sala Secreta', 'is_group' => false]);

        Livewire::actingAs($user)
            ->test(ChatComponent::class)
            ->set('newRoomName', 'Sala Proibida')
            ->call('createRoom')
            ->assertStatus(403);
    }

    public function test_utilizador_consegue_criar_grupo_com_membros(): void
    {
        $creator = User::factory()->create(['role' => User::ROLE_CIDADAO]);
        $member = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        Livewire::actingAs($creator)
            ->test(ChatComponent::class)
            ->set('newGroupName', 'Grupo de Leitura')
            ->set('selectedGroupMembers', [(string) $member->id])
            ->call('createGroup')
            ->assertHasNoErrors();

        $group = Room::where('nome', 'Grupo de Leitura')->first();

        $this->assertNotNull($group);
        $this->assertTrue($group->is_group);
        $this->assertFalse($group->is_admin_only);
        $this->assertTrue($group->users()->where('user_id', $creator->id)->exists());
        $this->assertTrue($group->users()->where('user_id', $member->id)->exists());
    }

    public function test_apenas_admins_podem_criar_grupos_exclusivos_para_admins(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $cidadao = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        Livewire::actingAs($admin)
            ->test(ChatComponent::class)
            ->set('newGroupName', 'Conselho Admin')
            ->set('selectedGroupMembers', [(string) $otherAdmin->id])
            ->set('isGroupAdminOnly', true)
            ->call('createGroup')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rooms', [
            'nome' => 'Conselho Admin',
            'is_group' => true,
            'is_admin_only' => true,
        ]);

        Livewire::actingAs($cidadao)
            ->test(ChatComponent::class)
            ->set('newGroupName', 'Grupo Proibido')
            ->set('selectedGroupMembers', [(string) $admin->id])
            ->set('isGroupAdminOnly', true)
            ->call('createGroup')
            ->assertStatus(403);
    }

    public function test_cidadao_nao_acede_a_grupo_exclusivo_para_admins(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $cidadao = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        $group = Room::create([
            'nome' => 'Staff Only',
            'is_dm' => false,
            'is_group' => true,
            'is_admin_only' => true,
            'created_by' => $admin->id,
        ]);
        $group->users()->attach($admin->id);

        Livewire::actingAs($cidadao)
            ->test(ChatComponent::class)
            ->call('selectRoom', $group->id)
            ->assertSet('activeRoomId', fn ($id) => $id !== $group->id);
    }

    public function test_configuracoes_do_chat_nao_alteram_conta(): void
    {
        $user = User::factory()->create([
            'name' => 'Nome Real',
            'email' => 'real@example.com',
            'password' => 'password',
        ]);

        $originalPasswordHash = $user->fresh()->password;

        Livewire::actingAs($user)
            ->test(ChatComponent::class)
            ->set('chatNickname', 'Apelido Chat')
            ->set('userStatus', 'A ler 📖')
            ->call('saveSettings')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Nome Real', $user->name);
        $this->assertSame('real@example.com', $user->email);
        $this->assertSame($originalPasswordHash, $user->password);
        $this->assertSame('Apelido Chat', $user->chat_nickname);
        $this->assertSame('A ler 📖', $user->estado);
    }
}
