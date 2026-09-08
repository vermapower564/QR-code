<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['plan', 'profiles']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        $users = $query->latest()->paginate(15);
        $plans = Plan::all();

        return view('admin.users.index', compact('users', 'plans'));
    }

    public function show(int $id)
    {
        $user = User::with(['plan', 'profiles.qrCode', 'subscriptions', 'payments'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $oldStatus = $user->status ?? 'active';
        $newStatus = ($oldStatus === 'active') ? 'suspended' : 'active';
        $user->status = $newStatus;
        $user->save();

        // Also update profiles status if user suspended
        if ($newStatus === 'suspended') {
            $user->profiles()->update(['status' => 'suspended']);
        } else {
            $user->profiles()->update(['status' => 'active']);
        }

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'user.status_toggle',
            'User',
            $user->id,
            "User {$user->email} status changed from {$oldStatus} to {$newStatus}",
            ['old_status' => $oldStatus, 'new_status' => $newStatus],
            $request->ip()
        );

        return back()->with('success', "User status updated to {$newStatus}.");
    }

    public function updatePlan(Request $request, int $id)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        $user = User::findOrFail($id);
        $oldPlanId = $user->plan_id;
        $user->plan_id = $request->plan_id;
        $user->save();

        $plan = Plan::find($request->plan_id);

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'user.plan_update',
            'User',
            $user->id,
            "Updated plan for user {$user->email} to {$plan->name}",
            ['old_plan_id' => $oldPlanId, 'new_plan_id' => $request->plan_id],
            $request->ip()
        );

        return back()->with('success', 'User plan updated successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $email = $user->email;

        // Deactivate profiles
        $user->profiles()->update(['status' => 'inactive']);
        $user->delete();

        AuditLogService::log(
            $request->user()?->id ?? 1,
            'user.soft_delete',
            'User',
            $id,
            "Soft deleted user {$email}",
            [],
            $request->ip()
        );

        return redirect()->route('admin.users.index')->with('success', "User {$email} deleted successfully.");
    }
}

