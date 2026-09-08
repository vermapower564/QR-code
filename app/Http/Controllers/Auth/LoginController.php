<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Email Normalization (trim & lowercase)
        if ($request->has('email')) {
            $request->merge([
                'email' => strtolower(trim($request->email))
            ]);
        }

        // 2. Strict Input Validation
        $messages = [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
        ];

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns'],
            'password' => ['required', 'string'],
        ], $messages);

        // Additional strict regex for double dots or trailing dots
        if (preg_match('/\.\./', $request->email) || preg_match('/@.*\.$/', $request->email)) {
            throw ValidationException::withMessages([
                'email' => ['Please enter a valid email address.'],
            ]);
        }

        // 3. Rate Limiting / Brute-Force Protection
        $throttleKey = Str::transliterate(strtolower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        // 4. Attempt Authentication
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            if (Auth::user()->status === 'suspended') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
            }

            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin');
            }

            if (!Auth::user()->onboarding_completed) {
                return redirect()->route('onboarding.index');
            }

            return redirect()->intended('/dashboard');
        }

        // 5. Increment Rate Limiter on Failed Attempt
        RateLimiter::hit($throttleKey, 60);

        // 6. Generic Authentication Error (Prevents Account Enumeration)
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
