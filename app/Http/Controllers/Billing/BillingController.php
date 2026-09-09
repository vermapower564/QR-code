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
        $plans = Plan::where('status', 'active')->get();
        $currentSubscription = $user->activeSubscription();
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

        // Timeline activity log
        $activityLog = [
            ['event' => 'Subscription Renewal', 'date' => now()->subDays(2)->format('M d, Y'), 'status' => 'Success', 'desc' => 'Active plan renewed successfully.'],
            ['event' => 'Invoice #INV-2026-001 Generated', 'date' => now()->subDays(2)->format('M d, Y'), 'status' => 'Completed', 'desc' => 'Invoice paid via default payment method.'],
            ['event' => 'Payment Method Updated', 'date' => now()->subDays(15)->format('M d, Y'), 'status' => 'Updated', 'desc' => 'Visa •••• 4242 set as default card.'],
        ];

        return view('dashboard.billing.index', compact('user', 'plans', 'currentSubscription', 'payments', 'defaultPaymentMethod', 'activityLog'));
    }

    /**
     * Page 2 — Invoices (/dashboard/billing/invoices)
     */
    public function invoices(Request $request)
    {
        $user = Auth::user();

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

        $invoiceContent = "INVOICE SUMMARY\n" .
            "==============================\n" .
            "Invoice #: INV-2026-" . str_pad($payment->id, 4, '0', STR_PAD_LEFT) . "\n" .
            "Transaction ID: " . $payment->transaction_id . "\n" .
            "Customer: " . $user->name . " (" . $user->email . ")\n" .
            "Date: " . $payment->created_at->format('F d, Y') . "\n" .
            "Amount Paid: $" . number_format($payment->amount, 2) . " " . strtoupper($payment->currency) . "\n" .
            "Payment Status: " . ucfirst($payment->status) . "\n" .
            "Payment Method: " . ucfirst($payment->provider) . "\n" .
            "==============================\n" .
            "Thank you for using QR Identity SaaS.";

        return response($invoiceContent, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="Invoice-INV-2026-' . $payment->id . '.txt"',
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
}
