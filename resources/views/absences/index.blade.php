@extends('layouts.app')
@section('title', 'Absences')
@section('page_title', 'Gestion des Absences')
@section('page_sub', 'Saisie et gestion des absences · Année ' . $annee)

@section('head')
<style>
    .stats-abs { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
    .modal-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; }
    .modal-box { background:#fff; border-radius:14px; padding:28px; width:420px; border:1px solid #e5e7eb; }
    .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .modal-title { font-size:14px; font-weight:600; color:#1e2a3a; display:flex; align-items:center; gap:8px; }
    .modal-close { background:none; border:none; color:#6b7280; cursor:pointer; font-size:20px; line-height:1; }
    .modal-info { background:#f8f9ff; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:11px; color:#6b7280; margin-bottom:14px; }
    .modal-info strong { color:#1e2a3a; }
    .alert-impact { border-radius:8px; padding:10px 13px; font-size:11px; margin-bottom:14px; }
</style>
@endsection

@section('content')

<div class="page-header">
    <h1><i class="fas fa-calendar-times me-2" style="color:#2e7df7;"></i>Gestion des Absences</h1>
    <p>Année universitaire : <strong>{{ $annee }}</strong></p>
</div>

{{-- Stats --}}
<div class="stats-abs">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fcebeb;">
            <i class="fas fa-calendar-times" style="color:#791f1f;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total absences</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fcebeb;">
            <i class="fas fa-exclamation-circle" style="color:#791f1f;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['non_justifiees'] }}</div>
            <div class="stat-lbl">Non justifiées</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eaf3de;">
            <i class="fas fa-check-circle" style="color:#27500a;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['justifiees'] }}</div>
            <div class="stat-lbl">Justifiées</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#faeeda;">
            <i class="fas fa-minus-circle" style="color:#633806;"></i>
        </div>
        <div>
            <div class="stat-num">-{{ number_format($stats['total_penalites'], 2) }}</div>
            <div class="stat-lbl">Total pénalités (pt)</div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Formulaire --}}
    <div class="col-md-5">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-plus-circle"></i> Saisir une absence
            </div>

            <form method="POST" action="{{ route('absences.saisir') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label-inptic">Étudiant <span style="color:#e24b4a;">*</span></label>
                    <select name="etudiant_id" class="form-control-inptic" required>
                        <option value="">-- Sélectionner un étudiant --</option>
                        @foreach($etudiants as $etudiant)
                        <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                            {{ $etudiant->nom }} {{ $etudiant->prenom }} ({{ $etudiant->matricule }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-inptic">Semestre <span style="color:#e24b4a;">*</span></label>
                    <select id="semestreSelect" class="form-control-inptic">
                        <option value="">-- Sélectionner --</option>
                        <option value="S5">Semestre 5</option>
                        <option value="S6">Semestre 6</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-inptic">Matière <span style="color:#e24b4a;">*</span></label>
                    <select name="matiere_id" id="matiereSelect" class="form-control-inptic" required>
                        <option value="">-- Sélectionner d'abord un semestre --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-inptic">Heures d'absence <span style="color:#e24b4a;">*</span></label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="number" name="heures" class="form-control-inptic"
                               min="0.5" step="0.5" placeholder="Ex: 2"
                               value="{{ old('heures') }}" required>
                        <span style="font-size:13px; color:#6b7280; white-space:nowrap;">heure(s)</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input type="checkbox" name="justifie" value="1"
                               style="width:16px; height:16px; accent-color:#2e7df7;"
                               {{ old('justifie') ? 'checked' : '' }}>
                        <span style="font-size:13px; color:#374151;">
                            Absence justifiée
                            <em style="color:#6b7280; font-size:11px;">(pas de pénalité)</em>
                        </span>
                    </label>
                </div>
                <div style="background:#e6f1fb; border:1px solid #b5d4f4; border-radius:8px; padding:10px 14px; font-size:11px; color:#0c447c; margin-bottom:14px;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Règle :</strong> Non justifiée → -0.01 pt/h · Justifiée → 0 pénalité
                </div>
                <button type="submit" class="btn-primary-inptic w-100" style="justify-content:center;">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="col-md-7">
        <div class="card-white">
            <div class="card-white-title">
                <i class="fas fa-list"></i> Absences enregistrées
            </div>

            {{-- Filtres --}}
            <form method="GET" action="{{ route('absences.index') }}"
                  class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control-inptic" placeholder="🔍 Rechercher..."
                       style="width:180px;">
                <select name="statut" class="form-control-inptic" style="width:140px;">
                    <option value="">Tous statuts</option>
                    <option value="justifie"     {{ request('statut') == 'justifie'     ? 'selected' : '' }}>Justifiée</option>
                    <option value="non_justifie" {{ request('statut') == 'non_justifie' ? 'selected' : '' }}>Non justifiée</option>
                </select>
                <select name="semestre" class="form-control-inptic" style="width:130px;">
                    <option value="">Tous sem.</option>
                    <option value="S5" {{ request('semestre') == 'S5' ? 'selected' : '' }}>Semestre 5</option>
                    <option value="S6" {{ request('semestre') == 'S6' ? 'selected' : '' }}>Semestre 6</option>
                </select>
                <button type="submit" class="btn-primary-inptic" style="padding:7px 12px; font-size:12px;">
                    <i class="fas fa-filter"></i>
                </button>
                <a href="{{ route('absences.index') }}" class="btn-secondary-inptic" style="padding:7px 12px; font-size:12px;">
                    <i class="fas fa-times"></i>
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-inptic">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Matière</th>
                            <th class="text-center">Sem.</th>
                            <th class="text-center">Heures</th>
                            <th class="text-center">Pénalité</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absence)
                        <tr>
                            <td>
                                <strong>{{ $absence->etudiant->nom }}</strong>
                                {{ $absence->etudiant->prenom }}<br>
                                <span style="font-size:10px; color:#9ca3af;">{{ $absence->etudiant->matricule }}</span>
                            </td>
                            <td style="font-size:12px;">{{ $absence->matiere->libelle }}</td>
                            <td class="text-center">
                                <span class="badge-bleu" style="font-size:10px;">
                                    {{ $absence->matiere->ue->semestre->code ?? '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <strong style="color:{{ $absence->justifie ? '#27500a' : '#c62828' }};">
                                    {{ $absence->heures }}h
                                </strong>
                            </td>
                            <td class="text-center">
                                @if($absence->justifie)
                                    <span style="color:#27500a; font-weight:600; font-size:11px;">0 pt</span>
                                @else
                                    <span style="color:#c62828; font-weight:600; font-size:11px;">
                                        -{{ number_format($absence->heures * 0.01, 2) }} pt
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($absence->justifie)
                                    <span class="badge-vert">Justifiée</span>
                                @else
                                    <span class="badge-rouge">Non justifiée</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Modifier --}}
                                    <button class="tb-btn" title="Modifier"
                                            onclick="document.getElementById('modal-edit-{{ $absence->id }}').style.display='flex'">
                                        <i class="fas fa-edit" style="font-size:11px;"></i>
                                    </button>

                                    {{-- Justifier --}}
                                    @if(!$absence->justifie)
                                    <button class="tb-btn"
                                            style="background:#eaf3de; border-color:#c0dd97; color:#27500a;"
                                            title="Justifier"
                                            onclick="document.getElementById('modal-just-{{ $absence->id }}').style.display='flex'">
                                        <i class="fas fa-check" style="font-size:11px;"></i>
                                    </button>
                                    @endif

                                    {{-- Supprimer --}}
                                    <button class="tb-btn"
                                            style="background:#fcebeb; border-color:#f7c1c1; color:#791f1f;"
                                            title="Supprimer"
                                            onclick="document.getElementById('modal-del-{{ $absence->id }}').style.display='flex'">
                                        <i class="fas fa-trash" style="font-size:11px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding:40px; color:#9ca3af;">
                                <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                Aucune absence enregistrée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
@foreach($absences as $absence)

{{-- Modal Modifier --}}
<div id="modal-edit-{{ $absence->id }}" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-edit" style="color:#2e7df7;"></i>
                Modifier l'absence
            </div>
            <button class="modal-close" onclick="document.getElementById('modal-edit-{{ $absence->id }}').style.display='none'">&times;</button>
        </div>
        <div class="modal-info">
            <strong>{{ $absence->etudiant->nom }} {{ $absence->etudiant->prenom }}</strong>
            · {{ $absence->matiere->libelle }}
        </div>
        <form method="POST" action="{{ route('absences.modifier', $absence->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label-inptic">Heures d'absence</label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <input type="number" name="heures" class="form-control-inptic"
                           value="{{ $absence->heures }}" min="0.5" step="0.5" required>
                    <span style="font-size:13px; color:#6b7280;">heure(s)</span>
                </div>
            </div>
            <div class="mb-3">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="justifie" value="1"
                           style="width:16px; height:16px; accent-color:#2e7df7;"
                           {{ $absence->justifie ? 'checked' : '' }}>
                    <span style="font-size:13px; color:#374151;">
                        Absence justifiée
                        <em style="color:#6b7280; font-size:11px;">(pas de pénalité)</em>
                    </span>
                </label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn-primary-inptic" style="flex:1; justify-content:center;">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <button type="button" class="btn-secondary-inptic"
                        onclick="document.getElementById('modal-edit-{{ $absence->id }}').style.display='none'">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Justifier --}}
@if(!$absence->justifie)
<div id="modal-just-{{ $absence->id }}" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-check-circle" style="color:#27500a;"></i>
                Justifier l'absence
            </div>
            <button class="modal-close" onclick="document.getElementById('modal-just-{{ $absence->id }}').style.display='none'">&times;</button>
        </div>
        <div class="modal-info">
            <strong>{{ $absence->etudiant->nom }} {{ $absence->etudiant->prenom }}</strong>
            · {{ $absence->matiere->libelle }} · {{ $absence->heures }}h
        </div>
        <div class="alert-impact" style="background:#eaf3de; border:1px solid #c0dd97; color:#27500a;">
            <i class="fas fa-info-circle me-2"></i>
            En justifiant cette absence, la pénalité de
            <strong>-{{ number_format($absence->heures * 0.01, 2) }} pt</strong>
            sera supprimée et la moyenne sera recalculée automatiquement.
        </div>
        <form method="POST" action="{{ route('absences.justifier', $absence->id) }}">
            @csrf
            <div class="d-flex gap-2">
                <button type="submit" class="btn-primary-inptic"
                        style="flex:1; justify-content:center; background:#27500a;">
                    <i class="fas fa-check"></i> Confirmer la justification
                </button>
                <button type="button" class="btn-secondary-inptic"
                        onclick="document.getElementById('modal-just-{{ $absence->id }}').style.display='none'">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Modal Supprimer --}}
