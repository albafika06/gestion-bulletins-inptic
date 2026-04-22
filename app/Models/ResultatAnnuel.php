<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatAnnuel extends Model
{
    protected $table = 'resultats_annuels';

    public $timestamps = false;

    protected $fillable = [
        'etudiant_id',
        'annee_univ',
        'moyenne_s5',
        'moyenne_s6',
        'moyenne_annuelle',
        'rang_annuel',
        'credits_total',
        'credits_acquis',
        'decision_jury',
        'mention',
        'stat_min',
        'stat_max',
        'stat_moyenne_classe',
        'date_jury',
        'publie_etudiant',
    ];

    protected $casts = [
        'moyenne_s5'          => 'float',
        'moyenne_s6'          => 'float',
        'moyenne_annuelle'    => 'float',
        'stat_min'            => 'float',
        'stat_max'            => 'float',
        'stat_moyenne_classe' => 'float',
        'date_jury'           => 'datetime',
        'publie_etudiant'     => 'boolean',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }
}