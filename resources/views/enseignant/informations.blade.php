@extends('layouts.app')
@section('title', 'Mes Informations')
@section('page_title', 'Mes Informations')
@section('page_sub', 'Votre profil enseignant · ' . $annee)

@section('content')

<div class="page-header">
    <h1><i class="fas fa-id-card me-2" style="color:#2e7df7;"></i>Mes Informations</h1>
    <p>Profil enseignant · Année <strong>{{ $annee }}</strong></p>
</div>

<div class="row g-4">

    {{-- Profil --}}
    <div class="col-md-5">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-user"></i> Profil
            </div>

            <div class="text-center mb-4">
                <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#1e2a3a,#2e7df7); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; margin:0 auto 12px;">
                    {{ strtoupper(substr($user->nom_affichage, 0, 2)) }}
                </div>
                <div style="font-weight:700; color:#1e2a3a; font-size:16px;">
                    {{ $user->nom_affichage }}
                </div>
                <div style="margin-top:6px;">
                    <span style="background:#e1d5e7; color:#3c3489; padding:3px 12px; border-radius:20px; font-size:11px; font-weight:600;">
                        Enseignant
                    </span>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Login</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $user->login }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Email</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $user->email ?? '—' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Rôle</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">Enseignant</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Dernière connexion</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">
                        {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Année universitaire</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $annee }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Matières assignées --}}
    <div class="col-md-7">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-book"></i>
                Mes matières assignées
                <span class="badge-bleu ms-2">{{ $mesMatieres->count() }}</span>
            </div>

            @if($mesMatieres->count() > 0)
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($mesMatieres as $em)
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px 16px; background:#fff; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-weight:600; color:#1e2a3a; font-size:13px; margin-bottom:3px;">
                            {{ $em->matiere->libelle }}
                        </div>
                        <div style="font-size:11px; color:#6b7280;">
                            {{ $em->matiere->ue->code }} ·
                            {{ $em->matiere->ue->semestre->libelle ?? '' }} ·
                            Coeff: {{ $em->matiere->coefficient }} ·
                            {{ $em->matiere->credits }} crédit(s)
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('enseignant.saisir', $em->matiere_id) }}"
                           class="btn-primary-inptic"
                           style="font-size:11px; padding:5px 10px;">
                            <i class="fas fa-pen"></i> Saisir
                        </a>
                        <a href="{{ route('enseignant.releve', $em->matiere_id) }}"
                           class="btn-secondary-inptic"
                           style="font-size:11px; padding:5px 10px;">
                            <i class="fas fa-list-alt"></i> Relevé
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center" style="padding:32px; color:#9ca3af;">
                <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                Aucune matière assignée pour cette année.
            </div>
            @endif
        </div>
    </div>

</div>

@endsection