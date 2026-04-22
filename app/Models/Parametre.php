<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    public $timestamps = false;

    protected $fillable = [
        'cle',
        'valeur',
        'description',
        'modifiable',
    ];

    protected $casts = [
        'modifiable' => 'boolean',
    ];

    // Récupérer une valeur par sa clé
    public static function getValeur(string $cle, $defaut = null)
    {
        $param = self::where('cle', $cle)->first();
        return $param ? $param->valeur : $defaut;
    }
}
