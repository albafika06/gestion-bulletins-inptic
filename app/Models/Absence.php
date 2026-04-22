<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $table = 'absences';

    public $timestamps = false;

    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'annee_univ',
        'heures',
        'justifie',
        'saisie_par',
    ];

    protected $casts = [
        'heures'   => 'float',
        'justifie' => 'boolean',
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
