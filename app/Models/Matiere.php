<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'matieres';

    public $timestamps = false;

    protected $fillable = [
        'libelle',
        'code',
        'ue_id',
        'coefficient',
        'credits',
        'ordre',
        'actif',
    ];

    protected $casts = [
        'coefficient' => 'float',
        'actif'       => 'boolean',
    ];

    public function ue()
    {
        return $this->belongsTo(UniteEnseignement::class, 'ue_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'matiere_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'matiere_id');
    }

    public function moyennesMatieres()
    {
        return $this->hasMany(MoyenneMatiere::class, 'matiere_id');
    }

    public function statistiques()
    {
        return $this->hasMany(StatistiqueMatiere::class, 'matiere_id');
    }

    public function enseignants()
    {
        return $this->hasMany(EnseignantMatiere::class, 'matiere_id');
    }
}
