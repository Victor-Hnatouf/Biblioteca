<?php

namespace Database\Factories;

use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;


class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition(): array
    {
        return [
            'google_books_volume_id' => null,
            'isbn'         => $this->faker->isbn13(),
            'nome'         => $this->faker->sentence(3),
            'editora_id'   => Editora::factory(),
            'bibliografia' => $this->faker->paragraph(),
            'imagem_capa'  => null,
            'preco'        => null,
            'vendido_em'   => null,
        ];
    }

    
    public function vendido(): static
    {
        return $this->state(fn (array $attributes) => [
            'vendido_em' => now(),
        ]);
    }
}
