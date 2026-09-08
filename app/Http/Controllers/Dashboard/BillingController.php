<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $plans = Plan::where('status', 'active')->get();
        $currentSubscription = $user->activeSubscription();
        $payments = $user->payments()->latest()->take(10)->get();

        return view('dashboard.billing.index', compact('user', 'plans', 'currentSubscription', 'payments'));
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
}
