<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'argenis@mail.com'],
            [
                'name' => 'ARGENIS SIVIRA',
                'password' => bcrypt('argenis123')
            ]
        );

        User::firstOrCreate(
            ['email' => 'yasnier@mail.com'],
            [
                'name' => 'YASNIER ESCALONA',
                'password' => bcrypt('yasnier123')
            ]
        );
    }
}
