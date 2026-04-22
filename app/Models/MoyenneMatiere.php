<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoyenneMatiere extends Model
{
    protected $table = 'moyennes_matieres';

    public $timestamps = false;

    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'annee_univ',
        'note_cc',
        'note_examen',
        'note_rattrapage',
        'moyenne_brute',
        'heures_absence',
        'penalite_absence',
        'moyenne_finale',
        'rattrapage_utilise',
    ];

    protected $casts = [
        'note_cc'            => 'float',
        'note_examen'        => 'float',
        'note_rattrapage'    => 'float',
        'moyenne_brute'      => 'float',
        'heures_absence'     => 'float',
        'penalite_absence'   => 'float',
        'moyenne_finale'     => 'float',
        'rattrapage_utilise' => 'boolean',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}
