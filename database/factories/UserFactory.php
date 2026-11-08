<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isAnunciante = fake()->boolean();

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => $isAnunciante ? User::ROLE_ANUNCIANTE : User::ROLE_USER,
            'nome_corretora' => $isAnunciante ? fake()->company() : null,
            'cnpj_corretora' => $isAnunciante ? fake()->numerify('##.###.###/####-##') : null,
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    public function anunciante(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_ANUNCIANTE,
            'nome_corretora' => fake()->company(),
            'cnpj_corretora' => fake()->numerify('##.###.###/####-##'),
        ]);
    }
}