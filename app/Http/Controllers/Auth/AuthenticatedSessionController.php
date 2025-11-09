<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $remember = $request->filled('remember');

            if (Auth::attempt($credentials, $remember)) {
                
                if (Auth::user()->type != 'unauthorized') {
                    $request->session()->put('company_id', Auth::user()->company_id);
                    $request->session()->regenerate();
                    return redirect()->intended(RouteServiceProvider::HOME);
                }
                Auth::logout();
                return back()->with('error', 'You do not have authorization. Please contact the administrator.');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput()->with('error', 'Login failed. Please check your credentials.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput()->with('error', 'Login failed. Please check your credentials.');
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
