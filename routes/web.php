<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JournalController;

Route::get('/', fn() => redirect()->route('login'));

// ── Auth
Route::get('/login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications — tous rôles
    Route::get('/notifications',             [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/lu',    [NotificationController::class, 'marquerLu'])->name('notifications.lu');
    Route::post('/notifications/tout-lu',    [NotificationController::class, 'marquerToutLu'])->name('notifications.tout-lu');
    Route::delete('/notifications/{id}',     [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // ── ÉTUDIANT
    Route::middleware(['role:ETUDIANT'])->group(function () {
        Route::get('/mes-informations',              [EtudiantController::class, 'mesInformations'])->name('etudiant.informations');
        Route::get('/mes-notes',                     [NoteController::class, 'mesNotes'])->name('etudiant.notes');
        Route::get('/mes-bulletins',                 [BulletinController::class, 'mesBulletins'])->name('etudiant.bulletins');
        Route::get('/mes-bulletins/{semestre}',      [BulletinController::class, 'monBulletin'])->name('etudiant.bulletin');

        // Relevés publiés par les enseignants
        Route::get('/mes-releves/{matiere}',         [NoteController::class, 'releveEtudiant'])->name('etudiant.releve');
        Route::get('/mes-releves/{matiere}/export',  [NoteController::class, 'exportReleveEtudiant'])->name('etudiant.releve.export');
    });

    // ── ENSEIGNANT + ADMIN + SECRETARIAT
    Route::middleware(['role:ADMIN,SECRETARIAT,ENSEIGNANT'])->group(function () {
        Route::get('/notes',                  [NoteController::class, 'index'])->name('notes.index');
        Route::get('/notes/{etudiant}',       [NoteController::class, 'show'])->name('notes.show');
        Route::post('/notes/saisir',          [NoteController::class, 'saisir'])->name('notes.saisir');
        Route::post('/notes/recalculer/{id}', [NoteController::class, 'recalculer'])->name('notes.recalculer');

        Route::get('/enseignant/saisir/{matiere}',          [NoteController::class, 'saisirMatiere'])->name('enseignant.saisir');
        Route::post('/enseignant/saisir/{matiere}',         [NoteController::class, 'enregistrerMatiere'])->name('enseignant.enregistrer');
        Route::get('/enseignant/releve/{matiere}',          [NoteController::class, 'releve'])->name('enseignant.releve');
        Route::get('/enseignant/releve/{matiere}/export',   [NoteController::class, 'exportReleve'])->name('enseignant.releve.export');
        Route::post('/enseignant/releve/{matiere}/publier', [NoteController::class, 'publierReleve'])->name('enseignant.releve.publier');

        Route::get('/mes-informations-enseignant', function () {
            $user        = Auth::user();
            $annee       = config('app.annee_courante', '2025/2026');
            $mesMatieres = \App\Models\EnseignantMatiere::where('utilisateur_id', $user->id)
                            ->where('annee_univ', $annee)
                            ->with('matiere.ue.semestre')
                            ->get();
            return view('enseignant.informations', compact('user', 'mesMatieres', 'annee'));
        })->name('enseignant.informations');
    });

    // ── ADMIN + SECRETARIAT
    Route::middleware(['role:ADMIN,SECRETARIAT'])->group(function () {
        Route::resource('etudiants', EtudiantController::class);

        Route::get('/absences',                 [AbsenceController::class, 'index'])->name('absences.index');
        Route::post('/absences/saisir',         [AbsenceController::class, 'saisir'])->name('absences.saisir');
        Route::post('/absences/{id}/modifier',  [AbsenceController::class, 'modifier'])->name('absences.modifier');
        Route::post('/absences/{id}/justifier', [AbsenceController::class, 'justifier'])->name('absences.justifier');
        Route::delete('/absences/{id}',         [AbsenceController::class, 'destroy'])->name('absences.destroy');

        Route::get('/bulletins',                       [BulletinController::class, 'index'])->name('bulletins.index');
        Route::get('/bulletins/{etudiant}/s5',         [BulletinController::class, 'genererS5'])->name('bulletins.s5');
        Route::get('/bulletins/{etudiant}/s6',         [BulletinController::class, 'genererS6'])->name('bulletins.s6');
        Route::get('/bulletins/{etudiant}/annuel',     [BulletinController::class, 'genererAnnuel'])->name('bulletins.annuel');
        Route::post('/bulletins/{etudiant}/publier',   [BulletinController::class, 'publier'])->name('bulletins.publier');
        Route::post('/bulletins/{etudiant}/depublier', [BulletinController::class, 'depublier'])->name('bulletins.depublier');
        Route::post('/bulletins/publier-tous',         [BulletinController::class, 'publierTous'])->name('bulletins.publier-tous');

        Route::get('/jury',          [JuryController::class, 'index'])->name('jury.index');
        Route::post('/jury/valider', [JuryController::class, 'valider'])->name('jury.valider');

        Route::get('/import',        [ImportExportController::class, 'index'])->name('import.index');
        Route::post('/import/notes', [ImportExportController::class, 'importNotes'])->name('import.notes');
        Route::get('/export/releve', [ImportExportController::class, 'exportReleve'])->name('export.releve');
    });

    // ── ADMIN
    Route::middleware(['role:ADMIN'])->group(function () {
        Route::resource('utilisateurs', UtilisateurController::class);
        Route::post('/utilisateurs/{id}/reset-password',    [ResetPasswordController::class, 'adminReset'])->name('utilisateurs.reset-password');
        Route::get('/utilisateurs/{id}/affecter-matieres',  [UtilisateurController::class, 'affecterMatieres'])->name('utilisateurs.affecter-matieres');
        Route::post('/utilisateurs/{id}/affecter-matieres', [UtilisateurController::class, 'sauvegarderMatieres'])->name('utilisateurs.sauvegarder-matieres');

        Route::get('/parametres',  [ParametreController::class, 'index'])->name('parametres.index');
        Route::post('/parametres', [ParametreController::class, 'update'])->name('parametres.update');

        Route::get('/journal',                    [JournalController::class, 'index'])->name('journal.index');
        Route::delete('/journal/{id}',            [JournalController::class, 'destroy'])->name('journal.destroy');
        Route::post('/journal/destroy-selection', [JournalController::class, 'destroySelection'])->name('journal.destroy-selection');
        Route::post('/journal/vider',             [JournalController::class, 'viderTout'])->name('journal.vider');
    });
});