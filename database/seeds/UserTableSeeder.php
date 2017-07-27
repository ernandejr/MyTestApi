<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
        [
        	'name' => 'Ernande',
        	'username' => 'ernandejr',
        	'email' => 'ernande.junior@gmail.com',
        	'password' => Hash::make('Aa1234')
        ]
        ];

        foreach ($users as $user) {
        	\App\User::create($user);
        }
    }
}
