<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            ['code' => '001', 'name' => 'Banco do Brasil', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '033', 'name' => 'Banco Santander', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '104', 'name' => 'Caixa Econômica Federal', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '237', 'name' => 'Bradesco', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '341', 'name' => 'Itaú Unibanco', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '356', 'name' => 'Banco Real (antigo)', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '389', 'name' => 'Banco Mercantil do Brasil', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '399', 'name' => 'HSBC Bank Brasil', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '422', 'name' => 'Banco Safra', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '745', 'name' => 'Citibank', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '041', 'name' => 'Banrisul', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '070', 'name' => 'Banco de Brasília (BRB)', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('banks')->insert($banks);
    }
}
