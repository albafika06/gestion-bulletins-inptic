<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Etudiant;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $annee = config('app.annee_courante', '2025/2026');

        $query = Absence::with(['etudiant', 'matiere.ue.semestre'])
                    ->where('annee_univ', $annee)
                    ->where('heures', '>', 0);

        if ($request->filled('statut')) {
            $query->where('justifie', $request->statut == 'justifie' ? 1 : 0);
        }
        if ($request->filled('semestre')) {
            $query->whereHas('matiere.ue.semestre', fn($q) =>
                $q->where('code', $request->semestre)
            );
        }
        if ($request->filled('search')) {
            $query->whereHas('etudiant', fn($q) =>
                $q->where('nom', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('prenom', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('matricule', 'LIKE', '%' . $request->search . '%')
            );
        }

        $absences  = $query->orderByDesc('id')->get();
        $etudiants = Etudiant::where('annee_universitaire', $annee)->where('actif', 1)->orderBy('nom')->get();

        $stats = [
            'total'          => Absence::where('annee_univ', $annee)->where('heures', '>', 0)->count(),
            'non_justifiees' => Absence::where('annee_univ', $annee)->where('justifie', 0)->where('heures', '>', 0)->count(),
            'justifiees'     => Absence::where('annee_univ', $annee)->where('justifie', 1)->where('heures', '>', 0)->count(),
            'total_penalites'=> Absence::where('annee_univ', $annee)->where('justifie', 0)->sum(\Illuminate\Support\Facades\DB::raw('heures * 0.01')),
        ];

        return view('absences.index', compact('absences', 'etudiants', 'annee', 'stats'));
    }

    public function saisir(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id'  => 'required|exists:matieres,id',
            'heures'      => 'required|numeric|min:0.5',
            'justifie'    => 'nullable|boolean',
        ]);

        $annee    = config('app.annee_courante', '2025/2026');
        $justifie = $request->boolean('justifie');

        Absence::updateOrCreate(
            [
                'etudiant_id' => $request->etudiant_id,
                'matiere_id'  => $request->matiere_id,
                'annee_univ'  => $annee,
            ],
            [
                'heures'   => $request->heures,
                'justifie' => $justifie,
                'penalite' => $justifie ? 0 : ($request->heures * 0.01),
            ]
        );

        // Recalcul
        app(\App\Services\CalculMoyenneService::class)
            ->recalculerTout($request->etudiant_id);

        // Journal
        JournalController::log(
            'ABSENCE_SAISIE',
            'Absence saisie — ' . \App\Models\Etudiant::find($request->etudiant_id)?->nom,
            auth()->id(),
            $request->heures . 'h · ' . ($justifie ? 'Justifiée' : 'Non justifiée')
        );

        return back()->with('success', 'Absence enregistrée avec succès.');
    }

    public function modifier(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        $request->validate([
            'heures'   => 'required|numeric|min:0.5',
            'justifie' => 'nullable|boolean',
        ]);

        $justifie = $request->boolean('justifie');
        $absence->update([
            'heures'   => $request->heures,
            'justifie' => $justifie,
            'penalite' => $justifie ? 0 : ($request->heures * 0.01),
        ]);

        app(\App\Services\CalculMoyenneService::class)
            ->recalculerTout($absence->etudiant_id);

        JournalController::log('ABSENCE_MODIFIEE', 'Absence modifiée', auth()->id());

        return back()->with('success', 'Absence modifiée avec succès.');
    }

    public function justifier($id)
    {
        $absence = Absence::findOrFail($id);
        $absence->update(['justifie' => 1, 'penalite' => 0]);

        app(\App\Services\CalculMoyenneService::class)
            ->recalculerTout($absence->etudiant_id);

        JournalController::log('ABSENCE_JUSTIFIEE', 'Absence justifiée', auth()->id());

        return back()->with('success', 'Absence justifiée. Moyenne recalculée.');
    }

    public function destroy($id)
    {
        $absence = Absence::findOrFail($id);
        $etudiantId = $absence->etudiant_id;
        $absence->delete();

        app(\App\Services\CalculMoyenneService::class)
            ->recalculerTout($etudiantId);

        JournalController::log('ABSENCE_SUPPRIMEE', 'Absence supprimée', auth()->id());

        return back()->with('success', 'Absence supprimée. Moyenne recalculée.');
    }
}