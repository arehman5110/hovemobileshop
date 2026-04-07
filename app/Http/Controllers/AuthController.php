<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── User Management ───────────────────────────────────────────

    public function users()
    {
        $users = User::orderBy('name')->get();
        return view('auth.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:' . implode(',', UserRole::all()),
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return back()->with('success', '✅ User '.$data['name'].' created as '.$data['role'].'.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'role'     => 'required|in:' . implode(',', UserRole::all()),
        ]);

        // Prevent demoting yourself from admin
        if ($user->id === auth()->id() && $data['role'] !== 'admin' && auth()->user()->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', '❌ Cannot change role — you are the only admin.');
            }
        }

        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return back()->with('success', '✅ User '.$user->name.' updated.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:' . implode(',', UserRole::all()),
        ]);

        // Prevent removing last admin
        if ($user->role === 'admin' && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', '❌ Cannot change role — at least one admin must exist.');
            }
        }

        $user->update(['role' => $request->role]);
        return back()->with('success', '✅ Role updated to '.UserRole::config($request->role)['label'].'.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', '❌ You cannot delete your own account.');
        }

        // Prevent deleting last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', '❌ Cannot delete the only admin account.');
        }

        $name = $user->name;
        $user->delete();
        return back()->with('success', '✅ User '.$name.' deleted.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', '❌ Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', '✅ Password changed successfully.');
    }

    // ── Per-user permission overrides ─────────────────────────────
    public function savePermissions(Request $request, User $user)
    {
        $allPermissions = array_keys(\App\Helpers\UserRole::PERMISSIONS);
        $submitted      = $request->input('permissions', []);

        foreach ($allPermissions as $permission) {
            $roleDefault = \App\Helpers\UserRole::can($user->role ?? 'viewer', $permission);
            $submitted_val = in_array($permission, $submitted); // true if checkbox checked

            if ($submitted_val === $roleDefault) {
                // Same as role default — remove override (keep it clean)
                \App\Models\UserPermission::where('user_id', $user->id)
                    ->where('permission', $permission)
                    ->delete();
            } else {
                // Different from role default — save override
                \App\Models\UserPermission::updateOrCreate(
                    ['user_id' => $user->id, 'permission' => $permission],
                    ['granted' => $submitted_val]
                );
            }
        }

        $user->clearPermissionCache();
        return back()->with('success', '✅ Permissions updated for '.$user->name.'.');
    }
}