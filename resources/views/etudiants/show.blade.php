@extends('layouts.app')
@section('title', 'Fiche étudiant')
@section('page_title', 'Fiche étudiant')
@section('page_sub', $etudiant->nom . ' ' . $etudiant->prenom)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-graduate me-2" style="color:#2e7df7;"></i>
            {{ $etudiant->nom }} {{ $etudiant->prenom }}
        </h1>
        <p>Matricule : <strong>{{ $etudiant->matricule }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('etudiants.edit', $etudiant->id) }}" class="btn-primary-inptic">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <a href="{{ route('etudiants.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-id-card"></i> Identité
            </div>

            <div class="text-center mb-4">
                <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#1e2a3a,#2e7df7); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:24px; margin:0 auto 12px;">
                    {{ strtoupper(substr($etudiant->nom, 0, 1)) }}
                </div>
                <div style="font-weight:700; color:#1e2a3a; font-size:16px;">
                    {{ $etudiant->nom }} {{ $etudiant->prenom }}
                </div>
                <div style="margin-top:6px;">
                    <span class="badge-bleu">{{ $etudiant->matricule }}</span>
                    @if($etudiant->actif)
                        <span class="badge-vert ms-1">Actif</span>
                    @else
                        <span class="badge-rouge ms-1">Inactif</span>
                    @endif
                </div>
            </div>

            @php
                $infos = [
                    ['label' => 'Sexe',              'value' => $etudiant->sexe == 'M' ? 'Masculin' : ($etudiant->sexe == 'F' ? 'Féminin' : '—')],
                    ['label' => 'Date de naissance', 'value' => $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : '—'],
                    ['label' => 'Lieu de naissance', 'value' => $etudiant->lieu_naissance ?? '—'],
                    ['label' => 'Type de Bac',       'value' => $etudiant->type_bac ?? '—'],
                    ['label' => 'Établissement',     'value' => $etudiant->etablissement_origine ?? '—'],
                    ['label' => 'Année univ.',       'value' => $etudiant->annee_universitaire],
                ];
            @endphp

            @foreach($infos as $info)
            <div style="display:flex; justify-content:space-between; padding:10px 14px; background:#f8f9ff; border-radius:8px; margin-bottom:8px;">
                <span style="font-size:12px; color:#6b7280; font-weight:500;">{{ $info['label'] }}</span>
                <span style="font-size:13px; color:#1e2a3a; font-weight:600;">{{ $info['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-7">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-chart-bar"></i> Résultats
            </div>
            @php
                $annee = config('app.annee_courante', '2025/2026');
                $ra    = \App\Models\ResultatAnnuel::where('etudiant_id', $etudiant->id)
                            ->where('annee_univ', $annee)->first();
                $rsS5  = \App\Models\ResultatSemestre::where('etudiant_id', $etudiant->id)
                            ->whereHas('semestre', fn($q) => $q->where('code', 'S5'))
                            ->where('annee_univ', $annee)->first();
                $rsS6  = \App\Models\ResultatSemestre::where('etudiant_id', $etudiant->id)
                            ->whereHas('semestre', fn($q) => $q->where('code', 'S6'))
                            ->where('annee_univ', $annee)->first();
            @endphp

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div style="border:1px solid #e5e7eb; border-radius:8px; padding:14px; text-align:center;">
                        <div style="font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Moyenne S5</div>
                        @php $ms5 = $rsS5?->moyenne_semestre; @endphp
                        <div style="font-size:22px; font-weight:700; color:{{ $ms5 >= 10 ? '#27500a' : ($ms5 >= 6 ? '#e65100' : '#c62828') }};">
                            {{ $ms5 !== null ? number_format($ms5, 2) : '—' }}
                        </div>
                        <div style="font-size:11px; color:#6b7280; margin-top:4px;">
                            {{ $rsS5?->credits_acquis ?? 0 }}/30 crédits
                            @if($rsS5) · Rang {{ $rsS5->rang ?? 'N/A' }} @endif
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div style="border:1px solid #e5e7eb; border-radius:8px; padding:14px; text-align:center;">
                        <div style="font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Moyenne S6</div>
                        @php $ms6 = $rsS6?->moyenne_semestre; @endphp
                        <div style="font-size:22px; font-weight:700; color:{{ $ms6 >= 10 ? '#27500a' : ($ms6 >= 6 ? '#e65100' : '#c62828') }};">
                            {{ $ms6 !== null ? number_format($ms6, 2) : '—' }}
                        </div>
                        <div style="font-size:11px; color:#6b7280; margin-top:4px;">
                            {{ $rsS6?->credits_acquis ?? 0 }}/30 crédits
                            @if($rsS6) · Rang {{ $rsS6->rang ?? 'N/A' }} @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($ra)
            <div style="background:#f8f9ff; border:1px solid #e5e7eb; border-radius:10px; padding:16px;">
                <div style="font-size:12px; font-weight:600; color:#1e2a3a; margin-bottom:12px;">
                    <i class="fas fa-trophy me-2" style="color:#2e7df7;"></i>Bilan annuel
                </div>
                <div class="row g-3 text-center">
                    <div class="col-4">
                        <div style="font-size:10px; color:#6b7280;">Moy. annuelle</div>
                        @php $ma = $ra->moyenne_annuelle; @endphp
                        <div style="font-size:18px; font-weight:700; color:{{ $ma >= 10 ? '#27500a' : ($ma >= 6 ? '#e65100' : '#c62828') }};">
                            {{ $ma !== null ? number_format($ma, 2) : '—' }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:10px; color:#6b7280;">Crédits</div>
                        <div style="font-size:18px; font-weight:700; color:#1e2a3a;">
                            {{ $ra->credits_acquis }}/60
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:10px; color:#6b7280;">Rang</div>
                        <div style="font-size:18px; font-weight:700; color:#1e2a3a;">
                            {{ $ra->rang_annuel ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @php
                    $decisions = ['DIPLOME'=>'Diplômé(e)','REPRISE_SOUTENANCE'=>'Reprise Soutenance','REDOUBLE'=>'Redouble','EN_ATTENTE'=>'En attente'];
                    $mentions  = ['TRES_BIEN'=>'Très Bien','BIEN'=>'Bien','ASSEZ_BIEN'=>'Assez Bien','PASSABLE'=>'Passable','AUCUNE'=>'—'];
                @endphp
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <span class="{{ $ra->decision_jury == 'DIPLOME' ? 'badge-vert' : ($ra->decision_jury == 'REDOUBLE' ? 'badge-rouge' : 'badge-jaune') }}">
                        {{ $decisions[$ra->decision_jury] ?? '—' }}
                    </span>
                    @if($ra->mention && $ra->mention != 'AUCUNE')
                    <span class="badge-bleu">{{ $mentions[$ra->mention] ?? '—' }}</span>
                    @endif
                </div>
            </div>
            @else
            <div class="text-center" style="padding:32px; color:#9ca3af; font-size:13px;">
                <i class="fas fa-clock me-2"></i> Aucun résultat calculé pour cet étudiant.
            </div>
            @endif
        </div>

        {{-- Accès rapide --}}
        <div class="card-white mt-0">
            <div class="card-white-title">
                <i class="fas fa-bolt"></i> Accès rapide
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('notes.show', $etudiant->id) }}" class="btn-primary-inptic">
                    <i class="fas fa-pen"></i> Saisir les notes
                </a>
                <a href="{{ route('bulletins.s5', $etudiant->id) }}" target="_blank" class="btn-secondary-inptic">
                    <i class="fas fa-file-pdf"></i> Bulletin S5
                </a>
                <a href="{{ route('bulletins.s6', $etudiant->id) }}" target="_blank" class="btn-secondary-inptic">
                    <i class="fas fa-file-pdf"></i> Bulletin S6
                </a>
                <a href="{{ route('bulletins.annuel', $etudiant->id) }}" target="_blank" class="btn-secondary-inptic">
                    <i class="fas fa-file-pdf"></i> Bulletin Annuel
                </a>
            </div>
        </div>
    </div>
</div>

@endsection