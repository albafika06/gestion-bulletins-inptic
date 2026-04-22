@extends('layouts.app')
@section('title', 'Modifier l\'étudiant')
@section('page_title', 'Modifier un étudiant')
@section('page_sub', $etudiant->nom . ' ' . $etudiant->prenom . ' · ' . $etudiant->matricule)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-edit me-2" style="color:#2e7df7;"></i>Modifier un étudiant</h1>
        <p>Matricule : <strong>{{ $etudiant->matricule }}</strong></p>
    </div>
    <a href="{{ route('etudiants.index') }}" class="btn-secondary-inptic">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

@if($errors->any())
<div class="alert-inptic-error mb-3">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<form method="POST" action="{{ route('etudiants.update', $etudiant->id) }}">
    @csrf @method('PUT')

    <div class="row g-4">

        {{-- Identité --}}
        <div class="col-md-6">
            <div class="card-white">
                <div class="card-white-title">
                    <i class="fas fa-id-card"></i> Identité
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-inptic">
                            Matricule <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="matricule"
                               class="form-control-inptic"
                               value="{{ old('matricule', $etudiant->matricule) }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">Sexe</label>
                        <select name="sexe" class="form-control-inptic">
                            <option value="">-- Choisir --</option>
                            <option value="M" {{ old('sexe', $etudiant->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe', $etudiant->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">
                            Nom <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="nom"
                               class="form-control-inptic"
                               value="{{ old('nom', $etudiant->nom) }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">
                            Prénom(s) <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="prenom"
                               class="form-control-inptic"
                               value="{{ old('prenom', $etudiant->prenom) }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">Date de naissance</label>
                        <input type="date"
                               name="date_naissance"
                               class="form-control-inptic"
                               value="{{ old('date_naissance', $etudiant->date_naissance?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">Lieu de naissance</label>
                        <input type="text"
                               name="lieu_naissance"
                               class="form-control-inptic"
                               value="{{ old('lieu_naissance', $etudiant->lieu_naissance) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Parcours --}}
        <div class="col-md-6">
            <div class="card-white">
                <div class="card-white-title">
                    <i class="fas fa-graduation-cap"></i> Parcours académique
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-inptic">Type de Baccalauréat</label>
                        <select name="type_bac" class="form-control-inptic">
                            <option value="">-- Choisir --</option>
                            <option value="C"   {{ old('type_bac', $etudiant->type_bac) == 'C'   ? 'selected' : '' }}>Série C</option>
                            <option value="D"   {{ old('type_bac', $etudiant->type_bac) == 'D'   ? 'selected' : '' }}>Série D</option>
                            <option value="A"   {{ old('type_bac', $etudiant->type_bac) == 'A'   ? 'selected' : '' }}>Série A</option>
                            <option value="G"   {{ old('type_bac', $etudiant->type_bac) == 'G'   ? 'selected' : '' }}>Série G</option>
                            <option value="F"   {{ old('type_bac', $etudiant->type_bac) == 'F'   ? 'selected' : '' }}>Série F</option>
                            <option value="STI" {{ old('type_bac', $etudiant->type_bac) == 'STI' ? 'selected' : '' }}>STI</option>
                            <option value="Autre" {{ old('type_bac', $etudiant->type_bac) == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">Établissement d'origine</label>
                        <input type="text"
                               name="etablissement_origine"
                               class="form-control-inptic"
                               value="{{ old('etablissement_origine', $etudiant->etablissement_origine) }}"
                               placeholder="Ex: Lycée National Léon Mba">
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">Statut</label>
                        <select name="actif" class="form-control-inptic">
                            <option value="1" {{ $etudiant->actif ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ !$etudiant->actif ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn-primary-inptic">
            <i class="fas fa-save"></i> Enregistrer les modifications
        </button>
        <a href="{{ route('etudiants.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>

</form>

@endsection