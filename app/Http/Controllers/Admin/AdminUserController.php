<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('plan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $plans = Plan::all();

        return view('admin.users.index', compact('users', 'plans'));
    }

    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        $user->status = ($user->status === 'active') ? 'suspended' : 'active';
        $user->save();

        return back()->with('success', 'User status changed to ' . $user->status);
    }

    public function updatePlan(Request $request, int $id)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        $user = User::findOrFail($id);
        $user->plan_id = $request->plan_id;
        $user->save();

        return back()->with('success', 'User plan updated successfully.');
    }
}