<div id="modal-del-{{ $absence->id }}" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-trash" style="color:#791f1f;"></i>
                Supprimer l'absence
            </div>
            <button class="modal-close" onclick="document.getElementById('modal-del-{{ $absence->id }}').style.display='none'">&times;</button>
        </div>
        <div class="modal-info">
            <strong>{{ $absence->etudiant->nom }} {{ $absence->etudiant->prenom }}</strong>
            · {{ $absence->matiere->libelle }} · {{ $absence->heures }}h
        </div>
        <div class="alert-impact" style="background:#fcebeb; border:1px solid #f7c1c1; color:#791f1f;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            La suppression recalculera automatiquement la moyenne de l'étudiant.
            <strong>Cette action est irréversible.</strong>
        </div>
        <form method="POST" action="{{ route('absences.destroy', $absence->id) }}">
            @csrf @method('DELETE')
            <div class="d-flex gap-2">
                <button type="submit" class="btn-primary-inptic"
                        style="flex:1; justify-content:center; background:#c62828;">
                    <i class="fas fa-trash"></i> Supprimer définitivement
                </button>
                <button type="button" class="btn-secondary-inptic"
                        onclick="document.getElementById('modal-del-{{ $absence->id }}').style.display='none'">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

@endforeach

@endsection

@section('scripts')
<script>
    var matieresBySemestre = {
        'S5': {!! json_encode(
            \App\Models\Matiere::whereHas('ue', fn($q) =>
                $q->whereHas('semestre', fn($q2) => $q2->where('code', 'S5'))
            )->where('actif', 1)->orderBy('ordre')->get(['id','libelle'])
        ) !!},
        'S6': {!! json_encode(
            \App\Models\Matiere::whereHas('ue', fn($q) =>
                $q->whereHas('semestre', fn($q2) => $q2->where('code', 'S6'))
            )->where('actif', 1)->orderBy('ordre')->get(['id','libelle'])
        ) !!}
    };

    document.getElementById('semestreSelect').addEventListener('change', function() {
        var sem    = this.value;
        var select = document.getElementById('matiereSelect');
        select.innerHTML = '<option value="">-- Sélectionner une matière --</option>';
        if (sem && matieresBySemestre[sem]) {
            matieresBySemestre[sem].forEach(function(m) {
                select.innerHTML += '<option value="' + m.id + '">' + m.libelle + '</option>';
            });
        }
    });
</script>
@endsection