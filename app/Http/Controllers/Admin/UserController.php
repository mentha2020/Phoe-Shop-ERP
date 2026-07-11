<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('branch', 'roles');

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();
        $branches = Branch::active()->get();

        return view('admin.user.index', compact('users', 'roles', 'branches'));
    }

    public function create()
    {
        $roles = Role::all();
        $branches = Branch::active()->get();

        return view('admin.user.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        activity('user')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['role' => $validated['role'], 'status' => $validated['status']])
            ->log('Created user: ' . $user->name);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('branch', 'roles', 'permissions');
        $activityLogs = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.user.show', compact('user', 'activityLogs'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $branches = Branch::active()->get();
        $user->load('roles');

        return view('admin.user.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $oldData = $user->only(['name', 'email', 'phone', 'status']);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        activity('user')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['old' => $oldData, 'new' => $data, 'role' => $validated['role']])
            ->log('Updated user: ' . $user->name);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        activity('user')
            ->causedBy(auth()->user())
            ->withProperties(['deleted_user' => $userName])
            ->log('Deleted user: ' . $userName);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
