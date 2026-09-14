<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\Template;
use App\Models\QRProfile;
use App\Models\SocialLink;
use App\Models\CustomLink;
use App\Models\QRScan;
use App\Models\AnalyticsEvent;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\QRCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Plans
        $freePlan = Plan::firstOrCreate(['slug' => 'free'], [
            'name' => 'Starter Free',
            'price' => 0.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'profile_limit' => 1,
            'link_limit' => 5,
            'status' => 'active',
        ]);

        $proPlan = Plan::firstOrCreate(['slug' => 'pro'], [
            'name' => 'Pro Creator',
            'price' => 9.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'profile_limit' => -1, // unlimited
            'link_limit' => -1,
            'status' => 'active',
        ]);

        $bizPlan = Plan::firstOrCreate(['slug' => 'business'], [
            'name' => 'Business & Teams',
            'price' => 29.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'profile_limit' => -1,
            'link_limit' => -1,
            'status' => 'active',
        ]);

        // 2. Seed All 12 Initial Profile Templates
        $templatesData = [
            ['slug' => 'business-card', 'name' => 'Business Card', 'category' => 'Business', 'is_premium' => false],
            ['slug' => 'creator', 'name' => 'Creator', 'category' => 'Creator', 'is_premium' => true],
            ['slug' => 'influencer', 'name' => 'Influencer', 'category' => 'Social', 'is_premium' => true],
            ['slug' => 'restaurant', 'name' => 'Restaurant', 'category' => 'Food & Hospitality', 'is_premium' => true],
            ['slug' => 'real-estate', 'name' => 'Real Estate', 'category' => 'Property', 'is_premium' => true],
            ['slug' => 'freelancer', 'name' => 'Freelancer', 'category' => 'Creative', 'is_premium' => false],
            ['slug' => 'consultant', 'name' => 'Consultant', 'category' => 'Business', 'is_premium' => true],
            ['slug' => 'developer', 'name' => 'Developer', 'category' => 'Technology', 'is_premium' => false],
            ['slug' => 'agency', 'name' => 'Agency', 'category' => 'Business', 'is_premium' => true],
            ['slug' => 'personal', 'name' => 'Personal', 'category' => 'Personal', 'is_premium' => false],
            ['slug' => 'event', 'name' => 'Event', 'category' => 'Events', 'is_premium' => true],
            ['slug' => 'product', 'name' => 'Product Launch', 'category' => 'E-Commerce', 'is_premium' => true],
        ];

        foreach ($templatesData as $tData) {
            Template::firstOrCreate(['slug' => $tData['slug']], [
                'name' => $tData['name'],
                'category' => $tData['category'],
                'is_premium' => $tData['is_premium'],
                'status' => 'active',
            ]);
        }

        $bizTemplate = Template::where('slug', 'business-card')->first();
        $creatorTemplate = Template::where('slug', 'creator')->first();
        $devTemplate = Template::where('slug', 'developer')->first();
        // 3. Seed Admin User
        $admin = User::firstOrCreate(['email' => 'admin@qrsocialsaas.com'], [
            'name' => 'System Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'plan_id' => $bizPlan->id,
            'status' => 'active',
            'onboarding_completed' => true,
        ]);

        // =========================================================================
        // EXAMPLE 1 — BUSINESS (John Doe)
        // =========================================================================
        $userJohn = User::firstOrCreate(['email' => 'john@example.com'], [
            'name' => 'John Doe',
            'password' => Hash::make('password'),
            'role' => 'user',
            'plan_id' => $bizPlan->id,
            'status' => 'active',
            'onboarding_completed' => true,
        ]);

        $profileJohn = QRProfile::updateOrCreate(
            ['slug' => 'john-doe'],
            [
                'user_id' => $userJohn->id,
                'name' => 'John Doe',
                'designation' => 'CEO & Founder',
                'company' => 'ABC Technologies',
                'bio' => 'Technology entrepreneur helping businesses build modern digital solutions.',
                'phone' => '+19999999999',
                'email' => 'john@abctechnologies.com',
                'website' => 'https://abctechnologies.com',
                'template_id' => $bizTemplate->id,
                'theme_data' => [
                    'bg_color' => '#f8fafc',
                    'text_color' => '#0f172a',
                    'button_style' => 'rounded-xl',
                    'theme_preset' => 'Business'
                ],
                'status' => 'active',
            ]
        );

        $this->seedSocialLinks($profileJohn->id, [
            ['platform' => 'website', 'title' => 'Company Website', 'url' => 'https://abctechnologies.com', 'sort_order' => 1],
            ['platform' => 'linkedin', 'title' => 'LinkedIn', 'url' => 'https://linkedin.com/in/johndoe', 'sort_order' => 2],
            ['platform' => 'instagram', 'title' => 'Instagram', 'url' => 'https://instagram.com/johndoe', 'sort_order' => 3],
            ['platform' => 'youtube', 'title' => 'YouTube', 'url' => 'https://youtube.com/@johndoetech', 'sort_order' => 4],
            ['platform' => 'whatsapp', 'title' => 'WhatsApp', 'url' => 'https://wa.me/19999999999', 'sort_order' => 5],
        ]);

        $this->seedCustomLinks($profileJohn->id, [
            ['title' => 'Book a Meeting', 'description' => 'Schedule a 30-min strategy call on Calendly', 'url' => 'https://calendly.com/johndoe', 'sort_order' => 1],
            ['title' => 'Company Website', 'description' => 'Explore ABC Technologies solutions & services', 'url' => 'https://abctechnologies.com', 'sort_order' => 2],
            ['title' => 'Our Services', 'description' => 'Enterprise software, web applications & cloud development', 'url' => 'https://abctechnologies.com/services', 'sort_order' => 3],
            ['title' => 'Contact Us', 'description' => 'Get in touch with our sales & consulting team', 'url' => 'https://abctechnologies.com/contact', 'sort_order' => 4],
        ]);

        app(QRCodeService::class)->generate($profileJohn);
        $this->seedAnalytics($profileJohn->id, 124);

        // =========================================================================
        // EXAMPLE 2 — CREATOR (Sarah Sharma)
        // =========================================================================
        $userSarah = User::firstOrCreate(['email' => 'sarah@example.com'], [
            'name' => 'Sarah Sharma',
            'password' => Hash::make('password'),
            'role' => 'user',
            'plan_id' => $proPlan->id,
            'status' => 'active',
            'onboarding_completed' => true,
        ]);

        $profileSarah = QRProfile::updateOrCreate(
            ['slug' => 'sarah-sharma'],
            [
                'user_id' => $userSarah->id,
                'name' => 'Sarah Sharma',
                'designation' => 'Content Creator',
                'company' => 'Independent Creator',
                'bio' => 'Sharing technology, productivity and creative content.',
                'phone' => '+919876543210',
                'email' => 'sarah@creatorstudio.com',
                'website' => 'https://youtube.com/@sarahsharma',
                'template_id' => $creatorTemplate->id,
                'theme_data' => [
                    'bg_color' => '#faf5ff',
                    'text_color' => '#3b0764',
                    'button_style' => 'rounded-full',
                    'theme_preset' => 'Creator'
                ],
                'status' => 'active',
            ]
        );

        $this->seedSocialLinks($profileSarah->id, [
            ['platform' => 'instagram', 'title' => 'Instagram', 'url' => 'https://instagram.com/sarahsharma', 'sort_order' => 1],
            ['platform' => 'youtube', 'title' => 'YouTube Channel', 'url' => 'https://youtube.com/@sarahsharma', 'sort_order' => 2],
            ['platform' => 'tiktok', 'title' => 'TikTok', 'url' => 'https://tiktok.com/@sarahsharma', 'sort_order' => 3],
            ['platform' => 'twitter', 'title' => 'X / Twitter', 'url' => 'https://twitter.com/sarahsharma', 'sort_order' => 4],
            ['platform' => 'threads', 'title' => 'Threads', 'url' => 'https://threads.net/@sarahsharma', 'sort_order' => 5],
        ]);

        $this->seedCustomLinks($profileSarah->id, [
            ['title' => 'Latest Video', 'description' => 'Watch my latest video on YouTube', 'url' => 'https://youtube.com/watch?v=demo', 'sort_order' => 1],
            ['title' => 'My Newsletter', 'description' => 'Weekly tech & productivity digest', 'url' => 'https://sarahsharma.substack.com', 'sort_order' => 2],
            ['title' => 'YouTube Channel', 'description' => 'Subscribe for weekly tutorials & reviews', 'url' => 'https://youtube.com/@sarahsharma', 'sort_order' => 3],
            ['title' => 'Collaboration', 'description' => 'Sponsorships, speaking & brand partnerships', 'url' => 'mailto:sarah@creatorstudio.com', 'sort_order' => 4],
        ]);

        app(QRCodeService::class)->generate($profileSarah);
        $this->seedAnalytics($profileSarah->id, 860);

        // =========================================================================
        // EXAMPLE 3 — FREELANCER (Alex Verma)
        // =========================================================================
        $userAlex = User::firstOrCreate(['email' => 'alex@example.com'], [
            'name' => 'Alex Verma',
            'password' => Hash::make('password'),
            'role' => 'user',
            'plan_id' => $proPlan->id,
            'status' => 'active',
            'onboarding_completed' => true,
        ]);

        $profileAlex = QRProfile::updateOrCreate(
            ['slug' => 'alex-verma'],
            [
                'user_id' => $userAlex->id,
                'name' => 'Alex Verma',
                'designation' => 'Full-Stack Developer',
                'company' => 'Independent Developer',
                'bio' => 'Building scalable web applications and digital products.',
                'phone' => '+919998887776',
                'email' => 'alex@vermacode.dev',
                'website' => 'https://vermacode.dev',
                'template_id' => $devTemplate->id,
                'theme_data' => [
                    'bg_color' => '#0f172a',
                    'text_color' => '#f8fafc',
                    'button_style' => 'rounded-xl',
                    'theme_preset' => 'Dark'
                ],
                'status' => 'active',
            ]
        );

        $this->seedSocialLinks($profileAlex->id, [
            ['platform' => 'github', 'title' => 'GitHub Profile', 'url' => 'https://github.com/alexverma', 'sort_order' => 1],
            ['platform' => 'linkedin', 'title' => 'LinkedIn', 'url' => 'https://linkedin.com/in/alexverma', 'sort_order' => 2],
            ['platform' => 'website', 'title' => 'Portfolio', 'url' => 'https://vermacode.dev', 'sort_order' => 3],
            ['platform' => 'youtube', 'title' => 'Coding Tutorials', 'url' => 'https://youtube.com/@alexvermacode', 'sort_order' => 4],
            ['platform' => 'twitter', 'title' => 'X / Twitter', 'url' => 'https://twitter.com/alexvermacode', 'sort_order' => 5],
        ]);

        $this->seedCustomLinks($profileAlex->id, [
            ['title' => 'View Portfolio', 'description' => 'Explore my full-stack web & API projects', 'url' => 'https://vermacode.dev/portfolio', 'sort_order' => 1],
            ['title' => 'Hire Me', 'description' => 'Available for contract & freelance software projects', 'url' => 'https://vermacode.dev/hire', 'sort_order' => 2],
            ['title' => 'My Projects', 'description' => 'Open source packages & SaaS tools on GitHub', 'url' => 'https://github.com/alexverma', 'sort_order' => 3],
            ['title' => 'Download Resume', 'description' => 'Full-Stack Software Engineer CV (PDF)', 'url' => 'https://vermacode.dev/resume.pdf', 'sort_order' => 4],
        ]);

        app(QRCodeService::class)->generate($profileAlex);
        $this->seedAnalytics($profileAlex->id, 450);

        // Seed billing subscriptions and sample invoices
        $this->seedBillingForUser($admin, $bizPlan);
        $this->seedBillingForUser($userJohn, $bizPlan);
        $this->seedBillingForUser($userSarah, $proPlan);
        $this->seedBillingForUser($userAlex, $proPlan);
    }

    private function seedBillingForUser(User $user, Plan $plan): void
    {
        $subscription = Subscription::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            [
                'plan_id' => $plan->id,
                'provider' => 'stripe',
                'provider_subscription_id' => 'sub_stripe_' . uniqid(),
                'status' => 'active',
                'starts_at' => now()->subDays(12),
                'ends_at' => now()->addDays(18),
            ]
        );

        if ($user->payments()->count() === 0) {
            $sampleInvoices = [
                [
                    'amount' => $plan->price > 0 ? $plan->price : 29.00,
                    'created_at' => now()->subDays(12),
                    'status' => 'completed',
                    'provider' => 'stripe',
                    'transaction_id' => 'ch_3N' . strtoupper(substr(md5($user->id . '1'), 0, 14)),
                    'payment_data' => [
                        'plan' => $plan->name . ' (Monthly Renewal)',
                        'cycle' => 'monthly',
                        'card_brand' => 'Visa',
                        'card_last4' => '4242',
                        'tax' => 0.00,
                    ],
                ],
                [
                    'amount' => $plan->price > 0 ? $plan->price : 29.00,
                    'created_at' => now()->subDays(42),
                    'status' => 'completed',
                    'provider' => 'stripe',
                    'transaction_id' => 'ch_3M' . strtoupper(substr(md5($user->id . '2'), 0, 14)),
                    'payment_data' => [
                        'plan' => $plan->name . ' Subscription',
                        'cycle' => 'monthly',
                        'card_brand' => 'Visa',
                        'card_last4' => '4242',
                        'tax' => 0.00,
                    ],
                ],
                [
                    'amount' => 9.00,
                    'created_at' => now()->subDays(72),
                    'status' => 'completed',
                    'provider' => 'stripe',
                    'transaction_id' => 'ch_3L' . strtoupper(substr(md5($user->id . '3'), 0, 14)),
                    'payment_data' => [
                        'plan' => 'Pro Creator Plan Upgrade',
                        'cycle' => 'monthly',
                        'card_brand' => 'Mastercard',
                        'card_last4' => '8888',
                        'tax' => 0.00,
                    ],
                ],
                [
                    'amount' => 4.99,
                    'created_at' => now()->subDays(95),
                    'status' => 'completed',
                    'provider' => 'stripe',
                    'transaction_id' => 'ch_3K' . strtoupper(substr(md5($user->id . '4'), 0, 14)),
                    'payment_data' => [
                        'plan' => 'Custom Domain SSL Pack (Add-on)',
                        'cycle' => 'one-time',
                        'card_brand' => 'Visa',
                        'card_last4' => '4242',
                        'tax' => 0.00,
                    ],
                ],
                [
                    'amount' => 0.00,
                    'created_at' => now()->subDays(120),
                    'status' => 'completed',
                    'provider' => 'stripe',
                    'transaction_id' => 'ch_free_' . strtolower(substr(md5($user->id . '5'), 0, 12)),
                    'payment_data' => [
                        'plan' => 'Starter Free Tier Setup',
                        'cycle' => 'lifetime',
                        'card_brand' => 'System',
                        'card_last4' => 'Free',
                        'tax' => 0.00,
                    ],
                ],
            ];

            foreach ($sampleInvoices as $inv) {
                Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'provider' => $inv['provider'],
                    'transaction_id' => $inv['transaction_id'],
                    'amount' => $inv['amount'],
                    'currency' => 'USD',
                    'status' => $inv['status'],
                    'payment_data' => $inv['payment_data'],
                    'created_at' => $inv['created_at'],
                    'updated_at' => $inv['created_at'],
                ]);
            }
        }
    }

    private function seedSocialLinks(int $profileId, array $links): void
    {
        SocialLink::where('profile_id', $profileId)->delete();
        foreach ($links as $l) {
            SocialLink::create([
                'profile_id' => $profileId,
                'platform' => $l['platform'],
                'title' => $l['title'],
                'url' => $l['url'],
                'sort_order' => $l['sort_order'],
                'status' => 'active',
            ]);
        }
    }

    private function seedCustomLinks(int $profileId, array $links): void
    {
        CustomLink::where('profile_id', $profileId)->delete();
        foreach ($links as $l) {
            CustomLink::create([
                'profile_id' => $profileId,
                'title' => $l['title'],
                'description' => $l['description'],
                'url' => $l['url'],
                'sort_order' => $l['sort_order'],
                'status' => 'active',
            ]);
        }
    }

    private function seedAnalytics(int $profileId, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            QRScan::create([
                'profile_id' => $profileId,
                'scanned_at' => now()->subDays(rand(0, 30)),
                'ip_hash' => hash('sha256', 'ip_' . rand(1, 50)),
                'country' => 'United States',
                'device' => rand(0, 1) ? 'Mobile' : 'Desktop',
                'browser' => rand(0, 1) ? 'Chrome' : 'Safari',
            ]);
        }
    }
}
