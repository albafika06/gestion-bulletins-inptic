<?php

namespace App\Exports;

use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\MoyenneMatiere;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use App\Models\Semestre;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ReleveNotesExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithEvents
{
    private $annee;

    public function __construct(string $annee)
    {
        $this->annee = $annee;
    }

    public function title(): string
    {
        return 'Relevé de Notes';
    }

    public function headings(): array
    {
        $headers = ['Matricule', 'Nom', 'Prénom'];

        $matieres = Matiere::with('ue.semestre')
                           ->where('actif', 1)
                           ->orderBy('ue_id')
                           ->orderBy('ordre')
                           ->get();

        foreach ($matieres as $matiere) {
            $headers[] = $matiere->libelle . ' - CC';
            $headers[] = $matiere->libelle . ' - Examen';
            $headers[] = $matiere->libelle . ' - Rattrapage';
            $headers[] = $matiere->libelle . ' - Moyenne';
        }

        $headers[] = 'Moyenne S5';
        $headers[] = 'Crédits S5';
        $headers[] = 'Moy. Validée S5';
        $headers[] = 'Moyenne S6';
        $headers[] = 'Crédits S6';
        $headers[] = 'Moy. Validée S6';
        $headers[] = 'Moyenne Annuelle';
        $headers[] = 'Décision Jury';
        $headers[] = 'Mention';

        return $headers;
    }

    public function array(): array
    {
        $annee     = $this->annee;
        $etudiants = Etudiant::where('annee_universitaire', $annee)
                             ->where('actif', 1)
                             ->orderBy('nom')
                             ->get();

        $matieres = Matiere::where('actif', 1)
                           ->orderBy('ue_id')
                           ->orderBy('ordre')
                           ->get();

        $s5 = Semestre::where('code', 'S5')->first();
        $s6 = Semestre::where('code', 'S6')->first();

        $rows = [];

        foreach ($etudiants as $etudiant) {
            $row = [
                $etudiant->matricule,
                $etudiant->nom,
                $etudiant->prenom,
            ];

            foreach ($matieres as $matiere) {
                $mm = MoyenneMatiere::where('etudiant_id', $etudiant->id)
                        ->where('matiere_id', $matiere->id)
                        ->where('annee_univ', $annee)
                        ->first();

                $row[] = $mm?->note_cc        !== null ? number_format($mm->note_cc, 2)        : '';
                $row[] = $mm?->note_examen     !== null ? number_format($mm->note_examen, 2)    : '';
                $row[] = $mm?->note_rattrapage !== null ? number_format($mm->note_rattrapage, 2): '';
                $row[] = $mm?->moyenne_finale  !== null ? number_format($mm->moyenne_finale, 2) : '';
            }

            // Résultats S5
            $rsS5 = ResultatSemestre::where('etudiant_id', $etudiant->id)
                        ->where('semestre_id', $s5->id)
                        ->where('annee_univ', $annee)->first();

            // Résultats S6
            $rsS6 = ResultatSemestre::where('etudiant_id', $etudiant->id)
                        ->where('semestre_id', $s6->id)
                        ->where('annee_univ', $annee)->first();

            // Résultat annuel
            $ra = ResultatAnnuel::where('etudiant_id', $etudiant->id)
                        ->where('annee_univ', $annee)->first();

            $row[] = $rsS5?->moyenne_semestre !== null ? number_format($rsS5->moyenne_semestre, 2) : '';
            $row[] = $rsS5?->credits_acquis ?? '';
            $row[] = $rsS5?->valide ? 'Validé' : 'Non validé';

            $row[] = $rsS6?->moyenne_semestre !== null ? number_format($rsS6->moyenne_semestre, 2) : '';
            $row[] = $rsS6?->credits_acquis ?? '';
            $row[] = $rsS6?->valide ? 'Validé' : 'Non validé';

            $row[] = $ra?->moyenne_annuelle !== null ? number_format($ra->moyenne_annuelle, 2) : '';

            $decisions = [
                'DIPLOME'            => 'Diplômé(e)',
                'REPRISE_SOUTENANCE' => 'Reprise Soutenance',
                'REDOUBLE'           => 'Redouble',
                'EN_ATTENTE'         => 'En attente',
            ];
            $mentions = [
                'TRES_BIEN'  => 'Très Bien',
                'BIEN'       => 'Bien',
                'ASSEZ_BIEN' => 'Assez Bien',
                'PASSABLE'   => 'Passable',
                'AUCUNE'     => '',
            ];

            $row[] = $ra ? ($decisions[$ra->decision_jury] ?? '') : '';
            $row[] = $ra ? ($mentions[$ra->mention] ?? '') : '';

            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Ligne d'en-tête
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 10,
                ],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['rgb' => '1A237E'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                    'wrapText'   => true,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Hauteur de la ligne d'en-tête
                $sheet->getRowDimension(1)->setRowHeight(60);

                // Auto-ajustement de toutes les colonnes
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

                    // Colonnes fixes : Matricule, Nom, Prénom
                    if ($col <= 3) {
                        $sheet->getColumnDimension($colLetter)->setWidth(20);
                    } else {
                        // Colonnes de notes : largeur fixe lisible
                        $sheet->getColumnDimension($colLetter)->setWidth(22);
                    }

                    // Centrer les cellules de notes
                    if ($col > 3) {
                        $highestRow = $sheet->getHighestRow();
                        $sheet->getStyle($colLetter . '2:' . $colLetter . $highestRow)
                              ->getAlignment()
                              ->setHorizontal('center');
                    }
                }

                // Figer les 3 premières colonnes et la ligne d'en-tête
                $sheet->freezePane('D2');

                // Bordures sur toutes les cellules
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                      ->getBorders()
                      ->getAllBorders()
                      ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Couleurs alternées sur les lignes de données
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)
                              ->getFill()
                              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setRGB('EBF3FB');
                    }
                }

                // Mettre en évidence les colonnes Moyenne
                // (toutes les 4 colonnes à partir de la colonne 6)
                for ($col = 6; $col <= $highestColumnIndex - 6; $col += 4) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getStyle($colLetter . '1:' . $colLetter . $highestRow)
                          ->getFill()
                          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                          ->getStartColor()
                          ->setRGB('E3F2FD');
                    $sheet->getStyle($colLetter . '1')
                          ->getFill()
                          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                          ->getStartColor()
                          ->setRGB('1565C0');
                }

                // Titre de la feuille
                $sheet->getParent()
                      ->getProperties()
                      ->setTitle('Relevé de Notes LP ASUR')
                      ->setSubject('Année ' . config('app.annee_courante', '2025/2026'));
            },
        ];
    }
}