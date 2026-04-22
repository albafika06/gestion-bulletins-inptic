@extends('layouts.app')
@section('title', 'Affecter des matières')
@section('page_title', 'Affecter des matières')
@section('page_sub', 'Enseignant : ' . $utilisateur->nom_affichage)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chalkboard-teacher me-2" style="color:#2e7df7;"></i>
            Affecter des matières
        </h1>
        <p>Enseignant : <strong>{{ $utilisateur->nom_affichage }}</strong> · Année : <strong>{{ $annee }}</strong></p>
    </div>
    <a href="{{ route('utilisateurs.index') }}" class="btn-secondary-inptic">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" action="{{ route('utilisateurs.sauvegarder-matieres', $utilisateur->id) }}">
    @csrf

    @foreach($semestres as $semestre)
    <div class="card-white mb-3">
        <div class="card-white-title">
            <i class="fas fa-book-open"></i>
            {{ $semestre->libelle }}
        </div>

        @foreach($semestre->unitesEnseignement as $ue)
        <div style="margin-bottom:16px;">
            <div style="font-size:12px; font-weight:600; color:#1e2a3a; background:#f8f9ff; border:1px solid #e5e7eb; border-radius:8px; padding:8px 14px; margin-bottom:8px;">
                <i class="fas fa-layer-group me-2" style="color:#2e7df7;"></i>
                {{ $ue->code }} — {{ $ue->libelle }}
            </div>

            <div class="row g-2 ps-3">
                @foreach($ue->matieres as $matiere)
                <div class="col-md-4">
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color 0.15s; background:#fff;"
                           onmouseover="this.style.borderColor='#2e7df7'"
                           onmouseout="this.style.borderColor='{{ in_array($matiere->id, $matieresAssignees) ? '#2e7df7' : '#e5e7eb' }}'">
                        <input type="checkbox"
                               name="matieres[]"
                               value="{{ $matiere->id }}"
                               {{ in_array($matiere->id, $matieresAssignees) ? 'checked' : '' }}
                               style="width:16px; height:16px; accent-color:#2e7df7; flex-shrink:0;">
                        <div>
                            <div style="font-size:12px; font-weight:500; color:#1e2a3a;">
                                {{ $matiere->libelle }}
                            </div>
                            <div style="font-size:10px; color:#6b7280;">
                                Coeff: {{ $matiere->coefficient }} · {{ $matiere->credits }} crédit(s)
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="d-flex gap-2">
        <button type="submit" class="btn-primary-inptic">
            <i class="fas fa-save"></i> Enregistrer les affectations
        </button>
        <a href="{{ route('utilisateurs.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>

@endsection