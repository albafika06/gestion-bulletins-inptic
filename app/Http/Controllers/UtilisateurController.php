<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Etudiant;
use App\Models\EnseignantMatiere;
use App\Models\Matiere;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs = User::orderBy('role')->orderBy('nom_affichage')->get();
        return view('utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        $annee     = config('app.annee_courante', '2025/2026');
        $etudiants = Etudiant::where('actif', 1)->where('annee_universitaire', $annee)->orderBy('nom')->get();
        return view('utilisateurs.create', compact('etudiants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'login'         => 'required|unique:utilisateurs,login|max:80',
            'mot_de_passe'  => 'required|min:6|confirmed',
            'nom_affichage' => 'required|max:200',
            'email'         => 'nullable|email|unique:utilisateurs,email',
            'role'          => 'required|in:ADMIN,ENSEIGNANT,SECRETARIAT,ETUDIANT',
            'etudiant_id'   => 'nullable|exists:etudiants,id',
        ]);

        $user = User::create([
            'login'         => $request->login,
            'mot_de_passe'  => Hash::make($request->mot_de_passe),
            'nom_affichage' => $request->nom_affichage,
            'email'         => $request->email,
            'role'          => $request->role,
            'etudiant_id'   => $request->role == 'ETUDIANT' ? $request->etudiant_id : null,
            'actif'         => 1,
        ]);

        JournalController::log(
            'UTILISATEUR_CREE',
            'Utilisateur créé : ' . $user->nom_affichage,
            auth()->id(),
            'Rôle : ' . $user->role
        );

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show($id)
    {
        return view('utilisateurs.show', ['utilisateur' => User::findOrFail($id)]);
    }

    public function edit($id)
    {
        $utilisateur = User::findOrFail($id);
        $annee       = config('app.annee_courante', '2025/2026');
        $etudiants   = Etudiant::where('actif', 1)->where('annee_universitaire', $annee)->orderBy('nom')->get();
        return view('utilisateurs.edit', compact('utilisateur', 'etudiants'));
    }

    public function update(Request $request, $id)
    {
        $utilisateur = User::findOrFail($id);

        $request->validate([
            'login'         => 'required|max:80|unique:utilisateurs,login,' . $id,
            'nom_affichage' => 'required|max:200',
            'email'         => 'nullable|email|unique:utilisateurs,email,' . $id,
            'role'          => 'required|in:ADMIN,ENSEIGNANT,SECRETARIAT,ETUDIANT',
            'etudiant_id'   => 'nullable|exists:etudiants,id',
            'mot_de_passe'  => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'login'         => $request->login,
            'nom_affichage' => $request->nom_affichage,
            'email'         => $request->email,
            'role'          => $request->role,
            'etudiant_id'   => $request->role == 'ETUDIANT' ? $request->etudiant_id : null,
        ];

        if ($request->filled('mot_de_passe')) {
            $data['mot_de_passe'] = Hash::make($request->mot_de_passe);
        }

        $utilisateur->update($data);

        JournalController::log('UTILISATEUR_MODIFIE', 'Utilisateur modifié : ' . $utilisateur->nom_affichage, auth()->id());

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy($id)
    {
        $utilisateur = User::findOrFail($id);

        if ($utilisateur->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $utilisateur->update(['actif' => 0]);

        JournalController::log('UTILISATEUR_DESACTIVE', 'Compte désactivé : ' . $utilisateur->nom_affichage, auth()->id());

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur désactivé.');
    }

    public function affecterMatieres($id)
    {
        $utilisateur = User::findOrFail($id);

        if ($utilisateur->role !== 'ENSEIGNANT') {
            return redirect()->route('utilisateurs.index')
                ->with('error', 'Seuls les enseignants peuvent avoir des matières assignées.');
        }

        $annee             = config('app.annee_courante', '2025/2026');
        $semestres         = \App\Models\Semestre::with([
            'unitesEnseignement.matieres' => fn($q) => $q->where('actif', 1)->orderBy('ordre')
        ])->orderBy('ordre')->get();

        $matieresAssignees = EnseignantMatiere::where('utilisateur_id', $id)
                                ->where('annee_univ', $annee)
                                ->pluck('matiere_id')->toArray();

        return view('utilisateurs.affecter-matieres', compact(
            'utilisateur', 'semestres', 'matieresAssignees', 'annee'
        ));
    }

    public function sauvegarderMatieres(Request $request, $id)
    {
        $utilisateur = User::findOrFail($id);
        $annee       = config('app.annee_courante', '2025/2026');

        EnseignantMatiere::where('utilisateur_id', $id)->where('annee_univ', $annee)->delete();

        $matiereLibelles = '';

        if ($request->has('matieres')) {
            foreach ($request->matieres as $matiereId) {
                EnseignantMatiere::create([
                    'utilisateur_id' => $id,
                    'matiere_id'     => (int) $matiereId,
                    'annee_univ'     => $annee,
                    'releve_publie'  => false,
                ]);
            }

            $matiereLibelles = Matiere::whereIn('id', $request->matieres)
                                ->pluck('libelle')->join(', ');

            // Notification à l'enseignant
            Notification::creer(
                $id,
                'MATIERE_ASSIGNEE',
                '📚 Nouvelles matières assignées',
                'L\'administrateur vous a assigné les matières suivantes pour ' . $annee . ' : ' . $matiereLibelles,
                route('dashboard')
            );
        }

        // Journal
        JournalController::log(
            'MATIERE_ASSIGNEE',
            'Matières assignées à ' . $utilisateur->nom_affichage,
            auth()->id(),
            'Matières : ' . ($matiereLibelles ?: 'Aucune')
        );

        return redirect()->route('utilisateurs.index')
            ->with('success', 'Matières assignées à ' . $utilisateur->nom_affichage . ' avec succès.');
    }
}