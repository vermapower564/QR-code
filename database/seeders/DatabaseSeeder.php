<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\Template;
use App\Models\QRProfile;
use App\Models\SocialLink;
use App\Models\CustomLink;
use App\Services\QRCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Plans
        $freePlan = Plan::create([
            'name' => 'Starter Free',
            'slug' => 'free',
            'price' => 0.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'profile_limit' => 1,
            'link_limit' => 5,
            'status' => 'active',
        ]);

        $proPlan = Plan::create([
            'name' => 'Pro Creator',
            'slug' => 'pro',
            'price' => 9.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'profile_limit' => -1, // unlimited
            'link_limit' => -1,
            'status' => 'active',
        ]);

        $bizPlan = Plan::create([
            'name' => 'Business & Teams',
            'slug' => 'business',
            'price' => 29.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'profile_limit' => -1,
            'link_limit' => -1,
            'status' => 'active',
        ]);

        // 2. Seed Templates
        $classicTemplate = Template::create([
            'name' => 'Classic Minimal',
            'slug' => 'classic-minimal',
            'category' => 'General',
            'is_premium' => false,
            'status' => 'active',
        ]);

        Template::create([
            'name' => 'Modern Gradient',
            'slug' => 'modern-gradient',
            'category' => 'Creator',
            'is_premium' => true,
            'status' => 'active',
        ]);

        // 3. Seed Admin User
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@qrsocialsaas.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'plan_id' => $bizPlan->id,
            'status' => 'active',
        ]);

        // 4. Seed Demo User
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'plan_id' => $proPlan->id,
            'status' => 'active',
        ]);

        // 5. Seed Demo Profile
        $profile = QRProfile::create([
            'user_id' => $user->id,
            'slug' => 'john-doe',
            'name' => 'John Doe',
            'designation' => 'CEO & Co-founder',
            'company' => 'ABC Technologies',
            'bio' => 'Technology entrepreneur building next-gen web & cloud solutions.',
            'phone' => '+19999999999',
            'email' => 'john@example.com',
            'website' => 'https://example.com',
            'template_id' => $classicTemplate->id,
            'theme_data' => [
                'bg_color' => '#f8fafc',
                'text_color' => '#0f172a',
                'button_style' => 'rounded-xl',
                'theme_preset' => 'Classic',
            ],
            'status' => 'active',
        ]);

        // Social Links
        SocialLink::create([
            'profile_id' => $profile->id,
            'platform' => 'instagram',
            'title' => 'Instagram',
            'url' => 'https://instagram.com/johndoe',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        SocialLink::create([
            'profile_id' => $profile->id,
            'platform' => 'linkedin',
            'title' => 'LinkedIn Profile',
            'url' => 'https://linkedin.com/in/johndoe',
            'sort_order' => 2,
            'status' => 'active',
        ]);

        // Custom Links
        CustomLink::create([
            'profile_id' => $profile->id,
            'title' => 'Book a Meeting',
            'description' => 'Schedule a 30-min strategy call on Calendly',
            'url' => 'https://calendly.com/johndoe',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        // Generate QR code for seed profile
        app(QRCodeService::class)->generate($profile);
    }
}
