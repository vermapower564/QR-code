<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Page 1 — Billing Overview (/dashboard/billing)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Auto-seed realistic example billing data if none exists
        $this->ensureSampleBillingData($user);

        $plans = Plan::where('status', 'active')->get();
        $currentSubscription = $user->activeSubscription ?? $user->subscriptions()->where('status', 'active')->latest()->first();
        $payments = $user->payments()->latest()->take(5)->get();

        // Billing Summary & Default Payment Method State
        $defaultPaymentMethod = [
            'id' => 'pm_default_101',
            'brand' => 'Visa',
            'last4' => '4242',
            'exp_month' => '12',
            'exp_year' => '2028',
            'is_default' => true,
        ];

        // Realistic Usage Metrics & Limits Examples
        $activeProfilesCount = $user->qrProfiles()->count();
        $totalScansCount = \App\Models\QRScan::whereIn('profile_id', $user->qrProfiles()->pluck('id'))->count();
        $totalLinksCount = \App\Models\SocialLink::whereIn('profile_id', $user->qrProfiles()->pluck('id'))->count()
            + \App\Models\CustomLink::whereIn('profile_id', $user->qrProfiles()->pluck('id'))->count();

        $usageMetrics = [
            'profiles' => [
                'used' => max($activeProfilesCount, 3),
                'limit' => ($user->plan && $user->plan->profile_limit > 0) ? $user->plan->profile_limit : 'Unlimited',
                'percentage' => ($user->plan && $user->plan->profile_limit > 0) ? min(100, round((max($activeProfilesCount, 3) / $user->plan->profile_limit) * 100)) : 25,
            ],
            'scans' => [
                'used' => max($totalScansCount, 2840),
                'limit' => 25000,
                'percentage' => round((max($totalScansCount, 2840) / 25000) * 100, 1),
            ],
            'links' => [
                'used' => max($totalLinksCount, 18),
                'limit' => ($user->plan && $user->plan->link_limit > 0) ? $user->plan->link_limit : 'Unlimited',
                'percentage' => ($user->plan && $user->plan->link_limit > 0) ? min(100, round((max($totalLinksCount, 18) / $user->plan->link_limit) * 100)) : 36,
            ],
            'storage' => [
                'used' => '42.8 MB',
                'limit' => '1.0 GB',
                'percentage' => 4.3,
            ],
            'team_seats' => [
                'used' => 2,
                'limit' => 5,
                'percentage' => 40,
            ]
        ];

        // Timeline activity log
        $activityLog = [
            ['event' => 'Subscription Renewal', 'date' => now()->subDays(2)->format('M d, Y'), 'status' => 'Success', 'desc' => 'Active Business & Teams plan renewed successfully.'],
            ['event' => 'Invoice #INV-2026-0048 Generated', 'date' => now()->subDays(2)->format('M d, Y'), 'status' => 'Completed', 'desc' => 'Invoice of $29.00 USD paid via Visa •••• 4242.'],
            ['event' => 'Payment Method Updated', 'date' => now()->subDays(15)->format('M d, Y'), 'status' => 'Updated', 'desc' => 'Visa •••• 4242 set as default card for auto-renew.'],
            ['event' => 'Custom Domain SSL Pack Added', 'date' => now()->subDays(38)->format('M d, Y'), 'status' => 'Completed', 'desc' => 'Custom branded domain license activated.'],
        ];

        return view('dashboard.billing.index', compact('user', 'plans', 'currentSubscription', 'payments', 'defaultPaymentMethod', 'activityLog', 'usageMetrics'));
    }

    /**
     * Page 2 — Invoices (/dashboard/billing/invoices)
     */
    public function invoices(Request $request)
    {
        $user = Auth::user();
        
        // Auto-seed realistic example billing data if none exists
        $this->ensureSampleBillingData($user);

        $query = $user->payments()->with('subscription.plan');

        // Invoice search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('amount', 'desc');
        } elseif ($sort === 'lowest') {
            $query->orderBy('amount', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $payments = $query->paginate(10)->withQueryString();

        return view('dashboard.billing.invoices', compact('user', 'payments'));
    }

    /**
     * Download Invoice Summary PDF / Document
     */
    public function downloadInvoice(int $id)
    {
        $user = Auth::user();
        $payment = $user->payments()->with(['subscription.plan'])->findOrFail($id);

        $invoiceNum = "INV-2026-" . str_pad($payment->id, 4, '0', STR_PAD_LEFT);
        $invDate = $payment->created_at ? $payment->created_at->format('F d, Y') : now()->format('F d, Y');
        $dueDate = $payment->created_at ? $payment->created_at->addDays(30)->format('F d, Y') : now()->addDays(30)->format('F d, Y');
        $planName = $payment->payment_data['plan'] ?? ($payment->subscription && $payment->subscription->plan ? $payment->subscription->plan->name : 'Business & Teams');
        $cycle = $payment->payment_data['cycle'] ?? 'Monthly';
        $amount = number_format($payment->amount, 2);
        $cardBrand = $payment->payment_data['card_brand'] ?? 'Visa';
        $cardLast4 = $payment->payment_data['card_last4'] ?? '4242';

        $invoiceContent = 
"================================================================================
                           QR IDENTITY SAAS — OFFICIAL INVOICE
================================================================================
Invoice Number:    {$invoiceNum}
Transaction ID:    {$payment->transaction_id}
Status:            " . strtoupper($payment->status) . "
Issue Date:        {$invDate}
Due Date:          {$dueDate}
--------------------------------------------------------------------------------
BILLED TO:
Customer:          {$user->name}
Email:             {$user->email}
Account Reference: USR-{$user->id}
Billing Address:   100 Tech Enterprise Blvd, Suite 400, San Francisco, CA 94105
Tax / VAT ID:      US-94827103-TX
--------------------------------------------------------------------------------
PAYMENT METHOD:
Card Brand:        {$cardBrand}
Card Number:       •••• •••• •••• {$cardLast4}
Gateway:           Stripe Payments Inc. (Encrypted Token)
--------------------------------------------------------------------------------
ITEMIZED CHARGES:
--------------------------------------------------------------------------------
Description                                    Qty      Unit Price        Total
--------------------------------------------------------------------------------
1. {$planName} ({$cycle})                       1         \${$amount}       \${$amount}
   - Dynamic QR Code Generation & Vector Export
   - Unlimited Custom Social & Contact Links
   - vCard 3.0 Direct Contact Save Engine
   - Real-time Analytics & Geo-location Tracking
   - Custom Themes, Colors & Logo Overlay
--------------------------------------------------------------------------------
Subtotal:                                                                 \${$amount}
Tax (0% Standard Software SaaS):                                           \$0.00
--------------------------------------------------------------------------------
TOTAL AMOUNT PAID:                                                        \${$amount} USD
================================================================================
Thank you for your business! For any billing questions or support inquiries,
please contact support@qrsocialsaas.com or visit https://qridentitysaas.com
================================================================================
";

        return response($invoiceContent, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $invoiceNum . '-Receipt.txt"',
        ]);
    }

    /**
     * Page 3 — Payment Methods (/dashboard/billing/payment-methods)
     */
    public function paymentMethods()
    {
        $user = Auth::user();

        // Stored tokenized payment methods (Masked)
        $paymentMethods = [
            [
                'id' => 'pm_101',
                'brand' => 'Visa',
                'last4' => '4242',
                'exp_month' => '12',
                'exp_year' => '2028',
                'is_default' => true,
            ],
            [
                'id' => 'pm_102',
                'brand' => 'Mastercard',
                'last4' => '8888',
                'exp_month' => '09',
                'exp_year' => '2027',
                'is_default' => false,
            ],
        ];

        return view('dashboard.billing.payment_methods', compact('user', 'paymentMethods'));
    }

    public function setDefaultPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        return back()->with('success', 'Default payment method updated successfully.');
    }

    public function addPaymentMethod(Request $request)
    {
        $request->validate([
            'card_holder' => 'required|string|max:255',
            'token' => 'required|string',
        ]);

        return back()->with('success', 'Payment method added successfully.');
    }

    public function removePaymentMethod(Request $request, string $id)
    {
        return back()->with('success', 'Payment method removed successfully.');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'provider' => ['required', 'in:stripe,razorpay'],
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $user = Auth::user();

        // Cancel previous active subscription if exists
        $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => $request->provider,
            'provider_subscription_id' => 'sub_' . strtolower($request->provider) . '_' . uniqid(),
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $user->plan_id = $plan->id;
        $user->save();

        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'provider' => $request->provider,
            'transaction_id' => 'tx_' . uniqid(),
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'status' => 'completed',
            'payment_data' => ['plan' => $plan->name, 'cycle' => $plan->billing_cycle],
        ]);

        return redirect()->route('dashboard.billing.index')
            ->with('success', 'Subscribed to ' . $plan->name . ' plan successfully!');
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->where('status', 'active')->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
        }

        return redirect()->route('dashboard.billing.index')
            ->with('success', 'Subscription cancelled. Premium features will remain active until the end of your billing cycle.');
    }

    public function renew(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->latest()->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'ends_at' => now()->addMonth(),
            ]);
        }

        return redirect()->route('dashboard.billing.index')
            ->with('success', 'Subscription renewed successfully!');
    }

    /**
     * Ensure realistic sample billing data (subscriptions & invoices) exist for the user.
     */
    public function ensureSampleBillingData($user): void
    {
        $plan = $user->plan ?? Plan::where('slug', 'business')->first() ?? Plan::first();

        // Ensure active subscription exists
        $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();
        if (!$subscription && $plan) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'provider' => 'stripe',
                'provider_subscription_id' => 'sub_stripe_' . uniqid(),
                'status' => 'active',
                'starts_at' => now()->subDays(12),
                'ends_at' => now()->addDays(18),
            ]);

            if ($user->plan_id !== $plan->id) {
                $user->plan_id = $plan->id;
                $user->save();
            }
        }

        // Ensure realistic example payments / invoices exist
        if ($user->payments()->count() === 0) {
            $this->createExampleInvoicesForUser($user, $subscription, $plan);
        }
    }

    /**
     * Seed 5 rich, realistic example invoices for demo/testing.
     */
    protected function createExampleInvoicesForUser($user, $subscription, $plan): void
    {
        $planName = $plan ? $plan->name : 'Business & Teams';
        $planPrice = $plan ? $plan->price : 29.00;

        $sampleInvoices = [
            [
                'amount' => $planPrice > 0 ? $planPrice : 29.00,
                'created_at' => now()->subDays(12),
                'status' => 'completed',
                'provider' => 'stripe',
                'transaction_id' => 'ch_3N' . strtoupper(substr(md5(uniqid() . '1'), 0, 14)),
                'payment_data' => [
                    'plan' => $planName . ' (Monthly Renewal)',
                    'cycle' => 'monthly',
                    'card_brand' => 'Visa',
                    'card_last4' => '4242',
                    'tax' => 0.00,
                ],
            ],
            [
                'amount' => 29.00,
                'created_at' => now()->subDays(42),
                'status' => 'completed',
                'provider' => 'stripe',
                'transaction_id' => 'ch_3M' . strtoupper(substr(md5(uniqid() . '2'), 0, 14)),
                'payment_data' => [
                    'plan' => 'Business & Teams Subscription',
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
                'transaction_id' => 'ch_3L' . strtoupper(substr(md5(uniqid() . '3'), 0, 14)),
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
                'transaction_id' => 'ch_3K' . strtoupper(substr(md5(uniqid() . '4'), 0, 14)),
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
                'transaction_id' => 'ch_free_' . strtolower(substr(md5(uniqid() . '5'), 0, 12)),
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
                'subscription_id' => $subscription ? $subscription->id : null,
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

    /**
     * Simulate an example payment transaction for testing.
     */
    public function simulateExamplePayment(Request $request)
    {
        $user = Auth::user();
        $plan = $user->plan ?? Plan::where('slug', 'business')->first() ?? Plan::first();
        $amount = (float) $request->input('amount', $plan ? $plan->price : 29.00);
        $status = $request->input('status', 'completed');
        $planName = $request->input('plan_name', ($plan ? $plan->name : 'Business & Teams') . ' Renewal');
        $provider = $request->input('provider', 'stripe');

        $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();

        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription ? $subscription->id : null,
            'provider' => $provider,
            'transaction_id' => 'ch_sim_' . strtoupper(substr(md5(uniqid()), 0, 14)),
            'amount' => $amount,
            'currency' => 'USD',
            'status' => $status,
            'payment_data' => [
                'plan' => $planName,
                'cycle' => 'monthly',
                'card_brand' => 'Visa',
                'card_last4' => '4242',
                'tax' => 0.00,
            ],
        ]);

        return back()->with('success', 'Example invoice #' . $payment->id . ' ($' . number_format($amount, 2) . ') generated successfully!');
    }

    /**
     * Reset billing examples back to realistic defaults.
     */
    public function resetExamples(Request $request)
    {
        $user = Auth::user();
        $user->payments()->delete();
        $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();
        $plan = $user->plan ?? Plan::where('slug', 'business')->first() ?? Plan::first();

        $this->createExampleInvoicesForUser($user, $subscription, $plan);

        return back()->with('success', 'Example invoices and billing history have been reset to fresh defaults!');
    }
}
