<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnseignantMatiere extends Model
{
    protected $table = 'enseignant_matiere';

    public $timestamps = false;

    protected $fillable = [
        'utilisateur_id',
        'matiere_id',
        'annee_univ',
    ];

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}