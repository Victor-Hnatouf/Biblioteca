<?php

use App\Livewire\RequisicoesComponent;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use Illuminate\Support\Facades\Mail;
uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});


test('1. verifica se um utilizador pode criar uma requisição de um livro corretamente', function () {
    $user = User::factory()->create();
    $livro = Livro::factory()->create();

    Livewire::actingAs($user)
        ->test(RequisicoesComponent::class)
        ->set('livro_id', $livro->id)
        ->call('criarRequisicao')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('requisicoes', [
        'livro_id' => $livro->id,
        'cidadao_id' => $user->id,
    ]);
});

test('2. assegura que uma requisição não pode ser criada sem um livro válido', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(RequisicoesComponent::class)
        ->set('livro_id', 99999) 
        ->call('criarRequisicao')
        ->assertHasErrors(['livro_id' => 'exists']);
});

test('3. confirma se um utilizador pode devolver um livro', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $livro = Livro::factory()->create();
    
    
    $requisicao = Requisicao::factory()->create([
        'livro_id' => $livro->id,
        'cidadao_id' => $user->id,
        'entregue_em' => null,
        'cidadao_entregou_em' => null,
    ]);

    
    Livewire::actingAs($user)
        ->test(RequisicoesComponent::class)
        ->call('marcarEntregaNaBiblioteca', $requisicao->id);
        
    expect($requisicao->fresh()->cidadao_entregou_em)->not->toBeNull();

    
    Livewire::actingAs($admin)
        ->test(RequisicoesComponent::class)
        ->call('registarRelatorioDevolucao', $requisicao->id, Requisicao::CONDICAO_BOAS);

    expect($requisicao->fresh()->entregue_em)->not->toBeNull()
        ->and($requisicao->fresh()->condicao_na_devolucao)->toBe(Requisicao::CONDICAO_BOAS);
});

test('4. garante que um utilizador consegue ver as suas requisições corretamente', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $livro1 = Livro::factory()->create();
    $livro2 = Livro::factory()->create();

    
    Requisicao::factory()->create([
        'livro_id' => $livro1->id,
        'cidadao_id' => $user1->id,
    ]);

    
    Requisicao::factory()->create([
        'livro_id' => $livro2->id,
        'cidadao_id' => $user2->id,
    ]);

    
    Livewire::actingAs($user1)
        ->test(RequisicoesComponent::class)
        ->assertSee($livro1->nome)
        ->assertDontSee($livro2->nome);
});

test('5. confirma se não é possível requisitar um livro sem stock disponível (já requisitado)', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $livro = Livro::factory()->create();

    
    Livewire::actingAs($user1)
        ->test(RequisicoesComponent::class)
        ->set('livro_id', $livro->id)
        ->call('criarRequisicao');

    
    
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Livro indisponível para requisição.');

    Livewire::actingAs($user2)
        ->test(RequisicoesComponent::class)
        ->set('livro_id', $livro->id)
        ->call('criarRequisicao');
});
