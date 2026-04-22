<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\Absence;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneUE;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use App\Models\Matiere;
use App\Models\UniteEnseignement;
use App\Models\Semestre;
use App\Models\Parametre;
use App\Models\StatistiqueMatiere;

class CalculMoyenneService
{
    private $annee;
    private $poidsCc;
    private $poidsExam;
    private $penaliteAbsence;
    private $noteMinValidation;

    public function __construct()
    {
        $this->annee             = config('app.annee_courante', '2025/2026');
        $this->poidsCc           = (float) Parametre::getValeur('poids_cc', 0.40);
        $this->poidsExam         = (float) Parametre::getValeur('poids_examen', 0.60);
        $this->penaliteAbsence   = (float) Parametre::getValeur('penalite_absence', 0.01);
        $this->noteMinValidation = (float) Parametre::getValeur('note_min_validation', 10.00);
    }

    // ─────────────────────────────────────────────────
    // RECALCUL COMPLET EN CASCADE POUR UN ÉTUDIANT
    // ─────────────────────────────────────────────────
    public function recalculerTout(int $etudiantId): void
    {
        // 1. Calculer toutes les moyennes de matières
        $matieres = Matiere::where('actif', 1)->get();
        foreach ($matieres as $matiere) {
            $this->calculerMoyenneMatiere($etudiantId, $matiere->id);
        }

        // 2. Calculer les moyennes de semestre provisoires
        $moyS5 = $this->calculerMoyenneSemestreProvisoire($etudiantId, 'S5');
        $moyS6 = $this->calculerMoyenneSemestreProvisoire($etudiantId, 'S6');

        // 3. Calculer les UEs avec compensation
        $ues = UniteEnseignement::with('semestre')->where('actif', 1)->get();
        foreach ($ues as $ue) {
            $moySemestre = $ue->semestre->code === 'S5' ? $moyS5 : $moyS6;
            $this->calculerMoyenneUE($etudiantId, $ue->id, $moySemestre);
        }

        // 4. Sauvegarder les résultats semestriels
        $this->sauvegarderResultatSemestre($etudiantId, 'S5');
        $this->sauvegarderResultatSemestre($etudiantId, 'S6');

        // 5. Calculer le résultat annuel
        $this->sauvegarderResultatAnnuel($etudiantId);

        // 6. Mettre à jour les statistiques de promotion
        $this->mettreAJourStatistiques();

        // 7. Recalculer les rangs de toute la promotion
        $this->calculerRangsPromotion();
    }

    // ─────────────────────────────────────────────────
    // 1. CALCUL MOYENNE D'UNE MATIÈRE
    // Règle 4.1 du document :
    // → CC × 40% + Examen × 60%
    // → Si une seule note → retenue sans pondération
    // → Si rattrapage → remplace INTÉGRALEMENT la moyenne
    // ─────────────────────────────────────────────────
    public function calculerMoyenneMatiere(int $etudiantId, int $matiereId): ?float
    {
        $noteCC = Evaluation::where('etudiant_id', $etudiantId)
                    ->where('matiere_id', $matiereId)
                    ->where('type_eval', 'CC')
                    ->where('annee_univ', $this->annee)
                    ->value('note');

        $noteExamen = Evaluation::where('etudiant_id', $etudiantId)
                    ->where('matiere_id', $matiereId)
                    ->where('type_eval', 'EXAMEN')
                    ->where('annee_univ', $this->annee)
                    ->value('note');

        $noteRattrapage = Evaluation::where('etudiant_id', $etudiantId)
                    ->where('matiere_id', $matiereId)
                    ->where('type_eval', 'RATTRAPAGE')
                    ->where('annee_univ', $this->annee)
                    ->value('note');

        $heuresAbsence = Absence::where('etudiant_id', $etudiantId)
                    ->where('matiere_id', $matiereId)
                    ->where('annee_univ', $this->annee)
                    ->value('heures') ?? 0;

        $rattrapageUtilise = false;
        $moyenneBrute      = null;

        if ($noteRattrapage !== null) {
            // RÈGLE 4.1 : le rattrapage remplace INTÉGRALEMENT la moyenne initiale
            $moyenneBrute      = $noteRattrapage;
            $rattrapageUtilise = true;
        } elseif ($noteCC !== null && $noteExamen !== null) {
            // CC × 40% + Examen × 60%
            $moyenneBrute = round(
                ($noteCC * $this->poidsCc) + ($noteExamen * $this->poidsExam), 2
            );
        } elseif ($noteCC !== null) {
            // Seule note CC → retenue sans pondération
            $moyenneBrute = $noteCC;
        } elseif ($noteExamen !== null) {
            // Seule note Examen → retenue sans pondération
            $moyenneBrute = $noteExamen;
        }

        // Appliquer pénalité absence
        $penalite      = round($heuresAbsence * $this->penaliteAbsence, 2);
        $moyenneFinale = null;

        if ($moyenneBrute !== null) {
            $moyenneFinale = max(0, round($moyenneBrute - $penalite, 2));
        }

        MoyenneMatiere::updateOrCreate(
            [
                'etudiant_id' => $etudiantId,
                'matiere_id'  => $matiereId,
                'annee_univ'  => $this->annee,
            ],
            [
                'note_cc'            => $noteCC,
                'note_examen'        => $noteExamen,
                'note_rattrapage'    => $noteRattrapage,
                'moyenne_brute'      => $moyenneBrute,
                'heures_absence'     => $heuresAbsence,
                'penalite_absence'   => $penalite,
                'moyenne_finale'     => $moyenneFinale,
                'rattrapage_utilise' => $rattrapageUtilise,
            ]
        );

        return $moyenneFinale;
    }

