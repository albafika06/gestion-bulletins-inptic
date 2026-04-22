@extends('layouts.app')
@section('title', 'Ajouter un étudiant')
@section('page_title', 'Ajouter un étudiant')
@section('page_sub', 'Nouveau dossier étudiant')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-plus me-2" style="color:#2e7df7;"></i>Ajouter un étudiant</h1>
        <p>Remplissez les informations du nouvel étudiant</p>
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

<form method="POST" action="{{ route('etudiants.store') }}">
    @csrf

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
                               value="{{ old('matricule') }}"
                               placeholder="Ex: ASUR-001"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">Sexe</label>
                        <select name="sexe" class="form-control-inptic">
                            <option value="">-- Choisir --</option>
                            <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">
                            Nom <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="nom"
                               class="form-control-inptic"
                               value="{{ old('nom') }}"
                               placeholder="Nom de famille"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">
                            Prénom(s) <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="prenom"
                               class="form-control-inptic"
                               value="{{ old('prenom') }}"
                               placeholder="Prénom(s)"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">Date de naissance</label>
                        <input type="date"
                               name="date_naissance"
                               class="form-control-inptic"
                               value="{{ old('date_naissance') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-inptic">Lieu de naissance</label>
                        <input type="text"
                               name="lieu_naissance"
                               class="form-control-inptic"
                               value="{{ old('lieu_naissance') }}"
                               placeholder="Ex: Libreville">
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
                            <option value="C"  {{ old('type_bac') == 'C'  ? 'selected' : '' }}>Série C — Maths et Sciences physiques</option>
                            <option value="D"  {{ old('type_bac') == 'D'  ? 'selected' : '' }}>Série D — Sciences naturelles</option>
                            <option value="A"  {{ old('type_bac') == 'A'  ? 'selected' : '' }}>Série A — Lettres et Sciences humaines</option>
                            <option value="G"  {{ old('type_bac') == 'G'  ? 'selected' : '' }}>Série G — Sciences économiques</option>
                            <option value="F"  {{ old('type_bac') == 'F'  ? 'selected' : '' }}>Série F — Technique</option>
                            <option value="STI" {{ old('type_bac') == 'STI' ? 'selected' : '' }}>STI — Sciences et Technologies Industrielles</option>
                            <option value="Autre" {{ old('type_bac') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">Établissement d'origine</label>
                        <input type="text"
                               name="etablissement_origine"
                               class="form-control-inptic"
                               value="{{ old('etablissement_origine') }}"
                               placeholder="Ex: Lycée National Léon Mba">
                    </div>
                    <div class="col-12">
                        <div style="background:#e6f1fb; border:1px solid #b5d4f4; border-radius:8px; padding:12px 16px; font-size:12px; color:#0c447c;">
                            <i class="fas fa-info-circle me-2"></i>
                            L'année universitaire sera automatiquement définie à
                            <strong>{{ config('app.annee_courante', '2025/2026') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn-primary-inptic">
            <i class="fas fa-save"></i> Enregistrer l'étudiant
        </button>
        <a href="{{ route('etudiants.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>

</form>

@endsection