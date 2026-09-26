<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Standalone, individually sellable dupatta (Rs.300).
        $this->call(DupattaProductSeeder::class);

        // Demo user. Deliberately NOT User::factory(): faker lives in require-dev,
        // so the factory blows up with "undefined function fake()" on any
        // composer install --no-dev deployment - and it is not idempotent,
        // which made re-running /run-seed throw a duplicate-email violation.
        // Unguarded: email_verified_at / remember_token are not in $fillable and
        // would otherwise be silently discarded on a fresh database.
        User::unguarded(function () {
            User::firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
        });

        $this->command?->info('Seeded '.User::count().' user(s).');
    }
}
