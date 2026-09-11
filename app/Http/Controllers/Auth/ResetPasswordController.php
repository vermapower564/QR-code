<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['request' => $request, 'token' => $token]);
    }

    public function reset(Request $request)
    {
        if ($request->has('email')) {
            $request->merge([
                'email' => strtolower(trim($request->email))
            ]);
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !\Illuminate\Support\Facades\Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => ['Invalid or expired reset token.']]);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => ['We can\'t find a user with that email address.']]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        session()->forget('reset_email');

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
