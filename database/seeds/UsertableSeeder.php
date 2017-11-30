<?php

use Illuminate\Database\Seeder;

class UsertableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
        	'name' => 'admin',
        	'password' => bcrypt('admin'),
        	'email' => 'peter@monoforms.com',
        	'admin' => 1,
        	'avatar' => asset('avatars/avatar.png')
        ]);

          App\User::create([
            'name' => 'Emily Myers',
            'password' => bcrypt('password'),
            'email' => 'emily@myers.com',
            'avatar' => asset('avatars/avatar.png')  
        ]); 
    }
}
