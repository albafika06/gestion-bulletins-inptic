<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    // Afficher le formulaire "mot de passe oublié"
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Traiter la demande de réinitialisation
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email'    => 'Adresse email invalide.',
        ]);

        $user = User::where('email', $request->email)
                    ->where('actif', 1)
                    ->first();

        // On affiche toujours le même message pour des raisons de sécurité
        if (!$user) {
            return back()->with('info',
                'Si cet email existe dans notre système, vous recevrez un lien de réinitialisation.'
            );
        }

        // Générer un token unique
        $token = Str::random(64);

        // Supprimer l'ancien token si existe
        DB::table('password_reset_tokens')
          ->where('email', $request->email)
          ->delete();

        // Insérer le nouveau token
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Pour cette version, on affiche le lien directement
        // En production, on enverrait un email
        $resetUrl = route('password.reset.form', [
            'token' => $token,
            'email' => $request->email,
        ]);

        return back()->with('reset_link', $resetUrl)
                     ->with('info', 'Lien de réinitialisation généré.');
    }

    // Afficher le formulaire de nouveau mot de passe
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Traiter le nouveau mot de passe
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'token'        => 'required',
            'mot_de_passe' => 'required|min:6|confirmed',
        ], [
            'mot_de_passe.required'   => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min'        => 'Minimum 6 caractères.',
            'mot_de_passe.confirmed'  => 'Les mots de passe ne correspondent pas.',
        ]);

        // Vérifier le token
        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Lien invalide ou expiré.']);
        }

        // Vérifier expiration (1 heure)
        if (Carbon::parse($record->created_at)->addHour()->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Ce lien a expiré. Faites une nouvelle demande.']);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        $user->mot_de_passe = Hash::make($request->mot_de_passe);
        $user->save();

        // Supprimer le token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
    }

    // Reset par l'admin depuis l'interface
    public function adminReset(Request $request, $id)
    {
        $request->validate([
            'nouveau_mot_de_passe' => 'required|min:6|confirmed',
        ], [
            'nouveau_mot_de_passe.required'  => 'Le mot de passe est obligatoire.',
            'nouveau_mot_de_passe.min'       => 'Minimum 6 caractères.',
            'nouveau_mot_de_passe.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::findOrFail($id);
        $user->mot_de_passe = Hash::make($request->nouveau_mot_de_passe);
        $user->save();

        return redirect()->route('utilisateurs.index')
            ->with('success', 'Mot de passe de ' . $user->nom_affichage . ' réinitialisé avec succès.');
    }
}