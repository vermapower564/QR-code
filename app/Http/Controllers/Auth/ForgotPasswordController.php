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
            'email' => ['required', 'email:rfc,dns', 'exists:users,email']
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'No account found with this email address.'
        ]);

        $throttleKey = 'forgot-password|' . Str::transliterate($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many password reset attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        $otp = rand(100000, 999999);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($otp), 'created_at' => now()]
        );

        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\ResetPasswordOtpMail($otp));

        session()->put('reset_email', $request->email);

        return redirect()->route('password.otp')->with('status', 'A 6-digit code has been sent to your email.');
    }

    public function showOtpForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($record && \Illuminate\Support\Facades\Hash::check($request->otp, $record->token)) {
            // Check expiration (e.g. 15 mins)
            if (\Carbon\Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
                return back()->withErrors(['otp' => 'This code has expired. Please request a new one.']);
            }
            return redirect()->route('password.reset', ['token' => $request->otp, 'email' => $email]);
        }

        return back()->withErrors(['otp' => 'The entered code is incorrect.']);
    }
}
