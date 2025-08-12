<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitianFactory extends Seeder
{
    /**
     * Run the database seeds.
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
            'phone' => '1234567890',
            'name' => 'Default User',
            'otp' => null,
            'email' => 'test@test.com',
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => 1,
            'first_name' => 'Default',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
