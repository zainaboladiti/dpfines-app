<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect('/admin/dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8',
        ]);

        // Attempt authentication
        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Access denied. Admin account required.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect('/admin/dashboard')->with('success', 'Successfully logged in.');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        // Only allow registration if no admins exist (first admin setup)
        $adminCount = User::admins()->count();
        
        if ($adminCount > 0 && !Auth::check()) {
            abort(403, 'Registration is closed.');
        }

        return view('admin.auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // Only allow registration if no admins exist
        if (User::admins()->count() > 0 && !Auth::check()) {
            abort(403, 'Registration is closed.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true, // First admin or registration by existing admin
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/admin/dashboard')->with('success', 'Account created successfully.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
