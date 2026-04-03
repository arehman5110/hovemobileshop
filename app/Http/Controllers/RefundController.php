<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $data = $request->validate([
            'amount'        => 'required|numeric|min:0.01',
            'reason'        => 'nullable|string|max:255',
            'refund_method' => 'required|in:Cash,Card,Store Credit',
        ]);
        $data['job_id'] = $job->id;
        Refund::create($data);
        return back()->with('success', '↩️ Refund of £'.number_format($data['amount'],2).' recorded.');
    }

    public function destroy(Refund $refund)
    {
        $refund->delete();
        return back()->with('success', 'Refund removed.');
    }
}
