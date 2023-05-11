<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\Role;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('people')->insert(['firstName'=> 'Francis', 'lastName'=> 'Kings', 'role'=> 'Admin']);

        // \App\Models\Person::firstOrCreate(['firstName'=> 'Francis', 'lastName'=> 'Kings', 'username'=> 'FK', 'password'=> Hash::make('123'), 'role_id'=> 1]);
        // \App\Models\Person::firstOrCreate(['firstName'=> 'John', 'lastName'=> 'Stream', 'username'=> 'JS', 'password'=> Hash::make('123'), 'role_id'=> 2]);
        // \App\Models\Person::firstOrCreate(['firstName'=> 'Anthony', 'lastName'=> 'Shrimp', 'username'=> 'AC', 'password'=> Hash::make('123'), 'role_id'=> 3]);
        // \App\Models\Person::firstOrCreate(['firstName'=> 'Nathaniel', 'lastName'=> 'Days', 'username'=> 'ND', 'password'=> Hash::make('123'), 'role_id'=> 3]);
        // \App\Models\Person::firstOrCreate(['firstName'=> 'Richard', 'lastName'=> 'Scallop', 'username'=> 'RS', 'password'=> Hash::make('123'), 'role_id'=> 3]);

        $people = [
            ['firstName' => 'Francis', 'lastName' => 'Kings', 'username' => 'FK', 'password' => Hash::make('123'), 'role_id' => 1],
            ['firstName' => 'John', 'lastName' => 'Stream', 'username' => 'JS', 'password' => Hash::make('123'), 'role_id' => 2,],
            ['firstName' => 'Anthony', 'lastName' => 'Shrimp', 'username' => 'AC', 'password' => Hash::make('123'), 'role_id' => 3,],
            ['firstName' => 'Nathaniel', 'lastName' => 'Days', 'username' => 'ND', 'password' => Hash::make('123'), 'role_id' => 3,],
            ['firstName' => 'Richard', 'lastName' => 'Scallop', 'username' => 'RS', 'password' => Hash::make('123'), 'role_id' => 3,],
        ];
        
        foreach ($people as $personData) {
            $role = Role::where('id', $personData['role_id'])->first();
        
            if ($role) {
                Person::firstOrCreate($personData);
            }
        }
    }
}
