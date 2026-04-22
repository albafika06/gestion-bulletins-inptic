<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'utilisateur_id',
        'type',
        'titre',
        'message',
        'lien',
        'lu',
        'reference_id',
        'annee_univ',
    ];

    protected $casts = [
        'lu'         => 'boolean',
        'created_at' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    // Créer une notification facilement
    public static function creer(
        int    $utilisateurId,
        string $type,
        string $titre,
        string $message,
        string $lien = null,
        int    $referenceId = null
    ): self {
        return self::create([
            'utilisateur_id' => $utilisateurId,
            'type'           => $type,
            'titre'          => $titre,
            'message'        => $message,
            'lien'           => $lien,
            'lu'             => false,
            'reference_id'   => $referenceId,
            'annee_univ'     => config('app.annee_courante', '2025/2026'),
        ]);
    }

    // Notifier tous les étudiants
    public static function notifierTousEtudiants(
        string $type,
        string $titre,
        string $message,
        string $lien = null,
        int    $referenceId = null
    ): void {
        $annee     = config('app.annee_courante', '2025/2026');
        $etudiants = User::where('role', 'ETUDIANT')->where('actif', 1)->get();

        foreach ($etudiants as $etudiant) {
            self::creer(
                $etudiant->id, $type, $titre,
                $message, $lien, $referenceId
            );
        }
    }
}