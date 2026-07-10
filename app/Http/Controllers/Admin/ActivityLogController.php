<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');

        if ($request->filled('causer')) {
            $query->where('causer_id', $request->causer);
        }

        if ($request->filled('type')) {
            $query->where('log_name', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        $activities = $query->latest()->paginate(30);
        $causers = \App\Models\User::select('id', 'name')->orderBy('name')->get();
        $logTypes = Activity::distinct()->pluck('log_name')->filter()->values();

        return view('admin.activity-log', compact('activities', 'causers', 'logTypes'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $deleted = Activity::where('created_at', '<=', $request->date_to . ' 23:59:59')
            ->where('created_at', '>=', $request->date_from)
            ->delete();

        activity('system')
            ->causedBy(auth()->user())
            ->withProperties(['date_from' => $request->date_from, 'date_to' => $request->date_to, 'count' => $deleted])
            ->log('Purged ' . $deleted . ' activity log entries');

        return redirect()->route('admin.activity-log')->with('success', "Deleted {$deleted} activity log entries.");
    }
}
