<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoyenneUE extends Model
{
    protected $table = 'moyennes_ue';

    public $timestamps = false;

    protected $fillable = [
        'etudiant_id',
        'ue_id',
        'annee_univ',
        'moyenne_ue',
        'credits_ue',
        'credits_acquis',
        'statut',
    ];

    protected $casts = [
        'moyenne_ue'     => 'float',
        'credits_ue'     => 'integer',
        'credits_acquis' => 'integer',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function ue()
    {
        return $this->belongsTo(UniteEnseignement::class, 'ue_id');
    }
}
