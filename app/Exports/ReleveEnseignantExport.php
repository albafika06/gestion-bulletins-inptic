<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ReleveEnseignantExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithEvents
{
    private $matiere;
    private $rows;
    private $annee;

    public function __construct($matiere, array $rows, string $annee)
    {
        $this->matiere = $matiere;
        $this->rows    = $rows;
        $this->annee   = $annee;
    }

    public function title(): string
    {
        return 'Relevé ' . substr($this->matiere->libelle, 0, 20);
    }

    public function headings(): array
    {
        return [
            'Matricule',
            'Nom',
            'Prénom',
            'CC (40%)',
            'Examen (60%)',
            'Rattrapage',
            'Moyenne',
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E2A3A']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Titre du relevé
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Relevé de notes — ' . $this->matiere->libelle);
                $sheet->setCellValue('A2', 'Année : ' . $this->annee . ' | ' . $this->matiere->ue->semestre->libelle);
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E2A3A']],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Largeurs colonnes
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);

                // Figer la ligne d'en-tête
                $sheet->freezePane('A4');

                // Bordures
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:G' . $highestRow)
                      ->getBorders()->getAllBorders()
                      ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Lignes alternées
                for ($row = 4; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':G' . $row)
                              ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                              ->getStartColor()->setRGB('F8F9FF');
                    }
                }
            },
        ];
    }
}