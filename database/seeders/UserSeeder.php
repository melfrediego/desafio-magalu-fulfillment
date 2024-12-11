<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Regular Users
        $users = [
            [
                'name' => 'João Silva Neto - user',
                'email' => 'joao.silva6@example.com',
                'password' => Hash::make('senhaSegura123'),
                'is_client' => false,
                'phone_whatsapp' => null,
                'birth_date' => null,
                'cpf_cnpj' => null,
                'person_type' => null,
            ],
            [
                'name' => 'Maria Eduarda - user',
                'email' => 'maria.eduarda@example.com',
                'password' => Hash::make('senhaSegura123'),
                'is_client' => false,
                'phone_whatsapp' => null,
                'birth_date' => null,
                'cpf_cnpj' => null,
                'person_type' => null,
            ],
            [
                'name' => 'Carlos Silva - user',
                'email' => 'carlos.silva7@example.com',
                'password' => Hash::make('senhaSegura123'),
                'is_client' => false,
                'phone_whatsapp' => null,
                'birth_date' => null,
                'cpf_cnpj' => null,
                'person_type' => null,
            ],
            [
                'name' => 'Maria Oliveira - user',
                'email' => 'maria.oliveira8@example.com',
                'password' => Hash::make('senhaSegura123'),
                'is_client' => false,
                'phone_whatsapp' => null,
                'birth_date' => null,
                'cpf_cnpj' => null,
                'person_type' => null,
            ],
            [
                'name' => 'João Souza - user',
                'email' => 'joao.souza9@example.com',
                'password' => Hash::make('senhaSegura123'),
                'is_client' => false,
                'phone_whatsapp' => null,
                'birth_date' => null,
                'cpf_cnpj' => null,
                'person_type' => null,
            ],
        ];

        // Clients
        $clients = [
            [
                'name' => 'João Silva 3 - Client',
                'email' => 'joao.silvacreate3@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11999999999',
                'birth_date' => '1990-05-15',
                'cpf_cnpj' => '00000009996',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Ana Clara 4 - Client',
                'email' => 'ana.clara4@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11988888888',
                'birth_date' => '1992-07-20',
                'cpf_cnpj' => '00000009997',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Carlos Mendes 5 - Client',
                'email' => 'carlos.mendes5@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11977777777',
                'birth_date' => '1985-03-10',
                'cpf_cnpj' => '00000009998',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Maria Silva 6 - Client',
                'email' => 'maria.silva6@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11966666666',
                'birth_date' => '1989-08-25',
                'cpf_cnpj' => '00000009999',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'João Souza 7 - Client',
                'email' => 'joao.souza7@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11955555555',
                'birth_date' => '1991-12-30',
                'cpf_cnpj' => '00000010000',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Laura Martins 8 - Client',
                'email' => 'laura.martins8@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11944444444',
                'birth_date' => '1993-11-11',
                'cpf_cnpj' => '00000010001',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Pedro Santos 9 - Client',
                'email' => 'pedro.santos9@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11933333333',
                'birth_date' => '1987-04-18',
                'cpf_cnpj' => '00000010002',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Camila Torres 10 - Client',
                'email' => 'camila.torres10@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11922222222',
                'birth_date' => '1994-06-21',
                'cpf_cnpj' => '00000010003',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Rafael Costa 11 - Client',
                'email' => 'rafael.costa11@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11911111111',
                'birth_date' => '1990-09-05',
                'cpf_cnpj' => '00000010004',
                'person_type' => 'PF',
                'is_client' => true,
            ],
            [
                'name' => 'Fernanda Lima 12 - Client',
                'email' => 'fernanda.lima12@example.com',
                'password' => Hash::make('senha123'),
                'phone_whatsapp' => '11900000000',
                'birth_date' => '1995-01-15',
                'cpf_cnpj' => '00000010005',
                'person_type' => 'PF',
                'is_client' => true,
            ],
        ];

        DB::table('users')->insert(array_merge($users, $clients));
    }
}
