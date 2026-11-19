<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($credentials['login']);
        $password   = $credentials['password'];
        $remember   = $request->boolean('remember');

        // Allow logging in via either email or username/name
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $primaryField = $isEmail ? 'email' : 'name';
        $fallbackField = $isEmail ? 'name' : 'email';

        if (Auth::attempt([$primaryField => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Logged in successfully. Welcome back!');
        }

        if (Auth::attempt([$fallbackField => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Logged in successfully. Welcome back!');
        }

        throw ValidationException::withMessages([
            'login' => 'Invalid credentials. Please verify your email/username and password.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been signed out successfully.');
    }
}
