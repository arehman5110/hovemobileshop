<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Job;
use App\Models\Part;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalParts     = Part::all()->sum(fn($p) => $p->remainingStock());
        $totalCustomers = Customer::count();
        $totalJobs      = Job::count();
        $activeJobs     = Job::where('status', 'In Progress')->count();
        $pendingJobs    = Job::where('status', 'Waiting Parts')->count();
        $completedJobs  = Job::where('status', 'Completed')->count();
        $lowStockParts  = Part::all()->filter(fn($p) => $p->remainingStock() <= ($p->low_stock_threshold ?? 2))->count();
        $todayRevenue   = Payment::whereDate('created_at', today())->sum('amount');

        $recentJobs = Job::with(['customer', 'devices.repairItems.repairType', 'payments'])
                        ->latest()->take(8)->get();

        return view('dashboard.index', compact(
            'totalParts', 'totalCustomers', 'totalJobs',
            'activeJobs', 'pendingJobs', 'completedJobs',
            'lowStockParts', 'todayRevenue', 'recentJobs'
        ));
    }
}