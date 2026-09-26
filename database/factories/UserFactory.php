<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // fakerphp/faker is a require-dev dependency; the fake() helper is only
        // registered when Faker\Factory is present, so guard it rather than
        // crashing with "Call to undefined function fake()" on --no-dev installs.
        $faker = class_exists(\Faker\Factory::class) ? fake() : null;

        return [
            'name' => $faker?->name() ?? 'Test User',
            'email' => $faker?->unique()->safeEmail() ?? 'test@example.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
