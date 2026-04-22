<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'utilisateurs';

    public $timestamps = false;

    protected $fillable = [
        'login',
        'mot_de_passe',
        'nom_affichage',
        'email',
        'role',
        'etudiant_id',
        'actif',
        'derniere_connexion',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    protected $casts = [
        'actif'            => 'boolean',
        'derniere_connexion'=> 'datetime',
    ];

    // Laravel utilise ce champ comme mot de passe
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    // Vérifier le rôle
    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }

    public function isEnseignant()
    {
        return $this->role === 'ENSEIGNANT';
    }

    public function isSecretariat()
    {
        return $this->role === 'SECRETARIAT';
    }

    public function isEtudiant()
    {
        return $this->role === 'ETUDIANT';
    }

    // Relations
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function matieres()
    {
        return $this->hasMany(EnseignantMatiere::class, 'utilisateur_id');
    }
}
