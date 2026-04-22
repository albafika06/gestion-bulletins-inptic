<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->actif) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['login' => 'Votre compte a été désactivé.']);
        }

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé.');
    }
}