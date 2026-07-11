<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'logo' => 'nullable|image|mimes:jpeg,png,webp,gif|max:2048',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo', $path, 'general', 'text');
        }

        Cache::flush();

        return back()->with('success', 'Settings updated successfully!');
    }

    public function notifications()
    {
        $users = User::with('roles')->orderBy('name')->get();
        return view('admin.settings.notifications', compact('users'));
    }

    public function updateNotification(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'low_stock_alert' => 'boolean',
            'new_order_alert' => 'boolean',
            'daily_report' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ? '1' : '0', 'notifications', 'boolean');
        }

        return back()->with('success', 'Notification settings updated!');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }
}
