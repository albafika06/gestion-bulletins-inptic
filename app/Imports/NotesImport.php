<?php

namespace App\Imports;

use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Evaluation;
use App\Services\CalculMoyenneService;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NotesImport implements ToArray, WithHeadingRow
{
    protected $calculService;
    protected $stats = ['importees' => 0, 'erreurs' => 0];
    protected $annee;

    public function __construct(CalculMoyenneService $calculService)
    {
        $this->calculService = $calculService;
        $this->annee         = config('app.annee_courante', '2025/2026');
    }

    public function array(array $rows): void
    {
        foreach ($rows as $row) {
            try {
                // Chercher l'étudiant par matricule
                if (empty($row['matricule'])) continue;

                $etudiant = Etudiant::where('matricule', strtoupper($row['matricule']))
                                    ->where('annee_universitaire', $this->annee)
                                    ->first();

                if (!$etudiant) {
                    $this->stats['erreurs']++;
                    continue;
                }

                // Chercher la matière par code ou libellé
                if (empty($row['matiere'])) continue;

                $matiere = Matiere::where('code', strtoupper($row['matiere']))
                                  ->orWhere('libelle', $row['matiere'])
                                  ->first();

                if (!$matiere) {
                    $this->stats['erreurs']++;
                    continue;
                }

                // Importer les notes disponibles
                $typesNotes = [
                    'cc'         => 'CC',
                    'examen'     => 'EXAMEN',
                    'rattrapage' => 'RATTRAPAGE',
                ];

                foreach ($typesNotes as $colonne => $typeEval) {
                    if (isset($row[$colonne]) && $row[$colonne] !== '' && $row[$colonne] !== null) {
                        $note = (float) $row[$colonne];

                        // Valider que la note est entre 0 et 20
                        if ($note < 0 || $note > 20) {
                            $this->stats['erreurs']++;
                            continue;
                        }

                        Evaluation::updateOrCreate(
                            [
                                'etudiant_id' => $etudiant->id,
                                'matiere_id'  => $matiere->id,
                                'type_eval'   => $typeEval,
                                'annee_univ'  => $this->annee,
                            ],
                            [
                                'note'       => $note,
                                'saisie_par' => auth()->id(),
                            ]
                        );

                        $this->stats['importees']++;
                    }
                }

                // Recalcul automatique
                $this->calculService->recalculerTout($etudiant->id);

            } catch (\Exception $e) {
                $this->stats['erreurs']++;
            }
        }
    }

    public function getStats(): array
    {
        return $this->stats;
    }
}