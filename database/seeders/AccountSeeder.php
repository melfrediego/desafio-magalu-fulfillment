<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = DB::table('users')->where('is_client', true)->pluck('id')->toArray();
        $banks = [1, 2, 4, 5, 6, 7];

        $accounts = [];

        foreach (range(1, 10) as $index) {
            $accounts[] = [
                'agency' => str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'number' => str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT),
                'balance' => rand(100, 10000),
                'credit_limit' => rand(500, 5000),
                'user_id' => $clients[array_rand($clients)],
                'bank_id' => $banks[array_rand($banks)],
            ];
        }

        DB::table('accounts')->insert($accounts);
    }
}
