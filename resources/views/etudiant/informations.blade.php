@extends('layouts.app')
@section('title', 'Mes Informations')
@section('page_title', 'Mes Informations')
@section('page_sub', 'Votre dossier personnel')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-id-card me-2" style="color:#2e7df7;"></i>Mes Informations</h1>
    <p>Votre dossier académique — <strong>{{ $annee }}</strong></p>
</div>

<div class="row g-4">

    <!-- Identité -->
    <div class="col-md-6">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-user"></i> Identité
            </div>

            <!-- Avatar -->
            <div class="text-center mb-4">
                <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#1e2a3a,#2e7df7); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; margin:0 auto 12px;">
                    {{ strtoupper(substr($etudiant->nom, 0, 1)) }}
                </div>
                <div style="font-weight:700; color:#1e2a3a; font-size:16px;">
                    {{ $etudiant->nom }} {{ $etudiant->prenom }}
                </div>
                <div style="font-size:12px; color:#6b7280; margin-top:4px;">
                    <span style="background:#e6f1fb; color:#0c447c; padding:2px 10px; border-radius:20px; font-size:11px;">
                        {{ $etudiant->matricule }}
                    </span>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Nom</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->nom }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Prénom(s)</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->prenom }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Sexe</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">
                        {{ $etudiant->sexe == 'M' ? 'Masculin' : ($etudiant->sexe == 'F' ? 'Féminin' : '—') }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Date de naissance</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">
                        {{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Lieu de naissance</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->lieu_naissance ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Parcours académique -->
    <div class="col-md-6">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-graduation-cap"></i> Parcours Académique
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Matricule</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->matricule }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Année universitaire</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->annee_universitaire }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Formation</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">LP ASUR</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Type de Baccalauréat</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->type_bac ?? '—' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px;">
                    <span style="font-size:12px; color:#6b7280; font-weight:500;">Établissement d'origine</span>
                    <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $etudiant->etablissement_origine ?? '—' }}</span>
                </div>
            </div>

            <!-- Résultat annuel si disponible -->
            @if($resultatAnnuel && $resultatAnnuel->publie_etudiant)
            <div style="background:#e6f1fb; border:1px solid #b5d4f4; border-radius:10px; padding:16px;">
                <div style="font-size:12px; font-weight:600; color:#0c447c; margin-bottom:12px;">
                    <i class="fas fa-trophy me-1"></i> Résultat annuel
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div style="text-align:center; background:#fff; border-radius:8px; padding:10px;">
                        <div style="font-size:20px; font-weight:700; color:#1e2a3a;">
                            {{ $resultatAnnuel->moyenne_annuelle ? number_format($resultatAnnuel->moyenne_annuelle, 2) : '—' }}
                        </div>
                        <div style="font-size:11px; color:#6b7280;">Moyenne annuelle</div>
                    </div>
                    <div style="text-align:center; background:#fff; border-radius:8px; padding:10px;">
                        <div style="font-size:20px; font-weight:700; color:#1e2a3a;">
                            {{ $resultatAnnuel->credits_acquis }}/60
                        </div>
                        <div style="font-size:11px; color:#6b7280;">Crédits acquis</div>
                    </div>
                </div>
                <div style="text-align:center; margin-top:10px;">
                    @php
                        $decisions = ['DIPLOME'=>'Diplômé(e)','REPRISE_SOUTENANCE'=>'Reprise Soutenance','REDOUBLE'=>'Redouble','EN_ATTENTE'=>'En attente'];
                        $couleurDecision = $resultatAnnuel->decision_jury == 'DIPLOME' ? '#27500a' : ($resultatAnnuel->decision_jury == 'REDOUBLE' ? '#791f1f' : '#633806');
                        $bgDecision = $resultatAnnuel->decision_jury == 'DIPLOME' ? '#eaf3de' : ($resultatAnnuel->decision_jury == 'REDOUBLE' ? '#fcebeb' : '#faeeda');
                    @endphp
                    <span style="background:{{ $bgDecision }}; color:{{ $couleurDecision }}; padding:5px 16px; border-radius:20px; font-size:12px; font-weight:600;">
                        {{ $decisions[$resultatAnnuel->decision_jury] ?? '—' }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection