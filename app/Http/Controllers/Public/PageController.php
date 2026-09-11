<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function pricing()
    {
        $plans = Plan::where('status', 'active')->get();
        return view('pages.pricing', compact('plans'));
    }

    public function features()
    {
        return view('pages.features');
    }

    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Thank you for reaching out! Our support team will get back to you shortly.');
    }

    public function terms()
    {
        return view('pages.legal', ['title' => 'Terms & Conditions', 'type' => 'terms']);
    }

    public function privacy()
    {
        return view('pages.legal', ['title' => 'Privacy Policy', 'type' => 'privacy']);
    }

    public function cookiePolicy()
    {
        return view('pages.legal', ['title' => 'Cookie Policy', 'type' => 'cookie']);
    }

    public function refundPolicy()
    {
        return view('pages.legal', ['title' => 'Refund Policy', 'type' => 'refund']);
    }
}
