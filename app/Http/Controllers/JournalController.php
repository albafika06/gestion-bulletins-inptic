<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalAudit::with('utilisateur')
                    ->orderByDesc('created_at');

        // Filtres
        if ($request->filled('type')) {
            $query->where('action', $request->type);
        }
        if ($request->filled('role')) {
            $query->whereHas('utilisateur', fn($q) =>
                $q->where('role', $request->role)
            );
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $journaux = $query->paginate(20);

        // Stats du jour
        $today = now()->toDateString();
        $stats = [
            'today_total'    => JournalAudit::whereDate('created_at', $today)->count(),
            'today_notes'    => JournalAudit::whereDate('created_at', $today)->where('action', 'LIKE', '%NOTE%')->count(),
            'today_bulletins'=> JournalAudit::whereDate('created_at', $today)->where('action', 'LIKE', '%BULLETIN%')->count(),
            'today_connexions'=> JournalAudit::whereDate('created_at', $today)->where('action', 'CONNEXION')->count(),
        ];

        return view('journal.index', compact('journaux', 'stats'));
    }

    public function destroy($id)
    {
        JournalAudit::findOrFail($id)->delete();
        return back()->with('success', 'Entrée supprimée du journal.');
    }

    public function destroySelection(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            JournalAudit::whereIn('id', $ids)->delete();
        }
        return back()->with('success', count($ids) . ' entrée(s) supprimée(s) du journal.');
    }

    public function viderTout()
    {
        JournalAudit::truncate();
        return back()->with('success', 'Journal vidé avec succès.');
    }

    // Méthode statique pour enregistrer une action
    public static function log(
        string $action,
        string $description,
        int    $utilisateurId = null,
        string $details = null
    ): void {
        try {
            JournalAudit::create([
                'utilisateur_id' => $utilisateurId ?? auth()->id(),
                'action'         => $action,
                'description'    => $description,
                'details'        => $details,
                'ip_address'     => request()->ip(),
                'user_agent'     => request()->userAgent(),
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // Ne pas bloquer l'application si le log échoue
        }
    }
}