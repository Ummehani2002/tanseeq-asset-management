<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Show Login Page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Login User
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'Logged in successfully');
        }

        return back()->withErrors(['username' => 'Invalid username or password.']);
    }

    // Show Register Page
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Register User
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('login')->with('success', 'User registered successfully. Please login.');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
