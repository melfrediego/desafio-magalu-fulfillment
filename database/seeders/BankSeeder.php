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
            ['code' => '001', 'name' => 'Banco do Brasil'],
            ['code' => '033', 'name' => 'Banco Santander'],
            ['code' => '104', 'name' => 'Caixa Econômica Federal'],
            ['code' => '237', 'name' => 'Bradesco'],
            ['code' => '341', 'name' => 'Itaú Unibanco'],
            ['code' => '356', 'name' => 'Banco Real (antigo)'],
            ['code' => '389', 'name' => 'Banco Mercantil do Brasil'],
            ['code' => '399', 'name' => 'HSBC Bank Brasil'],
            ['code' => '422', 'name' => 'Banco Safra'],
            ['code' => '745', 'name' => 'Citibank'],
            ['code' => '041', 'name' => 'Banrisul'],
            ['code' => '070', 'name' => 'Banco de Brasília (BRB)'],
        ];
        DB::table('banks')->insert($banks);
    }
}
