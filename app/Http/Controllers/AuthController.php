<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect('/');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !$user->is_active) {
            return back()->withInput()->withErrors(['email' => 'Invalid credentials or account disabled.']);
        }

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withInput()->withErrors(['email' => 'Invalid email or password.']);
        }

        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── User management (admin only) ──────────────────────────────
    public function users()
    {
        $users = User::latest()->get();
        return view('auth.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,staff',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return back()->with('success', 'User created!');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'role'      => 'required|in:admin,staff',
            'is_active' => 'nullable|boolean',
            'password'  => 'nullable|string|min:6',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $user->update($data);
        return back()->with('success', 'User updated!');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) return back()->with('error', 'Cannot delete your own account.');
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    // Change own password
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($data['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($data['new_password'])]);
        return back()->with('success', '✅ Password changed!');
    }
}
