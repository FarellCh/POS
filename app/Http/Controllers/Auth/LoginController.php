<?php

namespace App\Http\Controllers\Auth;

use App\Domains\Account\Models\CashierSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user?->role === 'cashier') {
            CashierSession::create([
                'user_id' => $user->id,
                'session_id' => $request->session()->getId(),
                'started_at' => Carbon::now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->intended(route('kasir.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->role === 'cashier') {
            $activeSession = CashierSession::query()
                ->where('user_id', $user->id)
                ->where('session_id', $request->session()->getId())
                ->whereNull('ended_at')
                ->latest('started_at')
                ->first();

            if ($activeSession) {
                $endedAt = Carbon::now();
                $durationSeconds = (int) $activeSession->started_at?->diffInSeconds($endedAt);
                $activeSession->update([
                    'ended_at' => $endedAt,
                    'duration_seconds' => $durationSeconds,
                ]);
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
