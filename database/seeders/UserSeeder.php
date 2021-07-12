<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=> 'Super Admin',
            'email'=> 'superadmin@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> true,
        ]);

        User::create([
            'name'=> 'Store Admin',
            'email'=> 'storeadmin@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> false,
        ]);
    }
}
