<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\ResultatAnnuel;
use App\Models\ResultatSemestre;
use App\Models\Semestre;
use App\Services\CalculMoyenneService;
use Illuminate\Http\Request;

class JuryController extends Controller
{
    protected $calculService;

    public function __construct(CalculMoyenneService $calculService)
    {
        $this->calculService = $calculService;
    }

    public function index()
    {
        $annee     = config('app.annee_courante', '2025/2026');
        $etudiants = Etudiant::with(['resultatAnnuel'])
                             ->where('annee_universitaire', $annee)
                             ->where('actif', 1)
                             ->orderBy('nom')
                             ->get();

        // Statistiques globales
        $stats = [
            'total'              => $etudiants->count(),
            'diplomes'           => $etudiants->filter(fn($e) => $e->resultatAnnuel?->decision_jury == 'DIPLOME')->count(),
            'reprise_soutenance' => $etudiants->filter(fn($e) => $e->resultatAnnuel?->decision_jury == 'REPRISE_SOUTENANCE')->count(),
            'redoublants'        => $etudiants->filter(fn($e) => $e->resultatAnnuel?->decision_jury == 'REDOUBLE')->count(),
            'en_attente'         => $etudiants->filter(fn($e) => !$e->resultatAnnuel || $e->resultatAnnuel?->decision_jury == 'EN_ATTENTE')->count(),
        ];

        return view('jury.index', compact('etudiants', 'annee', 'stats'));
    }

    // Recalculer tous les étudiants en batch
    public function valider(Request $request)
    {
        $annee     = config('app.annee_courante', '2025/2026');
        $etudiants = Etudiant::where('annee_universitaire', $annee)
                             ->where('actif', 1)->get();

        foreach ($etudiants as $etudiant) {
            $this->calculService->recalculerTout($etudiant->id);
        }

        // Calculer les rangs
        $this->calculerRangs($annee);

        return redirect()
            ->route('jury.index')
            ->with('success', 'Recalcul complet effectué pour tous les étudiants.');
    }

    // Calculer les rangs de la promotion
    private function calculerRangs(string $annee): void
{
    $s5 = Semestre::where('code', 'S5')->first();
    $s6 = Semestre::where('code', 'S6')->first();

    // ── Rangs S5
    $rsS5 = ResultatSemestre::where('semestre_id', $s5->id)
                ->where('annee_univ', $annee)
                ->whereNotNull('moyenne_semestre')
                ->orderByDesc('moyenne_semestre')
                ->get();

    foreach ($rsS5 as $rang => $rs) {
        $rs->rang = $rang + 1;
        $rs->save();
    }

    // ── Rangs S6
    $rsS6 = ResultatSemestre::where('semestre_id', $s6->id)
                ->where('annee_univ', $annee)
                ->whereNotNull('moyenne_semestre')
                ->orderByDesc('moyenne_semestre')
                ->get();

    foreach ($rsS6 as $rang => $rs) {
        $rs->rang = $rang + 1;
        $rs->save();
    }

    // ── Rangs annuels
    $ra = ResultatAnnuel::where('annee_univ', $annee)
                ->whereNotNull('moyenne_annuelle')
                ->orderByDesc('moyenne_annuelle')
                ->get();

    foreach ($ra as $rang => $r) {
        $r->rang_annuel = $rang + 1;
        $r->save();
    }
}
}