<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(RolesTableSeeder::class);

        //Then create the admin user

        //Company owner s
        $adminID = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'secret')),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //Do the same in the ltu_contributors
        try {
            $contributorID = DB::table('ltu_contributors')->insertGetId([
                'name' => 'Admin',
                'email' => env('ADMIN_EMAIL', 'admin@example.com'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'secret')),
                'role' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        } catch (\Exception $e) {
            //
        }

        //Assign owner role
        $admin = User::find($adminID);
        $admin->assignRole('admin');
    }
}
