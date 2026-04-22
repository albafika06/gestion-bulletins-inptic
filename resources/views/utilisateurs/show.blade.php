@extends('layouts.app')

@section('title', 'Utilisateur — ' . $utilisateur->nom_affichage)
@section('page_title', 'Fiche utilisateur')
@section('page_sub', 'Login : ' . $utilisateur->login)

@section('styles')
.role-admin      { background:#e1d5e7; color:#6a1b9a; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.role-enseignant { background:#dae8fc; color:#1565c0; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.role-secretariat{ background:#d5e8d4; color:#2e7d32; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.role-etudiant   { background:#fff2cc; color:#e65100; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.info-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f1f2f4; font-size:13px; }
.info-row:last-child { border-bottom:none; }
.info-lbl { color:#6b7280; }
.info-val { font-weight:600; color:#1e2a3a; }
@endsection

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-circle me-2" style="color:#2e7df7;"></i>{{ $utilisateur->nom_affichage }}</h1>
        <p>Login : <strong>{{ $utilisateur->login }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('utilisateurs.edit', $utilisateur->id) }}" class="btn-primary-inptic">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <a href="{{ route('utilisateurs.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="card-white" style="max-width:600px;">
    <div class="card-white-title"><i class="fas fa-id-card"></i> Informations du compte</div>

    <div class="info-row">
        <span class="info-lbl">Login</span>
        <span class="info-val">{{ $utilisateur->login }}</span>
    </div>
    <div class="info-row">
        <span class="info-lbl">Nom d'affichage</span>
        <span class="info-val">{{ $utilisateur->nom_affichage }}</span>
    </div>
    <div class="info-row">
        <span class="info-lbl">Email</span>
        <span class="info-val">{{ $utilisateur->email ?? '—' }}</span>
    </div>
    <div class="info-row">
        <span class="info-lbl">Rôle</span>
        <span>
            @if($utilisateur->role == 'ADMIN')
                <span class="role-admin">Administrateur</span>
            @elseif($utilisateur->role == 'ENSEIGNANT')
                <span class="role-enseignant">Enseignant</span>
            @elseif($utilisateur->role == 'SECRETARIAT')
                <span class="role-secretariat">Secrétariat</span>
            @elseif($utilisateur->role == 'ETUDIANT')
                <span class="role-etudiant">Étudiant</span>
            @endif
        </span>
    </div>
    <div class="info-row">
        <span class="info-lbl">Statut</span>
        <span>
            @if($utilisateur->actif)
                <span class="badge-vert">Actif</span>
            @else
                <span class="badge-rouge">Inactif</span>
            @endif
        </span>
    </div>
    <div class="info-row">
        <span class="info-lbl">Dernière connexion</span>
        <span class="info-val">
            {{ $utilisateur->derniere_connexion ? $utilisateur->derniere_connexion->format('d/m/Y à H:i') : 'Jamais' }}
        </span>
    </div>
    @if($utilisateur->etudiant)
    <div class="info-row">
        <span class="info-lbl">Étudiant associé</span>
        <span class="info-val">
            <a href="{{ route('etudiants.show', $utilisateur->etudiant->id) }}" style="color:#2e7df7;">
                {{ $utilisateur->etudiant->nom }} {{ $utilisateur->etudiant->prenom }}
                ({{ $utilisateur->etudiant->matricule }})
            </a>
        </span>
    </div>
    @endif
</div>

@endsection
