<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Seeder for subscription plans.
 *
 * Creates default plans: Free, Pro, and Enterprise.
 */
class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'description' => 'Plano gratuito com funcionalidades básicas',
            'price' => 0,
            'billing_period' => 'monthly',
            'trial_days' => 0,
            'features' => ['basic'],
            'limits' => ['users' => 1, 'storage' => 100],
            'sort_order' => 1,
            'is_active' => true,
            'is_featured' => false,
        ]);

        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'Plano profissional com recursos avançados',
            'price' => 29.99,
            'billing_period' => 'monthly',
            'trial_days' => 14,
            'features' => ['basic', 'premium', 'analytics'],
            'limits' => ['users' => 10, 'storage' => 1000],
            'sort_order' => 2,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Plano empresarial sem limites',
            'price' => 99.99,
            'billing_period' => 'monthly',
            'trial_days' => 30,
            'features' => ['*'],
            'limits' => ['users' => -1, 'storage' => -1],
            'sort_order' => 3,
            'is_active' => true,
            'is_featured' => false,
        ]);
    }
}
