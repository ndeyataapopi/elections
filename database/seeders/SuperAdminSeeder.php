<?php

namespace Database\Seeders;

use App\Models\User; // Add this line

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        User::create([
            'tenant_id' => null,
            'role' => 'super_admin',
            'name' => 'Elections Admin',
            'email' => 'elections@nepticgroup.com',
            'password' => bcrypt('password')
        ]);

    }
}
