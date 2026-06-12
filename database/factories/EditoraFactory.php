<?php

namespace Database\Factories;

use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;


class EditoraFactory extends Factory
{
    protected $model = Editora::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->company() . ' Editora',
        ];
    }
}