    // ─────────────────────────────────────────────────
    // 2. CALCUL MOYENNE D'UNE UE
    // Règle 4.2 : moyenne pondérée des matières
    // Règle 4.5 :
    // → UE acquise si moyenne UE ≥ 10
    // → UE compensée si moyenne UE < 10 MAIS moyenne SEMESTRE ≥ 10
    // → UE non acquise sinon
    // ─────────────────────────────────────────────────
    public function calculerMoyenneUE(int $etudiantId, int $ueId, ?float $moySemestre): ?float
    {
        $matieres   = Matiere::where('ue_id', $ueId)->where('actif', 1)->get();
        $sommeP     = 0;
        $sommeCoeff = 0;
        $creditsUE  = 0;

        foreach ($matieres as $matiere) {
            $creditsUE += $matiere->credits;
            $mm = MoyenneMatiere::where('etudiant_id', $etudiantId)
                    ->where('matiere_id', $matiere->id)
                    ->where('annee_univ', $this->annee)
                    ->first();

            if ($mm && $mm->moyenne_finale !== null) {
                $sommeP     += $mm->moyenne_finale * $matiere->coefficient;
                $sommeCoeff += $matiere->coefficient;
            }
        }

        $moyenneUE     = null;
        $statut        = 'NON_EVALUEE';
        $creditsAcquis = 0;

        if ($sommeCoeff > 0) {
            $moyenneUE = round($sommeP / $sommeCoeff, 2);

            if ($moyenneUE >= $this->noteMinValidation) {
                // Vérifier si toutes les matières sont >= 10
                $toutesValidees = true;
                foreach ($matieres as $matiere) {
                    $mm = MoyenneMatiere::where('etudiant_id', $etudiantId)
                            ->where('matiere_id', $matiere->id)
                            ->where('annee_univ', $this->annee)
                            ->first();
                    if ($mm && $mm->moyenne_finale !== null && $mm->moyenne_finale < 10) {
                        $toutesValidees = false;
                        break;
                    }
                }

                if ($toutesValidees) {
                    // Toutes matières >= 10 → UE ACQUISE directement
                    $statut        = 'ACQUISE';
                    $creditsAcquis = $creditsUE;
                } else {
                    // Moyenne UE ≥ 10 mais pas toutes matières >= 10
                    // → UE ACQUISE PAR COMPENSATION
                    $statut        = 'COMPENSEE';
                    $creditsAcquis = $creditsUE;
                }

            } elseif ($moySemestre !== null && $moySemestre >= $this->noteMinValidation) {
                // RÈGLE 4.5 : Moyenne UE < 10 MAIS moyenne SEMESTRE ≥ 10
                // → UE ACQUISE PAR COMPENSATION
                $statut        = 'COMPENSEE';
                $creditsAcquis = $creditsUE;
            } else {
                // Moyenne UE < 10 ET moyenne semestre < 10
                // → UE NON ACQUISE
                $statut        = 'NON_ACQUISE';
                $creditsAcquis = 0;
            }
        }

        MoyenneUE::updateOrCreate(
            [
                'etudiant_id' => $etudiantId,
                'ue_id'       => $ueId,
                'annee_univ'  => $this->annee,
            ],
            [
                'moyenne_ue'     => $moyenneUE,
                'credits_ue'     => $creditsUE,
                'credits_acquis' => $creditsAcquis,
                'statut'         => $statut,
            ]
        );

        return $moyenneUE;
    }

