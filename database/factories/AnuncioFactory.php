<?php

namespace Database\Factories;

use App\Models\Anuncio;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anuncio>
 */
class AnuncioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = fake()->randomElement(['Apartamento', 'Casa', 'Sala Comercial', 'Terreno']);

        return [
            'titulo' => fake()->streetName() . ' - ' . $tipo,
            'email' => fake()->safeEmail(),
            'preco' => fake()->randomFloat(2, 80000, 950000),
            'area' => fake()->randomFloat(2, 25, 350),
            'user_id' => User::factory()->anunciante(),
            'categoria_id' => Categoria::where('nome', $tipo)->value('id'),
            'telefone' => fake()->numerify('(##) #####-####'),
            'descricao' => fake()->paragraph(),
        ];
    }
}
