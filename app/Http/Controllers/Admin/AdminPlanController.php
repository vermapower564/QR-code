<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

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

        Plan::create([
            'name' => $request->name,
            'slug' => strtolower($request->slug),
            'price' => $request->price,
            'currency' => strtoupper($request->currency),
            'billing_cycle' => $request->billing_cycle,
            'profile_limit' => $request->profile_limit,
            'link_limit' => $request->link_limit,
            'status' => 'active',
        ]);

        return back()->with('success', 'Plan created successfully.');
    }
}
