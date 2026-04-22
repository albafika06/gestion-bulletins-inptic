@extends('layouts.app')
@section('title', 'Mes Notes')
@section('page_title', 'Mes Notes')
@section('page_sub', 'Consultation de vos résultats · ' . $annee)

@section('head')
<style>
    .semestre-block { margin-bottom: 28px; }
    .semestre-title { font-size: 15px; font-weight: 600; color: #1e2a3a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
    .semestre-title i { color: #2e7df7; }
    .ue-block { margin-bottom: 20px; }
    .ue-header { background: #f8f9ff; border: 1px solid #e5e7eb; border-radius: 8px 8px 0 0; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .ue-title { font-size: 13px; font-weight: 600; color: #1e2a3a; display: flex; align-items: center; gap: 6px; }
    .notes-table { width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; border-top: none; }
    .notes-table th { background: #1e2a3a; color: #fff; font-size: 11px; font-weight: 500; padding: 9px 12px; text-align: center; }
    .notes-table th:first-child { text-align: left; }
    .notes-table td { font-size: 12px; padding: 9px 12px; border-bottom: 1px solid #f0f0f0; text-align: center; background: #fff; }
    .notes-table td:first-child { text-align: left; font-weight: 500; color: #1e2a3a; }
    .moy-badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .moy-vert  { background: #eaf3de; color: #27500a; }
    .moy-jaune { background: #faeeda; color: #633806; }
    .moy-rouge { background: #fcebeb; color: #791f1f; }
    .moy-null  { background: #f1efe8; color: #888780; }
    .lecture-seule-badge { background: #e6f1fb; color: #0c447c; border: 1px solid #b5d4f4; border-radius: 8px; padding: 6px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; }
</style>
@endsection

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-book-open me-2" style="color:#2e7df7;"></i>Mes Notes</h1>
        <p>{{ $etudiant->nom }} {{ $etudiant->prenom }} · <strong>{{ $etudiant->matricule }}</strong></p>
    </div>
    <span class="lecture-seule-badge">
        <i class="fas fa-eye"></i> Consultation uniquement
    </span>
</div>

@foreach($semestres as $semestre)
<div class="semestre-block">
    <div class="semestre-title">
        <i class="fas fa-book-open"></i>
        {{ $semestre->libelle }}
    </div>

    @foreach($semestre->unitesEnseignement as $ue)
    @php
        $moyUE = \App\Models\MoyenneUE::where('etudiant_id', $etudiant->id)
                    ->where('ue_id', $ue->id)
                    ->where('annee_univ', $annee)
                    ->first();
        $ueClass = 'moy-null';
        if ($moyUE && $moyUE->moyenne_ue !== null) {
            if ($moyUE->moyenne_ue >= 10) $ueClass = 'moy-vert';
            elseif ($moyUE->moyenne_ue >= 6) $ueClass = 'moy-jaune';
            else $ueClass = 'moy-rouge';
        }
    @endphp
    <div class="ue-block">
        <div class="ue-header">
            <div class="ue-title">
                <i class="fas fa-layer-group" style="color:#2e7df7;"></i>
                {{ $ue->code }} — {{ $ue->libelle }}
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($moyUE)
                    <span style="font-size:11px; color:#6b7280;">
                        Crédits : <strong>{{ $moyUE->credits_acquis }}/{{ $moyUE->credits_ue }}</strong>
                    </span>
                    <span class="moy-badge {{ $ueClass }}">
                        Moy UE : {{ number_format($moyUE->moyenne_ue, 2) }}/20
                    </span>
                    @if($moyUE->statut == 'ACQUISE')
                        <span style="color:#27500a; font-size:11px; font-weight:500;">
                            <i class="fas fa-check-circle"></i> Acquise
                        </span>
                    @elseif($moyUE->statut == 'COMPENSEE')
                        <span style="color:#633806; font-size:11px; font-weight:500;">
                            <i class="fas fa-bolt"></i> Compensée
                        </span>
                    @elseif($moyUE->statut == 'NON_ACQUISE')
                        <span style="color:#791f1f; font-size:11px; font-weight:500;">
                            <i class="fas fa-times-circle"></i> Non acquise
                        </span>
                    @endif
                @endif
            </div>
        </div>

        <table class="notes-table">
            <thead>
                <tr>
                    <th style="width:35%; text-align:left;">Matière</th>
                    <th>Coeff</th>
                    <th>Crédits</th>
                    <th>CC</th>
                    <th>Examen</th>
                    <th>Rattrapage</th>
                    <th>Moyenne</th>
                    <th>Moy. Classe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ue->matieres as $matiere)
                @php
                    $mm      = $moyennes[$matiere->id] ?? null;
                    $statMat = $stats[$matiere->id] ?? null;
                    $noteCC  = $evaluations[$matiere->id . '_CC'] ?? null;
                    $noteEx  = $evaluations[$matiere->id . '_EXAMEN'] ?? null;
                    $noteRat = $evaluations[$matiere->id . '_RATTRAPAGE'] ?? null;
                    $moyClass = 'moy-null';
                    if ($mm && $mm->moyenne_finale !== null) {
                        if ($mm->moyenne_finale >= 10) $moyClass = 'moy-vert';
                        elseif ($mm->moyenne_finale >= 6) $moyClass = 'moy-jaune';
                        else $moyClass = 'moy-rouge';
                    }
                @endphp
                <tr>
                    <td>{{ $matiere->libelle }}</td>
                    <td>{{ $matiere->coefficient }}</td>
                    <td>{{ $matiere->credits }}</td>
                    <td>{{ $noteCC ? number_format($noteCC->note, 2) : '—' }}</td>
                    <td>{{ $noteEx ? number_format($noteEx->note, 2) : '—' }}</td>
                    <td>{{ $noteRat ? number_format($noteRat->note, 2) : '—' }}</td>
                    <td>
                        @if($mm && $mm->moyenne_finale !== null)
                            <span class="moy-badge {{ $moyClass }}">
                                {{ number_format($mm->moyenne_finale, 2) }}
                            </span>
                        @else
                            <span class="moy-badge moy-null">—</span>
                        @endif
                    </td>
                    <td style="color:#9ca3af; font-size:11px;">
                        {{ $statMat ? number_format($statMat->moyenne_classe, 2) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>
@endforeach

@endsection