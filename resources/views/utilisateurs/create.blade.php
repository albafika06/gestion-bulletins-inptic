@extends('layouts.app')
@section('title', 'Créer un utilisateur')
@section('page_title', 'Créer un utilisateur')
@section('page_sub', 'Nouveau compte d\'accès')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-plus me-2" style="color:#2e7df7;"></i>Créer un utilisateur</h1>
        <p>Nouveau compte d'accès à l'application</p>
    </div>
    <a href="{{ route('utilisateurs.index') }}" class="btn-secondary-inptic">
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

<form method="POST" action="{{ route('utilisateurs.store') }}">
    @csrf

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card-white">
                <div class="card-white-title">
                    <i class="fas fa-id-badge"></i> Informations du compte
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Login <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="login"
                               class="form-control-inptic"
                               value="{{ old('login') }}"
                               placeholder="Ex: jdupont"
                               required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Nom d'affichage <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="nom_affichage"
                               class="form-control-inptic"
                               value="{{ old('nom_affichage') }}"
                               placeholder="Ex: Jean Dupont"
                               required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Email <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               class="form-control-inptic"
                               value="{{ old('email') }}"
                               placeholder="email@domaine.com"
                               required>
                        <div style="font-size:11px;color:#6b7280;margin-top:4px;">
                            <i class="fas fa-info-circle"></i>
                            Les identifiants seront envoyés à cette adresse.
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Rôle <span style="color:#e24b4a;">*</span>
                        </label>
                        <select name="role" id="roleSelect" class="form-control-inptic" required>
                            <option value="">-- Choisir un rôle --</option>
                            <option value="ADMIN"       {{ old('role') == 'ADMIN'       ? 'selected' : '' }}>Administrateur</option>
                            <option value="ENSEIGNANT"  {{ old('role') == 'ENSEIGNANT'  ? 'selected' : '' }}>Enseignant</option>
                            <option value="SECRETARIAT" {{ old('role') == 'SECRETARIAT' ? 'selected' : '' }}>Secrétariat Pédagogique</option>
                            <option value="ETUDIANT"    {{ old('role') == 'ETUDIANT'    ? 'selected' : '' }}>Étudiant</option>
                        </select>
                    </div>
                    <div class="col-12" id="etudiantDiv" style="display:none;">
                        <label class="form-label-inptic">Étudiant associé</label>
                        <select name="etudiant_id" class="form-control-inptic">
                            <option value="">-- Sélectionner l'étudiant --</option>
                            @foreach($etudiants as $etudiant)
                            <option value="{{ $etudiant->id }}"
                                    {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                ({{ $etudiant->matricule }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card-white">
                <div class="card-white-title">
                    <i class="fas fa-lock"></i> Mot de passe
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Mot de passe <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="password"
                               name="mot_de_passe"
                               class="form-control-inptic"
                               placeholder="Minimum 6 caractères"
                               required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Confirmer <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="password"
                               name="mot_de_passe_confirmation"
                               class="form-control-inptic"
                               placeholder="Répéter le mot de passe"
                               required>
                    </div>
                    <div class="col-12">
                        <div style="background:#faeeda;border:1px solid #fac775;border-radius:8px;padding:12px 16px;font-size:12px;color:#633806;">
                            <i class="fas fa-shield-alt me-2"></i>
                            Le mot de passe doit contenir au moins <strong>6 caractères</strong>.
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="background:#e6f1fb;border:1px solid #b3d4f5;border-radius:8px;padding:12px 16px;font-size:12px;color:#0c447c;">
                            <i class="fas fa-envelope me-2"></i>
                            Les identifiants (login + mot de passe) seront <strong>automatiquement envoyés</strong> à l'adresse email renseignée.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn-primary-inptic">
            <i class="fas fa-save"></i> Créer l'utilisateur
        </button>
        <a href="{{ route('utilisateurs.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>

</form>

@endsection

@section('scripts')
<script>
    document.getElementById('roleSelect').addEventListener('change', function() {
        document.getElementById('etudiantDiv').style.display =
            this.value === 'ETUDIANT' ? 'block' : 'none';
    });
    if (document.getElementById('roleSelect').value === 'ETUDIANT') {
        document.getElementById('etudiantDiv').style.display = 'block';
    }
</script>
@endsection