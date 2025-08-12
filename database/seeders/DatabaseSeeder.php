<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('tenants')->insert([
            'name' => 'Default Tenant',
            'domain' => 'default.tenant.com',
            'project_id' => 'default-project-id',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'tenant_id' => 1,
            'phone' => '01819400400',
            'name' => 'Default User',
            'otp' => null,
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'phone_verified_at' => null,
            'email_verified_at' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => 1,
            'name' => 'Default',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
