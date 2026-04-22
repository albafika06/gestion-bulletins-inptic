<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Semestre;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneUE;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use App\Models\StatistiqueMatiere;
use App\Models\Absence;
use App\Models\Notification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class BulletinController extends Controller
{
    private function getAnnee(): string
    {
        return config('app.annee_courante', '2025/2026');
    }

    private function getNbEtudiants(): int
    {
        return Etudiant::where('annee_universitaire', $this->getAnnee())
                        ->where('actif', 1)->count();
    }

    // ── Liste bulletins
    public function index()
    {
        $annee     = $this->getAnnee();
        $etudiants = Etudiant::where('annee_universitaire', $annee)
                        ->where('actif', 1)->orderBy('nom')->get();

        $resultats = ResultatAnnuel::where('annee_univ', $annee)
                        ->get()->keyBy('etudiant_id');

        return view('bulletins.index', compact('etudiants', 'resultats', 'annee'));
    }

    // ── Préparer les données d'un bulletin
    private function preparerDonnees(Etudiant $etudiant, Semestre $semestre): array
    {
        $annee = $this->getAnnee();

        $matieresIds    = $semestre->unitesEnseignement->flatMap->matieres->pluck('id');
        $moyennesMatieres = MoyenneMatiere::where('etudiant_id', $etudiant->id)
                            ->whereIn('matiere_id', $matieresIds)
                            ->where('annee_univ', $annee)->get()->keyBy('matiere_id');

        $moyennesUEs = MoyenneUE::where('etudiant_id', $etudiant->id)
                        ->whereIn('ue_id', $semestre->unitesEnseignement->pluck('id'))
                        ->where('annee_univ', $annee)->get()->keyBy('ue_id');

        $resultat = ResultatSemestre::where('etudiant_id', $etudiant->id)
                        ->where('semestre_id', $semestre->id)
                        ->where('annee_univ', $annee)->first();

        $stats = StatistiqueMatiere::where('annee_univ', $annee)
                        ->whereIn('matiere_id', $matieresIds)
                        ->get()->keyBy('matiere_id');

        $absences = Absence::where('etudiant_id', $etudiant->id)
                        ->whereIn('matiere_id', $matieresIds)
                        ->where('annee_univ', $annee)->get();

        $nbEtudiants = $this->getNbEtudiants();

        return compact(
            'etudiant', 'semestre', 'annee',
            'moyennesMatieres', 'moyennesUEs',
            'resultat', 'stats', 'absences', 'nbEtudiants'
        );
    }

    // ── Bulletin S5
    public function genererS5($etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $semestre = Semestre::with(['unitesEnseignement.matieres'])
                        ->where('code', 'S5')->firstOrFail();
        $data     = $this->preparerDonnees($etudiant, $semestre);
        $pdf      = Pdf::loadView('bulletins.template_s5', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('bulletin_s5_' . $etudiant->matricule . '.pdf');
    }

    // ── Bulletin S6
    public function genererS6($etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $semestre = Semestre::with(['unitesEnseignement.matieres'])
                        ->where('code', 'S6')->firstOrFail();
        $data     = $this->preparerDonnees($etudiant, $semestre);
        $pdf      = Pdf::loadView('bulletins.template_s6', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('bulletin_s6_' . $etudiant->matricule . '.pdf');
    }

    // ── Bulletin Annuel
    public function genererAnnuel($etudiantId)
    {
        $annee    = $this->getAnnee();
        $etudiant = Etudiant::findOrFail($etudiantId);

        $rsS5 = ResultatSemestre::where('etudiant_id', $etudiantId)
                    ->whereHas('semestre', fn($q) => $q->where('code', 'S5'))
                    ->where('annee_univ', $annee)->first();

        $rsS6 = ResultatSemestre::where('etudiant_id', $etudiantId)
                    ->whereHas('semestre', fn($q) => $q->where('code', 'S6'))
                    ->where('annee_univ', $annee)->first();

        $ra = ResultatAnnuel::where('etudiant_id', $etudiantId)
                ->where('annee_univ', $annee)->first();

        $nbEtudiants = $this->getNbEtudiants();

        $pdf = Pdf::loadView('bulletins.template_annuel', compact(
            'etudiant', 'rsS5', 'rsS6', 'ra', 'annee', 'nbEtudiants'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('bulletin_annuel_' . $etudiant->matricule . '.pdf');
    }

    // ── Publier le bulletin d'un étudiant
    public function publier($etudiantId)
    {
        $annee    = $this->getAnnee();
        $etudiant = Etudiant::findOrFail($etudiantId);

        ResultatAnnuel::where('etudiant_id', $etudiantId)
            ->where('annee_univ', $annee)
            ->update(['publie_etudiant' => 1]);

        ResultatSemestre::where('etudiant_id', $etudiantId)
            ->where('annee_univ', $annee)
            ->update(['publie_etudiant' => 1]);

        // Notification à l'étudiant
        $userEtudiant = User::where('etudiant_id', $etudiantId)->where('role', 'ETUDIANT')->first();
        if ($userEtudiant) {
            Notification::creer(
                $userEtudiant->id, 'BULLETIN_ANNUEL',
                '🎓 Votre bulletin annuel est disponible',
                'Votre bulletin de notes annuel ' . $annee . ' a été publié. Décision du jury incluse.',
                route('etudiant.bulletins')
            );
            Notification::creer(
                $userEtudiant->id, 'BULLETIN_S5',
                '📄 Votre bulletin du Semestre 5 est disponible',
                'Votre bulletin de notes du Semestre 5 est disponible. Vous pouvez le consulter et le télécharger.',
                route('etudiant.bulletins')
            );
            Notification::creer(
                $userEtudiant->id, 'BULLETIN_S6',
                '📄 Votre bulletin du Semestre 6 est disponible',
                'Votre bulletin de notes du Semestre 6 est disponible. Vous pouvez le consulter et le télécharger.',
                route('etudiant.bulletins')
            );
        }

        // Journal
        JournalController::log(
            'BULLETIN_PUBLIE',
            'Bulletin publié — ' . $etudiant->nom . ' ' . $etudiant->prenom,
            Auth::id(),
            'Matricule : ' . $etudiant->matricule
        );

        return back()->with('success', 'Bulletin publié. L\'étudiant a été notifié.');
    }

    // ── Dépublier
    public function depublier($etudiantId)
    {
        $annee    = $this->getAnnee();
        $etudiant = Etudiant::findOrFail($etudiantId);

        ResultatAnnuel::where('etudiant_id', $etudiantId)
            ->where('annee_univ', $annee)
            ->update(['publie_etudiant' => 0]);

        ResultatSemestre::where('etudiant_id', $etudiantId)
            ->where('annee_univ', $annee)
            ->update(['publie_etudiant' => 0]);

        JournalController::log(
            'BULLETIN_DEPUBLIE',
            'Bulletin dépublié — ' . $etudiant->nom . ' ' . $etudiant->prenom,
            Auth::id()
        );

        return back()->with('success', 'Bulletin dépublié.');
    }

    // ── Publier tous les bulletins
    public function publierTous()
    {
        $annee     = $this->getAnnee();
        $etudiants = Etudiant::where('annee_universitaire', $annee)->where('actif', 1)->get();

        foreach ($etudiants as $etudiant) {
            ResultatAnnuel::where('etudiant_id', $etudiant->id)
                ->where('annee_univ', $annee)->update(['publie_etudiant' => 1]);
            ResultatSemestre::where('etudiant_id', $etudiant->id)
                ->where('annee_univ', $annee)->update(['publie_etudiant' => 1]);

            $userEtudiant = User::where('etudiant_id', $etudiant->id)->where('role', 'ETUDIANT')->first();
            if ($userEtudiant) {
                Notification::creer(
                    $userEtudiant->id, 'BULLETIN_ANNUEL',
                    '🎓 Vos bulletins sont disponibles',
                    'Tous vos bulletins pour l\'année ' . $annee . ' ont été publiés.',
                    route('etudiant.bulletins')
                );
            }
        }

        JournalController::log('BULLETINS_TOUS_PUBLIES', 'Tous les bulletins publiés', Auth::id());

        return back()->with('success', 'Tous les bulletins ont été publiés. Les étudiants ont été notifiés.');
    }

    // ── Espace étudiant : ses bulletins
    public function mesBulletins()
    {
        $user = Auth::user();
        if (!$user->etudiant) return redirect()->route('dashboard');

        $annee    = $this->getAnnee();
        $etudiant = $user->etudiant;

        $rsS5 = ResultatSemestre::where('etudiant_id', $etudiant->id)
                    ->whereHas('semestre', fn($q) => $q->where('code', 'S5'))
                    ->where('annee_univ', $annee)->first();

        $rsS6 = ResultatSemestre::where('etudiant_id', $etudiant->id)
                    ->whereHas('semestre', fn($q) => $q->where('code', 'S6'))
                    ->where('annee_univ', $annee)->first();

        $ra = ResultatAnnuel::where('etudiant_id', $etudiant->id)
                ->where('annee_univ', $annee)->first();

        return view('etudiant.bulletins', compact('etudiant', 'rsS5', 'rsS6', 'ra', 'annee'));
    }

    public function monBulletin($semestre)
    {
        $user = Auth::user();
        if (!$user->etudiant) return redirect()->route('dashboard');

        $annee    = $this->getAnnee();
        $etudiant = $user->etudiant;

        if ($semestre === 'annuel') return $this->genererAnnuel($etudiant->id);
        if ($semestre === 's5')     return $this->genererS5($etudiant->id);
        if ($semestre === 's6')     return $this->genererS6($etudiant->id);

        return redirect()->route('etudiant.bulletins');
    }
}