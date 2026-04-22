<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $table = 'etudiants';

    public $timestamps = false;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'type_bac',
        'etablissement_origine',
        'annee_universitaire',
        'actif',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'actif'          => 'boolean',
    ];

    // Nom complet
    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    // Relations
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'etudiant_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'etudiant_id');
    }

    public function moyennesMatieres()
    {
        return $this->hasMany(MoyenneMatiere::class, 'etudiant_id');
    }

    public function moyennesUE()
    {
        return $this->hasMany(MoyenneUE::class, 'etudiant_id');
    }

    public function resultatsSemestres()
    {
        return $this->hasMany(ResultatSemestre::class, 'etudiant_id');
    }

    public function resultatAnnuel()
    {
        return $this->hasOne(ResultatAnnuel::class, 'etudiant_id');
    }

    public function utilisateur()
    {
        return $this->hasOne(Utilisateur::class, 'etudiant_id');
    }
}