<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Normalize Email (trim & lowercase)
        if ($request->has('email')) {
            $request->merge([
                'email' => strtolower(trim($request->email))
            ]);
        }

        // Additional strict regex for double dots or trailing dots
        if (preg_match('/\.\./', $request->email) || preg_match('/@.*\.$/', $request->email)) {
            throw ValidationException::withMessages([
                'email' => ['Please enter a valid email address.'],
            ]);
        }

        // 2. Validate and Normalize Website / URL Link
        $trimmedWebsite = trim((string) $request->input('website', ''));
        if (empty($trimmedWebsite)) {
            throw ValidationException::withMessages([
                'website' => ['Please enter a valid website URL.'],
            ]);
        }

        // Auto-prepend https:// if scheme is missing (e.g. example.com -> https://example.com)
        if (!preg_match('~^https?://~i', $trimmedWebsite)) {
            $trimmedWebsite = 'https://' . $trimmedWebsite;
        }

        $linkValidator = app(\App\Services\LinkValidationService::class);
        if (!$linkValidator->isValidUrl($trimmedWebsite)) {
            throw ValidationException::withMessages([
                'website' => ['Please enter a valid website URL.'],
            ]);
        }
        $request->merge(['website' => $trimmedWebsite]);

        $messages = [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'An account with this email already exists.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'website.required' => 'Please enter a valid website URL.',
            'website.url' => 'Please enter a valid website URL.',
        ];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'website' => ['nullable', 'string', 'max:2048'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], $messages);

        $freePlan = Plan::where('slug', 'free')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'website' => $request->website,
            'company' => $request->company,
            'plan_id' => $freePlan ? $freePlan->id : null,
            'role' => 'user',
            'status' => 'active',
            'onboarding_completed' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('onboarding.index')
            ->with('success', 'Account created successfully! Welcome to QR Identity. Complete your first dynamic profile below.');
    }
}
