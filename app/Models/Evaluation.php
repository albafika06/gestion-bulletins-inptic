<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluations';

    public $timestamps = false;

    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'type_eval',
        'note',
        'annee_univ',
        'saisie_par',
    ];

    protected $casts = [
        'note'        => 'float',
        'date_saisie' => 'datetime',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function saisiePar()
    {
        return $this->belongsTo(User::class, 'saisie_par');
    }
}
