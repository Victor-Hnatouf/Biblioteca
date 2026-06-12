<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class RequisicaoFactory extends Factory
{
    protected $model = Requisicao::class;

    public function definition(): array
    {
        return [
            'numero' => $this->faker->unique()->numberBetween(1, 1000),
            'livro_id' => Livro::factory(),
            'cidadao_id' => User::factory(),
            'cidadao_nome' => $this->faker->name(),
            'cidadao_email' => $this->faker->unique()->safeEmail(),
            'cidadao_profile_photo_path' => null,
            'requisitado_em' => now(),
            'previsto_entrega_em' => now()->addDays(5)->toDateString(),
            'cidadao_entregou_em' => null,
            'entregue_em' => null,
            'condicao_na_devolucao' => null,
            'confirmado_por_admin_id' => null,
            'dias_decorridos' => null,
        ];
    }

    public function ativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'cidadao_entregou_em' => null,
            'entregue_em' => null,
            'condicao_na_devolucao' => null,
            'confirmado_por_admin_id' => null,
            'dias_decorridos' => null,
        ]);
    }

    public function devolvida(): static
    {
        return $this->state(fn (array $attributes) => [
            'cidadao_entregou_em' => now(),
            'entregue_em' => now()->toDateString(),
            'condicao_na_devolucao' => Requisicao::CONDICAO_BOAS,
            'confirmado_por_admin_id' => User::factory()->create(['role' => User::ROLE_ADMIN])->id,
            'dias_decorridos' => 5,
        ]);
    }

    public function aguardandoRelatorio(): static
    {
        return $this->state(fn (array $attributes) => [
            'cidadao_entregou_em' => now(),
            'entregue_em' => null,
            'condicao_na_devolucao' => null,
            'confirmado_por_admin_id' => null,
            'dias_decorridos' => null,
        ]);
    }

    public function paraUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'cidadao_id' => $user->id,
            'cidadao_nome' => $user->name,
            'cidadao_email' => $user->email,
            'cidadao_profile_photo_path' => $user->profile_photo_path,
        ]);
    }

    public function paraLivro(Livro $livro): static
    {
        return $this->state(fn (array $attributes) => [
            'livro_id' => $livro->id,
        ]);
    }
}
