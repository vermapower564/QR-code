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

        // 2. Validate and Normalize Website / URL Link (Optional)
        if ($request->filled('website')) {
            $rawWebsite = (string) $request->input('website', '');
            $linkValidator = app(\App\Services\LinkValidationService::class);
            $normalizedWebsite = $linkValidator->normalizeUrl($rawWebsite);

            if (!$linkValidator->isValidUrl($normalizedWebsite)) {
                throw ValidationException::withMessages([
                    'website' => ['Please enter a valid website URL.'],
                ]);
            }
            $request->merge(['website' => $normalizedWebsite]);
        } else {
            $request->merge(['website' => null]);
        }

        $messages = [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'An account with this email already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'website.url' => 'Please enter a valid website URL.',
            'phone.regex' => 'Mobile number must be strictly 10 digits (0-9).',
        ];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'website' => ['nullable', 'string', 'max:2048'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/'],
        ], $messages);

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            if ($existingUser->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'email' => ['An account with this email already exists. Please log in.'],
                ]);
            } else {
                $key = 'resend_verification_' . $existingUser->id;
                if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
                    throw ValidationException::withMessages([
                        'email' => ['Please wait 60 seconds before requesting another verification email.'],
                    ]);
                }
                \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

                $existingUser->sendEmailVerificationNotification();

                return back()->with('success', 'This account already exists but is not verified. We have sent a new verification email.');
            }
        }

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
