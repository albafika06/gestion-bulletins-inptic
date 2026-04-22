@extends('layouts.app')
@section('title', 'Modifier l\'utilisateur')
@section('page_title', 'Modifier un utilisateur')
@section('page_sub', 'Login : ' . $utilisateur->login)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-edit me-2" style="color:#2e7df7;"></i>Modifier un utilisateur</h1>
        <p>Login : <strong>{{ $utilisateur->login }}</strong> · {{ $utilisateur->role }}</p>
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

<form method="POST" action="{{ route('utilisateurs.update', $utilisateur->id) }}">
    @csrf @method('PUT')

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
                               value="{{ old('login', $utilisateur->login) }}"
                               required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Nom d'affichage <span style="color:#e24b4a;">*</span>
                        </label>
                        <input type="text"
                               name="nom_affichage"
                               class="form-control-inptic"
                               value="{{ old('nom_affichage', $utilisateur->nom_affichage) }}"
                               required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control-inptic"
                               value="{{ old('email', $utilisateur->email) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">
                            Rôle <span style="color:#e24b4a;">*</span>
                        </label>
                        <select name="role" id="roleSelect" class="form-control-inptic" required>
                            <option value="ADMIN"       {{ old('role', $utilisateur->role) == 'ADMIN'       ? 'selected' : '' }}>Administrateur</option>
                            <option value="ENSEIGNANT"  {{ old('role', $utilisateur->role) == 'ENSEIGNANT'  ? 'selected' : '' }}>Enseignant</option>
                            <option value="SECRETARIAT" {{ old('role', $utilisateur->role) == 'SECRETARIAT' ? 'selected' : '' }}>Secrétariat</option>
                            <option value="ETUDIANT"    {{ old('role', $utilisateur->role) == 'ETUDIANT'    ? 'selected' : '' }}>Étudiant</option>
                        </select>
                    </div>
                    <div class="col-12" id="etudiantDiv"
                         style="display:{{ $utilisateur->role == 'ETUDIANT' ? 'block' : 'none' }}">
                        <label class="form-label-inptic">Étudiant associé</label>
                        <select name="etudiant_id" class="form-control-inptic">
                            <option value="">-- Sélectionner --</option>
                            @foreach($etudiants as $etudiant)
                            <option value="{{ $etudiant->id }}"
                                    {{ old('etudiant_id', $utilisateur->etudiant_id) == $etudiant->id ? 'selected' : '' }}>
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
                    <span style="font-size:11px; font-weight:400; color:#6b7280; margin-left:6px;">
                        (laisser vide pour ne pas changer)
                    </span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-inptic">Nouveau mot de passe</label>
                        <input type="password"
                               name="mot_de_passe"
                               class="form-control-inptic"
                               placeholder="Laisser vide pour garder l'actuel">
                    </div>
                    <div class="col-12">
                        <label class="form-label-inptic">Confirmer</label>
                        <input type="password"
                               name="mot_de_passe_confirmation"
                               class="form-control-inptic"
                               placeholder="Répéter le nouveau mot de passe">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn-primary-inptic">
            <i class="fas fa-save"></i> Enregistrer les modifications
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
</script>
@endsection