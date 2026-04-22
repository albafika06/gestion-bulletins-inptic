@extends('layouts.app')
@section('title', 'Relevé — ' . $matiere->libelle)
@section('page_title', 'Relevé de Notes')
@section('page_sub', $matiere->libelle . ' · ' . $annee)

@push('styles')
<style>
    .note-bloc {
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:10px;
        padding:20px 24px;
        text-align:center;
    }
    .note-label { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; }
    .note-value { font-size:28px; font-weight:700; color:#1e2a3a; line-height:1; }
    .note-sub   { font-size:11px; color:#9ca3af; margin-top:4px; }
    .moy-vert   { background:#eaf3de; color:#27500a; padding:4px 14px; border-radius:20px; font-size:14px; font-weight:600; display:inline-block; }
    .moy-jaune  { background:#faeeda; color:#633806; padding:4px 14px; border-radius:20px; font-size:14px; font-weight:600; display:inline-block; }
    .moy-rouge  { background:#fcebeb; color:#791f1f; padding:4px 14px; border-radius:20px; font-size:14px; font-weight:600; display:inline-block; }
    .moy-null   { background:#f1efe8; color:#888780; padding:4px 14px; border-radius:20px; font-size:14px; display:inline-block; }
</style>
@endpush

@section('content')

@php
    $moy      = $notes['moyenne'] ?? null;
    $moyClass = 'moy-null';
    $statut   = 'Non renseigné';
    if ($moy !== null) {
        if ($moy >= 10)    { $moyClass = 'moy-vert';  $statut = '✅ Validé'; }
        elseif ($moy >= 6) { $moyClass = 'moy-jaune'; $statut = '⚠️ Insuffisant'; }
        else               { $moyClass = 'moy-rouge'; $statut = '❌ Non validé'; }
    }
@endphp

{{-- En-tête --}}
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-list-alt me-2" style="color:#2e7df7;"></i>Relevé de Notes</h1>
        <p>
            <strong>{{ $matiere->libelle }}</strong> ·
            {{ $matiere->ue->code ?? '—' }} ·
            {{ $matiere->ue->semestre->libelle ?? '—' }} ·
            Coeff : {{ $matiere->coefficient }}
        </p>
    </div>
    <a href="{{ route('etudiant.bulletins') }}" class="btn-secondary-inptic">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

{{-- Identité étudiant --}}
<div style="background:#f8f9ff; border:1px solid #e5e7eb; border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
    <div style="width:44px; height:44px; border-radius:50%; background:#2e7df7; display:flex; align-items:center; justify-content:center; color:#fff; font-size:16px; font-weight:700; flex-shrink:0;">
        {{ strtoupper(substr($etudiant->nom, 0, 1)) }}
    </div>
    <div>
        <div style="font-size:14px; font-weight:600; color:#1e2a3a;">
            {{ $etudiant->nom }} {{ $etudiant->prenom }}
        </div>
        <div style="font-size:12px; color:#6b7280;">
            Matricule : <strong>{{ $etudiant->matricule }}</strong> · {{ $annee }}
        </div>
    </div>
    <div style="margin-left:auto;">
        <span class="{{ $moyClass }}" style="font-size:13px;">
            {{ $statut }}
        </span>
    </div>
</div>

{{-- Notes détaillées --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="note-bloc">
            <div class="note-label">Contrôle Continu (40%)</div>
            <div class="note-value" style="color:#0c447c;">
                {{ $notes['cc'] !== null ? number_format($notes['cc'], 2) : '—' }}
            </div>
            <div class="note-sub">/ 20 points</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="note-bloc">
            <div class="note-label">Examen (60%)</div>
            <div class="note-value" style="color:#1b5e20;">
                {{ $notes['examen'] !== null ? number_format($notes['examen'], 2) : '—' }}
            </div>
            <div class="note-sub">/ 20 points</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="note-bloc">
            <div class="note-label">Rattrapage</div>
            <div class="note-value" style="color:#633806;">
                {{ $notes['rattrapage'] !== null ? number_format($notes['rattrapage'], 2) : '—' }}
            </div>
            <div class="note-sub">
                {{ $notes['rattrapage'] !== null ? 'Remplace la moyenne' : 'Non passé' }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="note-bloc" style="border-color:#2e7df7; border-width:2px;">
            <div class="note-label">Moyenne finale</div>
            <div style="margin-top:4px;">
                <span class="{{ $moyClass }}" style="font-size:22px; padding:6px 16px;">
                    {{ $moy !== null ? number_format($moy, 2) : '—' }}
                </span>
            </div>
            <div class="note-sub" style="margin-top:6px;">/ 20 points</div>
        </div>
    </div>
</div>

{{-- Position dans la classe --}}
<div class="row g-3 mb-4">
    @if($rang !== null && $nbEtudiants > 0)
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#faeeda;">
                <i class="fas fa-trophy" style="color:#633806;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $rang }}<sup style="font-size:14px;">e</sup></div>
                <div class="stat-lbl">Position dans la classe</div>
            </div>
        </div>
    </div>
    @endif

    @if($stat)
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf3de;">
                <i class="fas fa-chart-bar" style="color:#27500a;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $stat->moyenne_classe ?? '—' }}</div>
                <div class="stat-lbl">Moyenne de la classe</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f1fb;">
                <i class="fas fa-users" style="color:#0c447c;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $nbEtudiants }}</div>
                <div class="stat-lbl">Étudiants dans la promotion</div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Détail du calcul --}}
<div class="card-white">
    <div class="card-white-title">
        <i class="fas fa-info-circle"></i> Détail du calcul
    </div>
    <div style="font-size:13px; color:#374151; line-height:2;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px; flex-wrap:wrap;">
            <span style="background:#f4f5f7; border:1px solid #e5e7eb; padding:3px 10px; border-radius:6px; font-size:12px;">
                Formule : CC × 40% + Examen × 60%
            </span>
            @if($notes['rattrapage'] !== null)
            <span style="background:#faeeda; border:1px solid #f5c87a; padding:3px 10px; border-radius:6px; font-size:12px; color:#633806;">
                ⚠️ Rattrapage utilisé — remplace la moyenne initiale
            </span>
            @endif
        </div>

        @if($notes['cc'] !== null && $notes['examen'] !== null && $notes['rattrapage'] === null)
        @php $moyCalc = ($notes['cc'] * 0.40) + ($notes['examen'] * 0.60); @endphp
        <div style="font-size:12px; color:#6b7280; background:#f8f9ff; border-radius:8px; padding:10px 14px; margin-top:8px;">
            Calcul : ({{ number_format($notes['cc'], 2) }} × 0,40) + ({{ number_format($notes['examen'], 2) }} × 0,60)
            = <strong>{{ number_format($moyCalc, 2) }}/20</strong>
        </div>
        @endif

        @if($stat)
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:14px;">
            <div style="background:#f8f9ff; border-radius:8px; padding:10px 14px; text-align:center;">
                <div style="font-size:10px; color:#6b7280; text-transform:uppercase; margin-bottom:4px;">Note max</div>
                <div style="font-size:16px; font-weight:600; color:#27500a;">{{ $stat->note_max ?? '—' }}</div>
            </div>
            <div style="background:#f8f9ff; border-radius:8px; padding:10px 14px; text-align:center;">
                <div style="font-size:10px; color:#6b7280; text-transform:uppercase; margin-bottom:4px;">Note min</div>
                <div style="font-size:16px; font-weight:600; color:#791f1f;">{{ $stat->note_min ?? '—' }}</div>
            </div>
            <div style="background:#f8f9ff; border-radius:8px; padding:10px 14px; text-align:center;">
                <div style="font-size:10px; color:#6b7280; text-transform:uppercase; margin-bottom:4px;">Taux de réussite</div>
                @php
                    $taux = ($stat->nb_total ?? 0) > 0
                        ? round(($stat->nb_valides / $stat->nb_total) * 100)
                        : 0;
                @endphp
                <div style="font-size:16px; font-weight:600; color:#0c447c;">{{ $taux }}%</div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection