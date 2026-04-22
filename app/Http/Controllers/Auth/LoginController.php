<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\JournalAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'        => 'required',
            'mot_de_passe' => 'required',
        ], [
            'login.required'        => 'Le login est obligatoire.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
        ]);

        $user = User::where('login', $request->login)->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Identifiants incorrects.'])->withInput();
        }
        if (!$user->actif) {
            return back()->withErrors(['login' => 'Votre compte a été désactivé.'])->withInput();
        }
        if (!Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            return back()->withErrors(['login' => 'Identifiants incorrects.'])->withInput();
        }

        Auth::login($user);
        $user->update(['derniere_connexion' => now()]);

        // ✅ Log connexion pour TOUS les rôles (admin inclus)
        JournalAudit::log(
            'CONNEXION',
            $user->nom_affichage . ' s\'est connecté(e)',
            $user->id,
            $request->ip()
        );

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // ✅ Log déconnexion pour TOUS les rôles
        if (Auth::check()) {
            JournalAudit::log(
                'DECONNEXION',
                Auth::user()->nom_affichage . ' s\'est déconnecté(e)',
                Auth::id(),
                request()->ip()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}