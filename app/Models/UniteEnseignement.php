<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniteEnseignement extends Model
{
    protected $table = 'unites_enseignement';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'libelle',
        'semestre_id',
        'credits_total',
        'ordre',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }

    public function matieres()
    {
        return $this->hasMany(Matiere::class, 'ue_id')->orderBy('ordre');
    }

    public function moyennesUE()
    {
        return $this->hasMany(MoyenneUE::class, 'ue_id');
    }
}
