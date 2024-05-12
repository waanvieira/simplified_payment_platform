<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'user Payer test',
            'cpf_cnpj' => '363.019.710-86',
            'email' => 'test@example.com',
            'password' => '123456',
            'balance' => 100
        ]);

        User::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Test Payee User',
            'cpf_cnpj' => '657.287.100-26',
            'email' => 'payee@example.com',
            'balance' => 50
        ]);
    }
}
