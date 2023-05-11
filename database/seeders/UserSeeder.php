<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            ['firstName' => 'Francis', 'lastName' => 'Kings', 'username' => 'FK', 'password' => Hash::make('123'), 'role_id' => 1],
            ['firstName' => 'John', 'lastName' => 'Stream', 'username' => 'JS', 'password' => Hash::make('123'), 'role_id' => 2,],
            ['firstName' => 'Anthony', 'lastName' => 'Shrimp', 'username' => 'AC', 'password' => Hash::make('123'), 'role_id' => 3,],
            ['firstName' => 'Nathaniel', 'lastName' => 'Days', 'username' => 'ND', 'password' => Hash::make('123'), 'role_id' => 3,],
            ['firstName' => 'Richard', 'lastName' => 'Scallop', 'username' => 'RS', 'password' => Hash::make('123'), 'role_id' => 3,],
        ];
        
        foreach ($user as $userData) {
            $role = Role::where('id', $userData['role_id'])->first();
        
            if ($role) {
                User::firstOrCreate($userData);
            }
        }
    }
}
