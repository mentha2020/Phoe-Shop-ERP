<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load('branch');

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        $oldData = $user->only(['name', 'email', 'phone']);

        $user->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        activity('profile')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['old' => $oldData, 'new' => $validated])
            ->log('Updated profile information');

        return Redirect::route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        activity('profile')
            ->performedOn($user)
            ->causedBy($user)
            ->log('Updated profile avatar');

        return Redirect::route('admin.profile.edit')->with('success', 'Avatar updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();
        $user->update(['password' => Hash::make($validated['password'])]);

        activity('profile')
            ->performedOn($user)
            ->causedBy($user)
            ->log('Changed password');

        return Redirect::route('admin.profile.edit')->with('success', 'Password changed successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
