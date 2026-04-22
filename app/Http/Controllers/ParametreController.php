<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $parametres = Parametre::orderBy('cle')->get();
        return view('parametres.index', compact('parametres'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'parametres'       => 'required|array',
            'parametres.*.cle' => 'required|string',
            'parametres.*.valeur' => 'required|string',
        ]);

        foreach ($request->parametres as $item) {
            Parametre::where('cle', $item['cle'])
                     ->where('modifiable', 1)
                     ->update(['valeur' => $item['valeur']]);
        }

        return redirect()->route('parametres.index')
                         ->with('success', 'Paramètres mis à jour avec succès.');
    }
}