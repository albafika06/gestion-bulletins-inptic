<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatSemestre extends Model
{
    protected $table = 'resultats_semestres';

    public $timestamps = false;

    protected $fillable = [
        'etudiant_id',
        'semestre_id',
        'annee_univ',
        'moyenne_semestre',
        'rang',
        'credits_total',
        'credits_acquis',
        'valide',
        'statut_decision',
        'stat_min',
        'stat_max',
        'stat_moyenne_classe',
        'stat_ecart_type',
        'publie_etudiant',
    ];

    protected $casts = [
        'moyenne_semestre'    => 'float',
        'valide'              => 'boolean',
        'publie_etudiant'     => 'boolean',
        'stat_min'            => 'float',
        'stat_max'            => 'float',
        'stat_moyenne_classe' => 'float',
        'stat_ecart_type'     => 'float',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }
}