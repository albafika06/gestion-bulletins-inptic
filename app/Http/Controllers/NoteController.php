<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Matiere;
use App\Models\MoyenneMatiere;
use App\Models\EnseignantMatiere;
use App\Services\CalculMoyenneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    protected CalculMoyenneService $calculService;

    public function __construct(CalculMoyenneService $calculService)
    {
        $this->calculService = $calculService;
    }

    // =========================================================
    // Admin/Secrétariat : liste étudiants
    // =========================================================
    public function index()
    {
        $annee = config('app.annee_courante', '2025/2026');
        $user  = Auth::user();

        if ($user->isEnseignant()) {
            return redirect()->route('dashboard')
                ->with('error', 'Utilisez les boutons "Saisir" de votre tableau de bord.');
        }

        $etudiants = Etudiant::where('annee_universitaire', $annee)
                        ->where('actif', 1)->orderBy('nom')->get();

        return view('notes.index', compact('etudiants', 'annee'));
    }

    // =========================================================
    // Admin/Secrétariat : notes d'un étudiant
    // =========================================================
    public function show($etudiantId)
    {
        $annee    = config('app.annee_courante', '2025/2026');
        $user     = Auth::user();
        $etudiant = Etudiant::findOrFail($etudiantId);

        if ($user->isEnseignant()) {
            $premiereMat = EnseignantMatiere::where('utilisateur_id', $user->id)
                            ->where('annee_univ', $annee)->first();
            if ($premiereMat) return redirect()->route('enseignant.saisir', $premiereMat->matiere_id);
            return redirect()->route('dashboard')->with('error', 'Aucune matière assignée.');
        }

        $semestres = \App\Models\Semestre::with([
            'unitesEnseignement.matieres' => fn($q) => $q->where('actif', 1)->orderBy('ordre')
        ])->orderBy('ordre')->get();

        $evaluations = Evaluation::where('etudiant_id', $etudiantId)
                        ->where('annee_univ', $annee)->get()
                        ->keyBy(fn($e) => $e->matiere_id . '_' . $e->type_eval);

        $moyennes = MoyenneMatiere::where('etudiant_id', $etudiantId)
                        ->where('annee_univ', $annee)->get()->keyBy('matiere_id');

        $stats = \App\Models\StatistiqueMatiere::where('annee_univ', $annee)
                        ->get()->keyBy('matiere_id');

        return view('notes.show', compact(
            'etudiant', 'semestres', 'evaluations', 'moyennes', 'stats', 'annee'
        ));
    }

    // =========================================================
    // Admin/Secrétariat : saisie note individuelle
    // =========================================================
    public function saisir(Request $request)
    {
        $annee = config('app.annee_courante', '2025/2026');

        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id'  => 'required|exists:matieres,id',
            'type_eval'   => 'required|in:CC,EXAMEN,RATTRAPAGE',
            'note'        => 'required|numeric|min:0|max:20',
        ]);

        Evaluation::updateOrCreate(
            ['etudiant_id' => $request->etudiant_id, 'matiere_id' => $request->matiere_id,
             'type_eval' => $request->type_eval, 'annee_univ' => $annee],
            ['note' => $request->note, 'saisie_par' => Auth::id()]
        );

        $this->calculService->recalculerTout($request->etudiant_id);

        JournalController::log(
            'NOTE_SAISIE',
            'Note saisie — ' . Matiere::find($request->matiere_id)?->libelle . ' (' . $request->type_eval . ')',
            Auth::id(),
            'Note : ' . $request->note . '/20'
        );

        return back()->with('success', 'Note enregistrée et moyennes recalculées.');
    }

    // =========================================================
    // Recalcul manuel
    // =========================================================
    public function recalculer($etudiantId)
    {
        $this->calculService->recalculerTout($etudiantId);
        return back()->with('success', 'Moyennes recalculées avec succès.');
    }

    // =========================================================
    // Étudiant : ses propres notes
    // =========================================================
    public function mesNotes()
    {
        $user  = Auth::user();
        $annee = config('app.annee_courante', '2025/2026');

        if (!$user->etudiant) return redirect()->route('dashboard');

        $etudiant  = $user->etudiant;
        $semestres = \App\Models\Semestre::with([
            'unitesEnseignement.matieres' => fn($q) => $q->where('actif', 1)->orderBy('ordre')
        ])->orderBy('ordre')->get();

        $evaluations = Evaluation::where('etudiant_id', $etudiant->id)
                        ->where('annee_univ', $annee)->get()
                        ->keyBy(fn($e) => $e->matiere_id . '_' . $e->type_eval);

        $moyennes = MoyenneMatiere::where('etudiant_id', $etudiant->id)
                        ->where('annee_univ', $annee)->get()->keyBy('matiere_id');

        $stats = \App\Models\StatistiqueMatiere::where('annee_univ', $annee)->get()->keyBy('matiere_id');

        return view('etudiant.notes', compact('etudiant', 'semestres', 'evaluations', 'moyennes', 'stats', 'annee'));
    }

    // =========================================================
    // Étudiant : voir le relevé d'une matière publiée
    // =========================================================
    public function releveEtudiant($matiereId)
    {
        $user  = Auth::user();
        $annee = config('app.annee_courante', '2025/2026');

        if (!$user->etudiant) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun dossier étudiant associé à votre compte.');
        }

        $etudiant = $user->etudiant;
        $matiere  = Matiere::with('ue.semestre')->findOrFail($matiereId);

        // Vérifier que le relevé est bien publié par un enseignant
        $estPublie = EnseignantMatiere::where('matiere_id', $matiereId)
                        ->where('annee_univ', $annee)
                        ->where('releve_publie', true)
                        ->exists();

        if (!$estPublie) {
            return redirect()->route('etudiant.bulletins')
                ->with('error', 'Ce relevé n\'est pas encore disponible.');
        }

        // Notes personnelles de l'étudiant pour cette matière
        $notes = [
            'cc'         => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'CC')->where('annee_univ', $annee)->value('note'),
            'examen'     => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'EXAMEN')->where('annee_univ', $annee)->value('note'),
            'rattrapage' => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'RATTRAPAGE')->where('annee_univ', $annee)->value('note'),
            'moyenne'    => MoyenneMatiere::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('annee_univ', $annee)->value('moyenne_finale'),
        ];

        // Statistiques de la classe (pour le positionnement)
        $stat = \App\Models\StatistiqueMatiere::where('matiere_id', $matiereId)
                    ->where('annee_univ', $annee)->first();

        // Rang de l'étudiant dans la classe
        $toutesLesMoyennes = MoyenneMatiere::where('matiere_id', $matiereId)
                                ->where('annee_univ', $annee)
                                ->whereNotNull('moyenne_finale')
                                ->orderByDesc('moyenne_finale')
                                ->pluck('moyenne_finale', 'etudiant_id');

        $rang       = null;
        $nbEtudiants = $toutesLesMoyennes->count();
        if ($notes['moyenne'] !== null && $nbEtudiants > 0) {
            $rang = $toutesLesMoyennes->search($notes['moyenne']);
            $rang = $rang !== false ? array_search($rang, $toutesLesMoyennes->keys()->toArray()) + 1 : null;
        }

        return view('etudiant.releve', compact(
            'matiere', 'etudiant', 'notes', 'stat', 'annee', 'rang', 'nbEtudiants'
        ));
    }

    // =========================================================
    // Étudiant : télécharger le relevé Excel d'une matière publiée
    // =========================================================
    public function exportReleveEtudiant($matiereId)
    {
        $user  = Auth::user();
        $annee = config('app.annee_courante', '2025/2026');

        if (!$user->etudiant) abort(403);

        // Vérifier que le relevé est publié
        $estPublie = EnseignantMatiere::where('matiere_id', $matiereId)
                        ->where('annee_univ', $annee)
                        ->where('releve_publie', true)
                        ->exists();

        if (!$estPublie) abort(403, 'Ce relevé n\'est pas encore disponible.');

        $matiere = Matiere::findOrFail($matiereId);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReleveEnseignantExport($matiereId, $annee),
            'releve_' . str_replace(' ', '_', $matiere->libelle) . '_' . $annee . '.xlsx'
        );
    }

    // =========================================================
    // Enseignant : saisie groupée par matière
    // =========================================================
    public function saisirMatiere($matiereId)
    {
        $annee   = config('app.annee_courante', '2025/2026');
        $user    = Auth::user();
        $matiere = Matiere::with('ue.semestre')->findOrFail($matiereId);

        if ($user->isEnseignant()) {
            $aAcces = EnseignantMatiere::where('utilisateur_id', $user->id)
                        ->where('matiere_id', $matiereId)
                        ->where('annee_univ', $annee)->exists();
            if (!$aAcces) abort(403, 'Vous n\'avez pas accès à cette matière.');
        }

        $etudiants = Etudiant::where('annee_universitaire', $annee)
                        ->where('actif', 1)->orderBy('nom')->get();

        $notes = [];
        foreach ($etudiants as $etudiant) {
            $notes[$etudiant->id] = [
                'cc'         => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'CC')->where('annee_univ', $annee)->value('note'),
                'examen'     => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'EXAMEN')->where('annee_univ', $annee)->value('note'),
                'rattrapage' => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'RATTRAPAGE')->where('annee_univ', $annee)->value('note'),
                'moyenne'    => MoyenneMatiere::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('annee_univ', $annee)->value('moyenne_finale'),
            ];
        }

        return view('enseignant.saisir', compact('matiere', 'etudiants', 'notes', 'annee', 'user'));
    }

    // =========================================================
    // Enseignant : enregistrer les notes
    // =========================================================
    public function enregistrerMatiere(Request $request, $matiereId)
    {
        $annee = config('app.annee_courante', '2025/2026');
        $user  = Auth::user();

        if ($user->isEnseignant()) {
            $aAcces = EnseignantMatiere::where('utilisateur_id', $user->id)
                        ->where('matiere_id', $matiereId)
                        ->where('annee_univ', $annee)->exists();
            if (!$aAcces) abort(403);
        }

        $etudiants     = Etudiant::where('annee_universitaire', $annee)->where('actif', 1)->get();
        $nbSauvegardes = 0;

        foreach ($etudiants as $etudiant) {
            foreach (['cc' => 'CC', 'examen' => 'EXAMEN', 'rattrapage' => 'RATTRAPAGE'] as $champ => $typeEval) {
                $note = $request->input('notes.' . $etudiant->id . '.' . $champ);
                if ($note !== null && $note !== '') {
                    $note = (float) $note;
                    if ($note < 0 || $note > 20) continue;
                    Evaluation::updateOrCreate(
                        ['etudiant_id' => $etudiant->id, 'matiere_id' => (int) $matiereId,
                         'type_eval' => $typeEval, 'annee_univ' => $annee],
                        ['note' => $note, 'saisie_par' => $user->id]
                    );
                    $nbSauvegardes++;
                }
            }
            $this->calculService->recalculerTout($etudiant->id);
        }

        JournalController::log(
            'NOTE_SAISIE',
            $nbSauvegardes . ' note(s) saisie(s) — ' . Matiere::find($matiereId)?->libelle,
            $user->id
        );

        return redirect()->route('enseignant.saisir', $matiereId)
            ->with('success', $nbSauvegardes . ' note(s) enregistrée(s) et moyennes recalculées.');
    }

    // =========================================================
    // Enseignant : relevé par matière
    // =========================================================
    public function releve($matiereId)
    {
        $annee   = config('app.annee_courante', '2025/2026');
        $user    = Auth::user();
        $matiere = Matiere::with('ue.semestre')->findOrFail($matiereId);

        if ($user->isEnseignant()) {
            $aAcces = EnseignantMatiere::where('utilisateur_id', $user->id)
                        ->where('matiere_id', $matiereId)
                        ->where('annee_univ', $annee)->exists();
            if (!$aAcces) abort(403);
        }

        $etudiants = Etudiant::where('annee_universitaire', $annee)
                        ->where('actif', 1)->orderBy('nom')->get();

        $notes = [];
        foreach ($etudiants as $etudiant) {
            $notes[$etudiant->id] = [
                'cc'         => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'CC')->where('annee_univ', $annee)->value('note'),
                'examen'     => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'EXAMEN')->where('annee_univ', $annee)->value('note'),
                'rattrapage' => Evaluation::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('type_eval', 'RATTRAPAGE')->where('annee_univ', $annee)->value('note'),
                'moyenne'    => MoyenneMatiere::where('etudiant_id', $etudiant->id)->where('matiere_id', $matiereId)->where('annee_univ', $annee)->value('moyenne_finale'),
            ];
        }

        $stat = \App\Models\StatistiqueMatiere::where('matiere_id', $matiereId)
                    ->where('annee_univ', $annee)->first();

        $relevePublie = EnseignantMatiere::where('utilisateur_id', $user->id)
                            ->where('matiere_id', $matiereId)
                            ->where('annee_univ', $annee)
                            ->value('releve_publie') ?? false;

        return view('enseignant.releve', compact(
            'matiere', 'etudiants', 'notes', 'annee', 'stat', 'relevePublie'
        ));
    }

    // =========================================================
    // Enseignant : exporter relevé Excel
    // =========================================================
    public function exportReleve($matiereId)
    {
        $annee   = config('app.annee_courante', '2025/2026');
        $matiere = Matiere::findOrFail($matiereId);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReleveEnseignantExport($matiereId, $annee),
            'releve_' . str_replace(' ', '_', $matiere->libelle) . '_' . $annee . '.xlsx'
        );
    }

    // =========================================================
    // Enseignant : publier le relevé
    // =========================================================
    public function publierReleve($matiereId)
    {
        $annee   = config('app.annee_courante', '2025/2026');
        $user    = Auth::user();
        $matiere = Matiere::with('ue.semestre')->findOrFail($matiereId);

        $aAcces = EnseignantMatiere::where('utilisateur_id', $user->id)
                    ->where('matiere_id', $matiereId)
                    ->where('annee_univ', $annee)->exists();
        if (!$aAcces) abort(403);

        EnseignantMatiere::where('utilisateur_id', $user->id)
            ->where('matiere_id', $matiereId)
            ->where('annee_univ', $annee)
            ->update(['releve_publie' => true]);

        // Lien vers la page relevé étudiant (pas le Excel direct)
        $releveUrl = route('etudiant.releve', $matiereId);

        \App\Models\Notification::notifierTousEtudiants(
            'RELEVE_PUBLIE',
            '📊 Relevé de ' . $matiere->libelle . ' disponible',
            'L\'enseignant ' . $user->nom_affichage . ' a publié le relevé de notes de ' .
            $matiere->libelle . '. Vous pouvez consulter votre position dans la classe.',
            $releveUrl,
            $matiereId
        );

        JournalController::log(
            'RELEVE_PUBLIE',
            'Relevé publié — ' . $matiere->libelle,
            $user->id,
            'Étudiants notifiés'
        );

        return back()->with('success', 'Relevé publié ! Tous les étudiants ont été notifiés.');
    }
}