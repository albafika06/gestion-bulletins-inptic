<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalAudit extends Model
{
    protected $table = 'journal_audit';

    const UPDATED_AT = null; // Pas de updated_at

    protected $fillable = [
        'utilisateur_id',
        'action',
        'description',
        'details',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function getCouleurAttribute(): string
    {
        return match(true) {
            str_contains($this->action, 'BULLETIN')   => 'jaune',
            str_contains($this->action, 'NOTE')       => 'bleu',
            str_contains($this->action, 'ETUDIANT')   => 'jaune',
            str_contains($this->action, 'IMPORT')     => 'violet',
            str_contains($this->action, 'CONNEXION')  => 'gris',
            str_contains($this->action, 'DECONNEXION')=> 'gris',
            str_contains($this->action, 'RELEVE')     => 'violet',
            str_contains($this->action, 'RESET')      => 'rouge',
            str_contains($this->action, 'MATIERE')    => 'bleu',
            default                                   => 'gris',
        };
    }

    public function getIconeAttribute(): string
    {
        return match(true) {
            str_contains($this->action, 'BULLETIN')   => 'fa-file-alt',
            str_contains($this->action, 'NOTE')       => 'fa-pen',
            str_contains($this->action, 'ETUDIANT')   => 'fa-user-graduate',
            str_contains($this->action, 'IMPORT')     => 'fa-file-excel',
            str_contains($this->action, 'DECONNEXION')=> 'fa-sign-out-alt',
            str_contains($this->action, 'CONNEXION')  => 'fa-sign-in-alt',
            str_contains($this->action, 'RELEVE')     => 'fa-list-alt',
            str_contains($this->action, 'RESET')      => 'fa-key',
            str_contains($this->action, 'MATIERE')    => 'fa-book',
            default                                   => 'fa-circle',
        };
    }

    // ✅ Méthode statique centrale pour enregistrer une action
    public static function log(
        string  $action,
        string  $description,
        ?int    $utilisateurId = null,
        ?string $details       = null
    ): void {
        try {
            static::create([
                'utilisateur_id' => $utilisateurId ?? auth()->id(),
                'action'         => $action,
                'description'    => $description,
                'details'        => $details,
                'ip_address'     => request()->ip(),
                'user_agent'     => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('JournalAudit::log failed: ' . $e->getMessage());
        }
    }
}