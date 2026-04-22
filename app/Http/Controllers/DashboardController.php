<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Etudiant;
use App\Models\ResultatAnnuel;
use App\Models\Semestre;
use App\Models\Evaluation;
use App\Models\MoyenneUE;
use App\Models\MoyenneMatiere;
use App\Models\EnseignantMatiere;
use App\Models\JournalAudit;
use App\Models\Absence;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $annee = config('app.annee_courante', '2025/2026');
        $stats = [];

        // ── ADMIN
        if ($user->isAdmin()) {
            $stats['total_etudiants'] = Etudiant::where('annee_universitaire', $annee)->where('actif', 1)->count();
            $stats['diplomes']        = ResultatAnnuel::where('annee_univ', $annee)->where('decision_jury', 'DIPLOME')->count();
            $stats['redoublants']     = ResultatAnnuel::where('annee_univ', $annee)->where('decision_jury', 'REDOUBLE')->count();
            $stats['en_attente']      = ResultatAnnuel::where('annee_univ', $annee)->where('decision_jury', 'EN_ATTENTE')->count();

            $stats['journal_recent']  = JournalAudit::with('utilisateur')
                                            ->orderByDesc('created_at')
                                            ->take(5)->get();
        }

        // ── SECRÉTARIAT
        if ($user->isSecretariat()) {
            $stats['total_etudiants']       = Etudiant::where('annee_universitaire', $annee)->where('actif', 1)->count();
            $stats['diplomes']              = ResultatAnnuel::where('annee_univ', $annee)->where('decision_jury', 'DIPLOME')->count();
            $stats['bulletins_publies']     = ResultatAnnuel::where('annee_univ', $annee)->where('publie_etudiant', 1)->count();
            $stats['bulletins_non_publies'] = $stats['total_etudiants'] - $stats['bulletins_publies'];
            $stats['total_absences']        = Absence::where('annee_univ', $annee)->sum('heures');
            $stats['absences_non_justifiees'] = Absence::where('annee_univ', $annee)->where('justifie', 0)->count();

            $stats['dernieres_absences'] = Absence::with(['etudiant', 'matiere'])
                                                ->where('annee_univ', $annee)
                                                ->orderByDesc('id')
                                                ->take(5)->get();
        }

        // ── ENSEIGNANT
        if ($user->isEnseignant()) {
            $mesMatieres = EnseignantMatiere::where('utilisateur_id', $user->id)
                                ->where('annee_univ', $annee)
                                ->with('matiere.ue.semestre')
                                ->get();

            $stats['mes_matieres']    = $mesMatieres;
            $stats['total_etudiants'] = Etudiant::where('annee_universitaire', $annee)->where('actif', 1)->count();

            $statsMatieres = [];
            foreach ($mesMatieres as $em) {
                $moyennes = MoyenneMatiere::where('matiere_id', $em->matiere_id)
                                ->where('annee_univ', $annee)
                                ->whereNotNull('moyenne_finale')
                                ->pluck('moyenne_finale');

                $nbNotes = Evaluation::where('matiere_id', $em->matiere_id)
                                ->where('annee_univ', $annee)
                                ->whereIn('type_eval', ['CC', 'EXAMEN'])
                                ->distinct('etudiant_id')->count('etudiant_id');

                $maxNotes = $stats['total_etudiants'] * 2;
                $pct      = $maxNotes > 0 ? min(100, round(($nbNotes / $maxNotes) * 100)) : 0;
                $couleur  = $pct == 100 ? '#27500a' : ($pct >= 50 ? '#e65100' : '#c62828');

                $relevePublie = \App\Models\EnseignantMatiere::where('utilisateur_id', $user->id)
                                    ->where('matiere_id', $em->matiere_id)
                                    ->where('annee_univ', $annee)
                                    ->value('releve_publie') ?? false;

                $statsMatieres[$em->matiere_id] = [
                    'saisies'       => $nbNotes,
                    'total'         => $maxNotes,
                    'pct'           => $pct,
                    'couleur'       => $couleur,
                    'moy_classe'    => $moyennes->count() > 0 ? round($moyennes->avg(), 2) : null,
                    'note_max'      => $moyennes->count() > 0 ? round($moyennes->max(), 2) : null,
                    'note_min'      => $moyennes->count() > 0 ? round($moyennes->min(), 2) : null,
                    'nb_valides'    => $moyennes->filter(fn($m) => $m >= 10)->count(),
                    'nb_faibles'    => $moyennes->filter(fn($m) => $m >= 6 && $m < 10)->count(),
                    'nb_insuff'     => $moyennes->filter(fn($m) => $m < 6)->count(),
                    'releve_publie' => $relevePublie,
                ];
            }
            $stats['stats_matieres'] = $statsMatieres;
        }

        // ── ÉTUDIANT
        if ($user->isEtudiant() && $user->etudiant) {
            $stats['etudiant']        = $user->etudiant;
            $stats['resultat_annuel'] = ResultatAnnuel::where('etudiant_id', $user->etudiant->id)
                                            ->where('annee_univ', $annee)->first();
        }

        return view('dashboard.index', compact('user', 'stats', 'annee'));
    }
}