<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Admin', 'TeamLeader', 'Member'];
        $roleCount = count($roles);

        for ($i = 0; $i < $roleCount; $i++) {
            Role::insert([
                'name' => $roles[$i],
            ]);
        }
    }
}
