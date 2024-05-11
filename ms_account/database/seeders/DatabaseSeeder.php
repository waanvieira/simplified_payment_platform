<?php

namespace Database\Seeders;

use App\Models\Account;
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
        // Account::factory(10)->create();
        if (!Account::where('id', '7e0b17a3-2f57-4273-a48a-9f81ed2958eb')->first()) {
            Account::factory()->create([
                'id' => '7e0b17a3-2f57-4273-a48a-9f81ed2958eb',
                'name' => 'user Payer test',
                'cpf_cnpj' => '363.019.710-86',
                'email' => 'test@example.com',
                'password' => '123456',
                'balance' => 100
            ]);
        }


        if (!Account::where('id', '8afd4fc5-5989-4b69-a799-1a0e2866235d')->first()) {
            Account::factory()->create([
                'id' => '8afd4fc5-5989-4b69-a799-1a0e2866235d',
                'name' => 'Test Payee User',
                'cpf_cnpj' => '657.287.100-26',
                'email' => 'payee@example.com',
                'password' => '123456',
                'balance' => 50
            ]);
        }

        if (!Account::where('id', '8d2b2858-d84a-4174-9a49-38fa95ed4bd6')->first()) {
            Account::factory()->create([
                'id' => '8d2b2858-d84a-4174-9a49-38fa95ed4bd6',
                'name' => 'Test ShopKeeper User',
                'cpf_cnpj' => '11.071.552/0001-07',
                'email' => 'shopKeeper@example.com',
                'password' => '123456',
                'shopkeeper' => true,
                'balance' => 50
            ]);
        }
    }
}
