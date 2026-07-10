<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match ($user->status) {
                'inactive' => 'Your account is inactive. Please contact an administrator.',
                'suspended' => 'Your account has been suspended. Please contact an administrator.',
                default => 'Your account is not available.',
            };

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        activity('auth')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip(), 'user_agent' => $request->userAgent()])
            ->log('User logged in');

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        activity('auth')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('User logged out');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
