<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\ResultatAnnuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtudiantController extends Controller
{
    public function index()
    {
        $annee     = config('app.annee_courante', '2025/2026');
        $etudiants = Etudiant::where('annee_universitaire', $annee)
                             ->where('actif', 1)
                             ->orderBy('nom')
                             ->get();
        return view('etudiants.index', compact('etudiants', 'annee'));
    }

    public function create()
    {
        return view('etudiants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricule' => 'required|unique:etudiants,matricule|max:30',
            'nom'       => 'required|max:100',
            'prenom'    => 'required|max:150',
            'date_naissance'        => 'nullable|date',
            'lieu_naissance'        => 'nullable|max:100',
            'sexe'                  => 'nullable|in:M,F',
            'type_bac'              => 'nullable|max:50',
            'etablissement_origine' => 'nullable|max:200',
        ], [
            'matricule.required' => 'Le matricule est obligatoire.',
            'matricule.unique'   => 'Ce matricule existe déjà.',
            'nom.required'       => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
        ]);

        Etudiant::create([
            'matricule'             => strtoupper($request->matricule),
            'nom'                   => strtoupper($request->nom),
            'prenom'                => ucwords(strtolower($request->prenom)),
            'date_naissance'        => $request->date_naissance,
            'lieu_naissance'        => $request->lieu_naissance,
            'sexe'                  => $request->sexe,
            'type_bac'              => $request->type_bac,
            'etablissement_origine' => $request->etablissement_origine,
            'annee_universitaire'   => config('app.annee_courante', '2025/2026'),
            'actif'                 => 1,
        ]);

        return redirect()->route('etudiants.index')
                         ->with('success', 'Étudiant ajouté avec succès.');
    }

    public function show($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        return view('etudiants.show', compact('etudiant'));
    }

    public function edit($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        return view('etudiants.edit', compact('etudiant'));
    }

    public function update(Request $request, $id)
    {
        $etudiant = Etudiant::findOrFail($id);

        $request->validate([
            'matricule' => 'required|max:30|unique:etudiants,matricule,' . $id,
            'nom'       => 'required|max:100',
            'prenom'    => 'required|max:150',
            'date_naissance'        => 'nullable|date',
            'lieu_naissance'        => 'nullable|max:100',
            'sexe'                  => 'nullable|in:M,F',
            'type_bac'              => 'nullable|max:50',
            'etablissement_origine' => 'nullable|max:200',
        ]);

        $etudiant->update([
            'matricule'             => strtoupper($request->matricule),
            'nom'                   => strtoupper($request->nom),
            'prenom'                => ucwords(strtolower($request->prenom)),
            'date_naissance'        => $request->date_naissance,
            'lieu_naissance'        => $request->lieu_naissance,
            'sexe'                  => $request->sexe,
            'type_bac'              => $request->type_bac,
            'etablissement_origine' => $request->etablissement_origine,
        ]);

        return redirect()->route('etudiants.index')
                         ->with('success', 'Étudiant modifié avec succès.');
    }

    public function destroy($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $etudiant->update(['actif' => 0]);
        return redirect()->route('etudiants.index')
                         ->with('success', 'Étudiant supprimé avec succès.');
    }

    // ─────────────────────────────────────────────────
    // ESPACE ÉTUDIANT — Mes informations
    // ─────────────────────────────────────────────────
    public function mesInformations()
    {
        $user     = Auth::user();
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun dossier associé à votre compte.');
        }

        $annee          = config('app.annee_courante', '2025/2026');
        $resultatAnnuel = ResultatAnnuel::where('etudiant_id', $etudiant->id)
                            ->where('annee_univ', $annee)->first();

        return view('etudiant.informations', compact('etudiant', 'resultatAnnuel', 'annee'));
    }
}