    // ─────────────────────────────────────────────────
    // 3. CALCUL MOYENNE SEMESTRE PROVISOIRE
    // Règle 4.3 : moyenne pondérée des UE
    // ─────────────────────────────────────────────────
    private function calculerMoyenneSemestreProvisoire(int $etudiantId, string $semCode): ?float
    {
        $semestre = Semestre::where('code', $semCode)->first();
        if (!$semestre) return null;

        $matieres   = Matiere::whereHas('ue', function($q) use ($semestre) {
                            $q->where('semestre_id', $semestre->id);
                        })->where('actif', 1)->get();

        $sommeP     = 0;
        $sommeCoeff = 0;

        foreach ($matieres as $matiere) {
            $mm = MoyenneMatiere::where('etudiant_id', $etudiantId)
                    ->where('matiere_id', $matiere->id)
                    ->where('annee_univ', $this->annee)
                    ->first();

            if ($mm && $mm->moyenne_finale !== null) {
                $sommeP     += $mm->moyenne_finale * $matiere->coefficient;
                $sommeCoeff += $matiere->coefficient;
            }
        }

        return $sommeCoeff > 0 ? round($sommeP / $sommeCoeff, 2) : null;
    }

    // ─────────────────────────────────────────────────
    // 4. SAUVEGARDER RÉSULTAT SEMESTRE
    // Règle 4.6 : semestre validé si crédits acquis ≥ 30
    // ─────────────────────────────────────────────────
    private function sauvegarderResultatSemestre(int $etudiantId, string $semCode): void
    {
        $semestre = Semestre::where('code', $semCode)->first();
        if (!$semestre) return;

        $moySemestre = $this->calculerMoyenneSemestreProvisoire($etudiantId, $semCode);

        $creditsAcquis = MoyenneUE::where('etudiant_id', $etudiantId)
                            ->where('annee_univ', $this->annee)
                            ->whereHas('ue', function($q) use ($semestre) {
                                $q->where('semestre_id', $semestre->id);
                            })->sum('credits_acquis');

        $valide = $creditsAcquis >= $semestre->credits_total;
        $statut = $valide ? 'VALIDE' : 'NON_VALIDE';

        ResultatSemestre::updateOrCreate(
            [
                'etudiant_id' => $etudiantId,
                'semestre_id' => $semestre->id,
                'annee_univ'  => $this->annee,
            ],
            [
                'moyenne_semestre' => $moySemestre,
                'credits_total'    => $semestre->credits_total,
                'credits_acquis'   => $creditsAcquis,
                'valide'           => $valide,
                'statut_decision'  => $statut,
            ]
        );
    }

    // ─────────────────────────────────────────────────
    // 5. SAUVEGARDER RÉSULTAT ANNUEL
    // Règle 4.4 : Moyenne annuelle = (S5 + S6) / 2
    // Règle 4.7 : Décision jury
    // Règle 4.8 : Mentions
    // ─────────────────────────────────────────────────
    private function sauvegarderResultatAnnuel(int $etudiantId): void
    {
        $s5 = Semestre::where('code', 'S5')->first();
        $s6 = Semestre::where('code', 'S6')->first();

        $rsS5 = ResultatSemestre::where('etudiant_id', $etudiantId)
                    ->where('semestre_id', $s5->id)
                    ->where('annee_univ', $this->annee)->first();

        $rsS6 = ResultatSemestre::where('etudiant_id', $etudiantId)
                    ->where('semestre_id', $s6->id)
                    ->where('annee_univ', $this->annee)->first();

        $moyS5 = $rsS5?->moyenne_semestre;
        $moyS6 = $rsS6?->moyenne_semestre;

        // RÈGLE 4.4 : Moyenne annuelle = (S5 + S6) / 2
        $moyAnnuelle = ($moyS5 !== null && $moyS6 !== null)
                        ? round(($moyS5 + $moyS6) / 2, 2)
                        : null;

        $creditsAcquis = ($rsS5?->credits_acquis ?? 0) + ($rsS6?->credits_acquis ?? 0);

        // RÈGLE 4.7 : Décision jury
        // UE6-2 = id 4 dans notre BDD
        $creditsUE62 = MoyenneUE::where('etudiant_id', $etudiantId)
                        ->where('annee_univ', $this->annee)
                        ->where('ue_id', 4)
                        ->value('credits_acquis') ?? 0;

        if ($creditsAcquis >= 60) {
            // Les deux semestres validés → DIPLÔMÉ
            $decision = 'DIPLOME';
        } elseif ($creditsAcquis >= 50 && $creditsUE62 == 0) {
            // Tous crédits acquis SAUF UE6-2 → REPRISE SOUTENANCE
            $decision = 'REPRISE_SOUTENANCE';
        } else {
            // Crédits insuffisants → REDOUBLE
            $decision = 'REDOUBLE';
        }

        // RÈGLE 4.8 : Mentions
        $mention = 'AUCUNE';
        if ($moyAnnuelle !== null) {
            if ($moyAnnuelle >= 16)     $mention = 'TRES_BIEN';
            elseif ($moyAnnuelle >= 14) $mention = 'BIEN';
            elseif ($moyAnnuelle >= 12) $mention = 'ASSEZ_BIEN';
            elseif ($moyAnnuelle >= 10) $mention = 'PASSABLE';
        }

        ResultatAnnuel::updateOrCreate(
            [
                'etudiant_id' => $etudiantId,
                'annee_univ'  => $this->annee,
            ],
            [
                'moyenne_s5'       => $moyS5,
                'moyenne_s6'       => $moyS6,
                'moyenne_annuelle' => $moyAnnuelle,
                'credits_total'    => 60,
                'credits_acquis'   => $creditsAcquis,
                'decision_jury'    => $decision,
                'mention'          => $mention,
            ]
        );
    }

