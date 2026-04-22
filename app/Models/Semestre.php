<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $table = 'semestres';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'libelle',
        'annee_universitaire',
        'credits_total',
        'ordre',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function unitesEnseignement()
    {
        return $this->hasMany(UniteEnseignement::class, 'semestre_id');
    }

    public function resultatsSemestres()
    {
        return $this->hasMany(ResultatSemestre::class, 'semestre_id');
    }
}
