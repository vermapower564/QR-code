<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'subscription'])->latest()->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }
}