    // ─────────────────────────────────────────────────
    // 6. STATISTIQUES DE PROMOTION
    // ─────────────────────────────────────────────────
    public function mettreAJourStatistiques(): void
    {
        // Stats par matière
        $matieres = Matiere::where('actif', 1)->get();
        foreach ($matieres as $matiere) {
            $moyennes = MoyenneMatiere::where('matiere_id', $matiere->id)
                            ->where('annee_univ', $this->annee)
                            ->whereNotNull('moyenne_finale')
                            ->pluck('moyenne_finale');

            if ($moyennes->count() > 0) {
                $moyenne  = round($moyennes->avg(), 2);
                $min      = round($moyennes->min(), 2);
                $max      = round($moyennes->max(), 2);
                $variance = $moyennes->map(fn($m) => pow($m - $moyenne, 2))->avg();
                $ecart    = round(sqrt($variance), 2);

                StatistiqueMatiere::updateOrCreate(
                    ['matiere_id' => $matiere->id, 'annee_univ' => $this->annee],
                    ['nb_notes' => $moyennes->count(), 'moyenne_classe' => $moyenne,
                     'note_min' => $min, 'note_max' => $max, 'ecart_type' => $ecart]
                );
            }
        }

        // Stats par semestre
        $semestres = Semestre::all();
        foreach ($semestres as $semestre) {
            $moyennesSem = ResultatSemestre::where('semestre_id', $semestre->id)
                                ->where('annee_univ', $this->annee)
                                ->whereNotNull('moyenne_semestre')
                                ->pluck('moyenne_semestre');

            if ($moyennesSem->count() > 0) {
                ResultatSemestre::where('semestre_id', $semestre->id)
                    ->where('annee_univ', $this->annee)
                    ->update([
                        'stat_moyenne_classe' => round($moyennesSem->avg(), 2),
                        'stat_min'            => round($moyennesSem->min(), 2),
                        'stat_max'            => round($moyennesSem->max(), 2),
                    ]);
            }
        }

        // Stats annuelles
        $moyennesAn = ResultatAnnuel::where('annee_univ', $this->annee)
                            ->whereNotNull('moyenne_annuelle')
                            ->pluck('moyenne_annuelle');

        if ($moyennesAn->count() > 0) {
            ResultatAnnuel::where('annee_univ', $this->annee)
                ->update([
                    'stat_moyenne_classe' => round($moyennesAn->avg(), 2),
                    'stat_min'            => round($moyennesAn->min(), 2),
                    'stat_max'            => round($moyennesAn->max(), 2),
                ]);
        }
    }

    // ─────────────────────────────────────────────────
    // 7. RANGS DE LA PROMOTION
    // ─────────────────────────────────────────────────
    public function calculerRangsPromotion(): void
    {
        $annee = $this->annee;

        $s5 = Semestre::where('code', 'S5')->first();
        $s6 = Semestre::where('code', 'S6')->first();

        // Rangs S5
        if ($s5) {
            $resultatsS5 = ResultatSemestre::where('semestre_id', $s5->id)
                                ->where('annee_univ', $annee)
                                ->whereNotNull('moyenne_semestre')
                                ->orderByDesc('moyenne_semestre')
                                ->get();
            foreach ($resultatsS5 as $rang => $rs) {
                $rs->rang = $rang + 1;
                $rs->save();
            }
        }

        // Rangs S6
        if ($s6) {
            $resultatsS6 = ResultatSemestre::where('semestre_id', $s6->id)
                                ->where('annee_univ', $annee)
                                ->whereNotNull('moyenne_semestre')
                                ->orderByDesc('moyenne_semestre')
                                ->get();
            foreach ($resultatsS6 as $rang => $rs) {
                $rs->rang = $rang + 1;
                $rs->save();
            }
        }

        // Rangs annuels
        $resultatsAnnuels = ResultatAnnuel::where('annee_univ', $annee)
                                ->whereNotNull('moyenne_annuelle')
                                ->orderByDesc('moyenne_annuelle')
                                ->get();
        foreach ($resultatsAnnuels as $rang => $ra) {
            $ra->rang_annuel = $rang + 1;
            $ra->save();
        }
    }
}
