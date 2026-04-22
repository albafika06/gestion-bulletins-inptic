<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Evaluation;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use App\Services\CalculMoyenneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReleveNotesExport;
use App\Imports\NotesImport;

class ImportExportController extends Controller
{
    protected $calculService;

    public function __construct(CalculMoyenneService $calculService)
    {
        $this->calculService = $calculService;
    }

    public function index()
    {
        $annee     = config('app.annee_courante', '2025/2026');
        $etudiants = Etudiant::where('annee_universitaire', $annee)
                             ->where('actif', 1)
                             ->count();

        return view('import.index', compact('annee', 'etudiants'));
    }

    // Import des notes depuis Excel
    public function importNotes(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.mimes'    => 'Le fichier doit être au format Excel (.xlsx, .xls) ou CSV.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $import = new NotesImport($this->calculService);
            Excel::import($import, $request->file('fichier'));

            $stats = $import->getStats();

            return redirect()->route('import.index')
                ->with('success', "Import réussi ! {$stats['importees']} notes importées, {$stats['erreurs']} erreur(s).");

        } catch (\Exception $e) {
            return redirect()->route('import.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    // Export relevé de notes Excel
    public function exportReleve()
    {
        $annee    = config('app.annee_courante', '2025/2026');
        $filename = 'releve_notes_' . str_replace('/', '-', $annee) . '.xlsx';

        return Excel::download(new ReleveNotesExport($annee), $filename);
    }
}