<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:buyer,seller'
        ]);

        $status = $request->role === 'seller' ? 'pending' : 'active';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $status
        ]);

        Auth::login($user);

        if ($request->role === 'seller') {
            return redirect()->route('auth.pending');
        }

        return redirect()->route('buyer.dashboard');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember_me');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'seller') {
                if ($user->status == 'active' && $user->seller && $user->seller->is_verified) {
                     return redirect()->route('seller.dashboard');
                }
                return redirect()->route('seller.status');
            }

            return redirect()->route('buyer.home');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'Anda telah logout dengan aman.');
    }

    public function showPendingPage() {
        return view('auth.pending');
    }

    public function suspended()
    {
        return view('auth.suspended');
    }
}
