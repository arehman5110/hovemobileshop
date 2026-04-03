<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $request->validate([
            'payment_type' => 'required|in:Cash,Card,Trade,Split',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string',
        ]);

        // Split: store as a SINGLE record with JSON notes so it shows/edits as one unit
        Payment::create([
            'job_id'       => $job->id,
            'payment_type' => $request->payment_type,
            'amount'       => $request->amount,
            'notes'        => $request->notes,
        ]);

        return back()->with('success', 'Payment recorded.');
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_type' => 'required|in:Cash,Card,Trade,Split',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string',
        ]);

        $payment->update([
            'payment_type' => $request->payment_type,
            'amount'       => $request->amount,
            'notes'        => $request->notes,
        ]);

        return back()->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back()->with('success', 'Payment removed.');
    }
}