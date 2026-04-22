<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // =========================================================
    // Liste des notifications de l'utilisateur connecté
    // =========================================================
    public function index()
    {
        $notifications = Notification::where('utilisateur_id', Auth::id())
                            ->orderByDesc('created_at')
                            ->take(20)
                            ->get();

        return view('notifications.index', compact('notifications'));
    }

    // =========================================================
    // Marquer UNE notification comme lue
    // Route : POST /notifications/{id}/lu  → notifications.lu
    // =========================================================
    public function marquerLu($id)
    {
        Notification::where('id', $id)
                    ->where('utilisateur_id', Auth::id())
                    ->firstOrFail()
                    ->update(['lu' => true]);

        return back()->with('success', 'Notification marquée comme lue.');
    }

    // =========================================================
    // Marquer TOUTES les notifications comme lues
    // Route : POST /notifications/tout-lu  → notifications.tout-lu
    // =========================================================
    public function marquerToutLu()
    {
        Notification::where('utilisateur_id', Auth::id())
                    ->where('lu', false)
                    ->update(['lu' => true]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    // =========================================================
    // Supprimer une notification
    // Route : DELETE /notifications/{id}  → notifications.destroy
    // =========================================================
    public function destroy($id)
    {
        Notification::where('id', $id)
                    ->where('utilisateur_id', Auth::id())
                    ->firstOrFail()
                    ->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    // =========================================================
    // Nombre de notifications non lues (helper statique pour les vues)
    // Usage : NotificationController::getNonLues()
    // =========================================================
    public static function getNonLues(): int
    {
        if (!Auth::check()) return 0;

        return Notification::where('utilisateur_id', Auth::id())
                    ->where('lu', false)
                    ->count();
    }
}