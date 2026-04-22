@extends('layouts.app')
@section('title', 'Notes de ' . $etudiant->nom)
@section('page_title', 'Notes — ' . $etudiant->nom . ' ' . $etudiant->prenom)
@section('page_sub', 'Matricule : ' . $etudiant->matricule . ' · Année : ' . $annee)

@section('head')
<style>
    .semestre-block { margin-bottom: 28px; }
    .semestre-title { font-size: 15px; font-weight: 600; color: #1e2a3a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
    .semestre-title i { color: #2e7df7; }
    .ue-block { margin-bottom: 20px; }
    .ue-header { background: #f8f9ff; border: 1px solid #e5e7eb; border-radius: 8px 8px 0 0; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .ue-title { font-size: 13px; font-weight: 600; color: #1e2a3a; display: flex; align-items: center; gap: 6px; }
    .ue-title i { color: #2e7df7; }
    .ue-stats { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .ue-credits { font-size: 11px; color: #6b7280; }
    .ue-moy { font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .ue-statut { font-size: 11px; font-weight: 500; display: flex; align-items: center; gap: 4px; }
    .notes-table { width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; overflow: hidden; }
    .notes-table th { background: #1e2a3a; color: #fff; font-size: 11px; font-weight: 500; padding: 9px 12px; text-align: center; border: none; }
    .notes-table th:first-child { text-align: left; }
    .notes-table td { font-size: 12px; padding: 9px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; text-align: center; background: #fff; }
    .notes-table td:first-child { text-align: left; font-weight: 500; color: #1e2a3a; }
    .notes-table tbody tr:hover td { background: #f8f9ff; }
    .note-wrap { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .note-input { width: 70px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 8px; text-align: center; font-size: 12px; color: #1e2a3a; background: #fff; outline: none; transition: border-color 0.15s; }
    .note-input:focus { border-color: #2e7df7; box-shadow: 0 0 0 2px rgba(46,125,247,0.1); }
    .note-input:disabled { background: #f8f9ff; cursor: not-allowed; }
    .btn-save { background: #2e7df7; color: #fff; border: none; border-radius: 6px; padding: 4px 8px; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s; }
    .btn-save:hover { background: #1a6de0; }
    .moy-badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .moy-vert  { background: #eaf3de; color: #27500a; }
    .moy-jaune { background: #faeeda; color: #633806; }
    .moy-rouge { background: #fcebeb; color: #791f1f; }
    .moy-null  { background: #f1efe8; color: #888780; }
    .ratt-badge { background: #faeeda; color: #633806; font-size: 10px; padding: 1px 6px; border-radius: 10px; margin-left: 4px; }
    .absence-cell { color: #6b7280; font-size: 12px; }
    .penalite { color: #e24b4a; font-size: 10px; display: block; }
</style>
@endsection

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-pen me-2" style="color:#2e7df7;"></i>{{ $etudiant->nom }} {{ $etudiant->prenom }}</h1>
        <p>Matricule : <strong>{{ $etudiant->matricule }}</strong> · Année : <strong>{{ $annee }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('notes.recalculer', $etudiant->id) }}">
            @csrf
            <button class="btn-secondary-inptic">
                <i class="fas fa-sync"></i> Recalculer
            </button>
        </form>
        <a href="{{ route('notes.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
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
                <i class="fas fa-layer-group"></i>
                {{ $ue->code }} — {{ $ue->libelle }}
            </div>
            <div class="ue-stats">
                @if($moyUE)
                    <span class="ue-credits">
                        Crédits : <strong>{{ $moyUE->credits_acquis }}/{{ $moyUE->credits_ue }}</strong>
                    </span>
                    <span class="moy-badge {{ $ueClass }}">
                        Moy UE : {{ number_format($moyUE->moyenne_ue, 2) }}/20
                    </span>
                    <span class="ue-statut">
                        @if($moyUE->statut == 'ACQUISE')
                            <i class="fas fa-check-circle" style="color:#27500a;"></i>
                            <span style="color:#27500a;">Acquise</span>
                        @elseif($moyUE->statut == 'COMPENSEE')
                            <i class="fas fa-bolt" style="color:#633806;"></i>
                            <span style="color:#633806;">Compensée</span>
                        @elseif($moyUE->statut == 'NON_ACQUISE')
                            <i class="fas fa-times-circle" style="color:#791f1f;"></i>
                            <span style="color:#791f1f;">Non acquise</span>
                        @else
                            <span style="color:#6b7280;">—</span>
                        @endif
                    </span>
                @endif
            </div>
        </div>

        <table class="notes-table">
            <thead>
                <tr>
                    <th style="width:28%; text-align:left;">Matière</th>
                    <th style="width:6%;">Coeff</th>
                    <th style="width:6%;">Crédits</th>
                    <th style="width:14%;">CC (40%)</th>
                    <th style="width:14%;">Examen (60%)</th>
                    <th style="width:14%;">Rattrapage</th>
                    <th style="width:8%;">Absence</th>
                    <th style="width:10%;">Moyenne</th>
                    <th style="width:10%;">Moy. Classe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ue->matieres as $matiere)
                @php
                    $canEdit  = $matieresAutorisees === null || in_array($matiere->id, $matieresAutorisees);
                    $mm       = $moyennes[$matiere->id] ?? null;
                    $statMat  = $stats[$matiere->id] ?? null;
                    $noteCC   = $evaluations[$matiere->id . '_CC'] ?? null;
                    $noteEx   = $evaluations[$matiere->id . '_EXAMEN'] ?? null;
                    $noteRat  = $evaluations[$matiere->id . '_RATTRAPAGE'] ?? null;

                    $moyClass = 'moy-null';
                    if ($mm && $mm->moyenne_finale !== null) {
                        if ($mm->moyenne_finale >= 10) $moyClass = 'moy-vert';
                        elseif ($mm->moyenne_finale >= 6) $moyClass = 'moy-jaune';
                        else $moyClass = 'moy-rouge';
                    }
                @endphp
                <tr>
                    <td>
                        {{ $matiere->libelle }}
                        @if($mm && $mm->rattrapage_utilise)
                            <span class="ratt-badge">Ratt.</span>
                        @endif
                    </td>
                    <td>{{ $matiere->coefficient }}</td>
                    <td>{{ $matiere->credits }}</td>

                    {{-- CC --}}
                    <td>
                        @if($canEdit)
                        <form method="POST" action="{{ route('notes.saisir') }}">
                            @csrf
                            <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                            <input type="hidden" name="matiere_id"  value="{{ $matiere->id }}">
                            <input type="hidden" name="type_eval"   value="CC">
                            <div class="note-wrap">
                                <input type="number" name="note" class="note-input"
                                       value="{{ $noteCC ? $noteCC->note : '' }}"
                                       min="0" max="20" step="0.25" placeholder="—">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-check" style="font-size:10px;"></i>
                                </button>
                            </div>
                        </form>
                        @else
                            {{ $noteCC ? number_format($noteCC->note, 2) : '—' }}
                        @endif
                    </td>

                    {{-- EXAMEN --}}
                    <td>
                        @if($canEdit)
                        <form method="POST" action="{{ route('notes.saisir') }}">
                            @csrf
                            <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                            <input type="hidden" name="matiere_id"  value="{{ $matiere->id }}">
                            <input type="hidden" name="type_eval"   value="EXAMEN">
                            <div class="note-wrap">
                                <input type="number" name="note" class="note-input"
                                       value="{{ $noteEx ? $noteEx->note : '' }}"
                                       min="0" max="20" step="0.25" placeholder="—">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-check" style="font-size:10px;"></i>
                                </button>
                            </div>
                        </form>
                        @else
                            {{ $noteEx ? number_format($noteEx->note, 2) : '—' }}
                        @endif
                    </td>

                    {{-- RATTRAPAGE --}}
                    <td>
                        @if($canEdit)
                        <form method="POST" action="{{ route('notes.saisir') }}">
                            @csrf
                            <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                            <input type="hidden" name="matiere_id"  value="{{ $matiere->id }}">
                            <input type="hidden" name="type_eval"   value="RATTRAPAGE">
                            <div class="note-wrap">
                                <input type="number" name="note" class="note-input"
                                       value="{{ $noteRat ? $noteRat->note : '' }}"
                                       min="0" max="20" step="0.25" placeholder="—">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-check" style="font-size:10px;"></i>
                                </button>
                            </div>
                        </form>
                        @else
                            {{ $noteRat ? number_format($noteRat->note, 2) : '—' }}
                        @endif
                    </td>

                    {{-- ABSENCE --}}
                    <td class="absence-cell">
                        {{ $mm ? $mm->heures_absence : '0' }}h
                        @if($mm && $mm->penalite_absence > 0)
                            <span class="penalite">-{{ $mm->penalite_absence }} pt</span>
                        @endif
                    </td>

                    {{-- MOYENNE --}}
                    <td>
                        @if($mm && $mm->moyenne_finale !== null)
                            <span class="moy-badge {{ $moyClass }}">
                                {{ number_format($mm->moyenne_finale, 2) }}
                            </span>
                        @else
                            <span class="moy-badge moy-null">—</span>
                        @endif
                    </td>

                    {{-- MOY CLASSE --}}
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