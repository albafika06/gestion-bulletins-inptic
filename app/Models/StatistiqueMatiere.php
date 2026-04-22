<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistiqueMatiere extends Model
{
    protected $table = 'statistiques_matieres';

    public $timestamps = false;

    protected $fillable = [
        'matiere_id',
        'annee_univ',
        'nb_notes',
        'moyenne_classe',
        'note_min',
        'note_max',
        'ecart_type',
    ];

    protected $casts = [
        'moyenne_classe' => 'float',
        'note_min'       => 'float',
        'note_max'       => 'float',
        'ecart_type'     => 'float',
    ];

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}
