<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
        ];

        $recentActivity = Activity::with('causer')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'recentActivity'));
    }
}
