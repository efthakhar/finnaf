<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Database\Seeders\DevDemo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect('admin');
        }

        return view('auth.login-form');
    }

    public function login(Request $request)
    {

        $seeder = new DevDemo();
        $seeder->run();

        $visitor = Visitor::where('ip', $request->ip())->first();

        if ($visitor) {
            $visitor->increment('count');
        } else {
            Visitor::create([
                'ip' => $request->ip(),
                'count' => 1,
            ]);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, 100)) {
            $request->session()->regenerate();
            redirect()->route('home');
        }

        return back()->with('login-error', 'Incorrect Credentials Provided')->onlyInput('email', 'password');
    }

    public function logout(Request $request)
    {

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('loginForm');

    }
}
