<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('features')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:plans,slug',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|in:monthly,yearly',
            'profile_limit' => 'required|integer',
            'link_limit' => 'required|integer',
        ]);

        $plan = Plan::create([
            'name' => $request->name,
            'slug' => strtolower($request->slug),
            'price' => $request->price,
            'currency' => strtoupper($request->currency),
            'billing_cycle' => $request->billing_cycle,
            'profile_limit' => $request->profile_limit,
            'link_limit' => $request->link_limit,
            'status' => 'active',
        ]);

        // Create default feature flags
        $defaultFeatures = [
            'custom_qr' => $request->price > 0 ? 'true' : 'false',
            'qr_logo' => $request->price > 0 ? 'true' : 'false',
            'advanced_analytics' => $request->price > 0 ? 'true' : 'false',
            'remove_branding' => $request->price > 0 ? 'true' : 'false',
            'custom_links' => 'true',
            'contact_card' => 'true',
        ];

        foreach ($defaultFeatures as $key => $val) {
            PlanFeature::create([
                'plan_id' => $plan->id,
                'feature_key' => $key,
                'feature_value' => $val,
            ]);
        }

        return back()->with('success', 'Plan created successfully.');
    }

    public function updateFeatures(Request $request, int $planId)
    {
        $plan = Plan::findOrFail($planId);

        $request->validate([
            'features' => 'required|array',
            'features.*.key' => 'required|string',
            'features.*.value' => 'required|string',
        ]);

        foreach ($request->features as $item) {
            PlanFeature::updateOrCreate(
                ['plan_id' => $plan->id, 'feature_key' => $item['key']],
                ['feature_value' => $item['value']]
            );
        }

        // Flush application cache
        Cache::flush();

        return back()->with('success', "Features for {$plan->name} updated successfully.");
    }
}
