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
            'otp_verified_at' => null,
            'otp_expires_at' => null,
            'email_verified_at' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wallets')->insert([
            'tenant_id' => 1,
            'user_id' => 1,
            'balance' => 100.00,
            'total_earned' => 0.00,
            'total_withdrawn' => 0.00,
            'status' => 'active',
            'last_transaction_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('withdrawals')->insert([
            'tenant_id' => 1,
            'user_id' => 1,
            'amount' => 0.00,
            'payment_method' => 'bank_transfer',
            'account_details' => 'Default Bank Account',
            'notes' => 'Initial withdrawal request',
            'status' => 'pending',
            'processed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => 1,
            'address' => '123 Default St',
            'city' => 'Default City',
            'state' => 'Default State',
            'country' => 'Default Country',
            'postal_code' => '12345',
            'avatar' => null,
            'metadata' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('applets')->insert([
            'name' => 'Default Applet',
            'app_id' => 'default-app-id',
            'description' => 'This is a default applet for testing purposes.',
            'icon' => null,
            'config' => json_encode(['key' => 'value']),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('transactions')->insert([
            'tenant_id' => 1,
            'user_id' => 1,
            'applet_id' => 1,
            'reference_id' => 'txn_123456',
            'balance_before' => 0.00,
            'balance_after' => 100.00,
            'amount' => 100.00,
            'type' => 'credit',
            'category' => 'testing',
            'status' => 'completed',
            'description' => 'Initial credit for testing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('withdrawals')->insert([
            'tenant_id' => 1,
            'user_id' => 1,
            'amount' => 50.00,
            'status' => 'pending',
            'processed_at' => null,
            'notes' => 'Test withdrawal request',
            'payment_method' => 'bank_transfer',
            'account_details' => 'Test Bank Account',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tasks')->insert([
            'applet_id' => 1,
            'title' => 'Default Task',
            'description' => 'This is a default task for testing purposes.',
            'reward' => 10.00,
            'requirements' => json_encode(['geoloc','vpn']),
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'metadata' => json_encode(['key' => 'value']),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
