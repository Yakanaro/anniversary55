<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HostSessionController extends Controller
{
    /**
     * Show the guest list password form.
     */
    public function create(): View
    {
        return view('host.login');
    }

    /**
     * Let the host in when the password matches.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            ['password' => ['required', 'string']],
            ['password.required' => 'Введите пароль.'],
        );

        $hostPassword = (string) config('invitation.host_password');

        if ($hostPassword === '' || ! hash_equals($hostPassword, $validated['password'])) {
            return back()->withErrors(['password' => 'Неверный пароль.']);
        }

        $request->session()->regenerate();
        $request->session()->put('host_authenticated', true);

        return redirect()->route('guests.index');
    }

    /**
     * Log the host out.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('host_authenticated');
        $request->session()->regenerate();

        return redirect()->route('host.login');
    }
}
