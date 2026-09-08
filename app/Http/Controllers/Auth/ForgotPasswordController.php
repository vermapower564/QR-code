<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        if ($request->has('email')) {
            $request->merge([
                'email' => strtolower(trim($request->email))
            ]);
        }

        $request->validate([
            'email' => ['required', 'email:rfc,dns']
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.'
        ]);

        $throttleKey = 'forgot-password|' . Str::transliterate($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many password reset attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        Password::sendResetLink($request->only('email'));

        // Generic security response preventing account enumeration
        return back()->with('status', 'If an account exists for this email, password reset instructions have been sent.');
    }
}
