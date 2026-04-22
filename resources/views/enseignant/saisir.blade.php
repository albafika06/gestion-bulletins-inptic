@extends('layouts.app')
@section('title', 'Saisir notes — ' . $matiere->libelle)
@section('page_title', 'Saisie des Notes')
@section('page_sub', $matiere->libelle . ' · ' . $annee)

@section('head')
<style>
    .note-input {
        width: 75px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 5px 8px;
        text-align: center;
        font-size: 12px;
        color: #1e2a3a;
        background: #fff;
        outline: none;
        transition: border-color 0.15s;
    }
    .note-input:focus {
        border-color: #2e7df7;
        box-shadow: 0 0 0 2px rgba(46,125,247,0.1);
    }
    .note-input:disabled {
        background: #f8f9ff;
        cursor: not-allowed;
        color: #9ca3af;
    }
    .moy-badge { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }
    .moy-vert  { background:#eaf3de; color:#27500a; }
    .moy-jaune { background:#faeeda; color:#633806; }
    .moy-rouge { background:#fcebeb; color:#791f1f; }
    .moy-null  { background:#f1efe8; color:#888780; }
    .matiere-info { background:#f8f9ff; border:1px solid #e5e7eb; border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
    .info-item { display:flex; flex-direction:column; }
    .info-label { font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; }
    .info-value { font-size:14px; font-weight:600; color:#1e2a3a; }
</style>
@endsection

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-pen me-2" style="color:#2e7df7;"></i>Saisie des Notes</h1>
        <p>{{ $matiere->libelle }} · {{ $matiere->ue->code }} · {{ $annee }}</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-secondary-inptic">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

{{-- Infos de la matière --}}
<div class="matiere-info">
    <div class="info-item">
        <span class="info-label">Matière</span>
        <span class="info-value">{{ $matiere->libelle }}</span>
    </div>
    <div style="width:1px; height:36px; background:#e5e7eb;"></div>
    <div class="info-item">
        <span class="info-label">UE</span>
        <span class="info-value">{{ $matiere->ue->code }}</span>
    </div>
    <div style="width:1px; height:36px; background:#e5e7eb;"></div>
    <div class="info-item">
        <span class="info-label">Semestre</span>
        <span class="info-value">{{ $matiere->ue->semestre->libelle ?? '—' }}</span>
    </div>
    <div style="width:1px; height:36px; background:#e5e7eb;"></div>
    <div class="info-item">
        <span class="info-label">Coefficient</span>
        <span class="info-value">{{ $matiere->coefficient }}</span>
    </div>
    <div style="width:1px; height:36px; background:#e5e7eb;"></div>
    <div class="info-item">
        <span class="info-label">Crédits</span>
        <span class="info-value">{{ $matiere->credits }}</span>
    </div>
    <div style="width:1px; height:36px; background:#e5e7eb;"></div>
    <div class="info-item">
        <span class="info-label">Formule</span>
        <span class="info-value" style="font-size:12px;">CC × 40% + Examen × 60%</span>
    </div>
</div>

<form method="POST" action="{{ route('enseignant.enregistrer', $matiere->id) }}">
    @csrf

    <div class="card-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="card-white-title mb-0">
                <i class="fas fa-users"></i>
                {{ $etudiants->count() }} étudiant(s)
            </div>
            <input type="text" id="searchInput" class="form-control-inptic"
                   placeholder="🔍 Rechercher..." style="width:220px;">
        </div>

        <div class="table-responsive">
            <table class="table table-inptic" id="saisirTable">
                <thead>
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:12%;">Matricule</th>
                        <th style="width:25%;">Nom et Prénom</th>
                        <th class="text-center" style="width:14%;">
                            CC (40%)
                            <div style="font-size:9px; font-weight:400; color:#b0c4de;">0 — 20</div>
                        </th>
                        <th class="text-center" style="width:14%;">
                            Examen (60%)
                            <div style="font-size:9px; font-weight:400; color:#b0c4de;">0 — 20</div>
                        </th>
                        <th class="text-center" style="width:14%;">
                            Rattrapage
                            <div style="font-size:9px; font-weight:400; color:#b0c4de;">Remplace la moyenne</div>
                        </th>
                        <th class="text-center" style="width:16%;">Moyenne calculée</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($etudiants as $i => $etudiant)
                    @php
                        $n        = $notes[$etudiant->id] ?? [];
                        $moy      = $n['moyenne'] ?? null;
                        $moyClass = 'moy-null';
                        if ($moy !== null) {
                            if ($moy >= 10)    $moyClass = 'moy-vert';
                            elseif ($moy >= 6) $moyClass = 'moy-jaune';
                            else               $moyClass = 'moy-rouge';
                        }
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $etudiant->matricule }}</strong></td>
                        <td>{{ $etudiant->nom }} {{ $etudiant->prenom }}</td>

                        {{-- CC --}}
                        <td class="text-center">
                            <input type="number"
                                   name="notes[{{ $etudiant->id }}][cc]"
                                   class="note-input"
                                   value="{{ $n['cc'] !== null ? $n['cc'] : '' }}"
                                   min="0" max="20" step="0.25"
                                   placeholder="—">
                        </td>

                        {{-- Examen --}}
                        <td class="text-center">
                            <input type="number"
                                   name="notes[{{ $etudiant->id }}][examen]"
                                   class="note-input"
                                   value="{{ $n['examen'] !== null ? $n['examen'] : '' }}"
                                   min="0" max="20" step="0.25"
                                   placeholder="—">
                        </td>

                        {{-- Rattrapage --}}
                        <td class="text-center">
                            <input type="number"
                                   name="notes[{{ $etudiant->id }}][rattrapage]"
                                   class="note-input"
                                   value="{{ $n['rattrapage'] !== null ? $n['rattrapage'] : '' }}"
                                   min="0" max="20" step="0.25"
                                   placeholder="—"
                                   style="border-color:#fac775;">
                        </td>

                        {{-- Moyenne --}}
                        <td class="text-center">
                            <span class="moy-badge {{ $moyClass }}">
                                {{ $moy !== null ? number_format($moy, 2) : '—' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding:40px; color:#9ca3af;">
                            Aucun étudiant enregistré.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bouton enregistrer --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
             style="border-top:1px solid #e5e7eb;">
            <div style="font-size:12px; color:#6b7280;">
                <i class="fas fa-info-circle me-1" style="color:#2e7df7;"></i>
                Les moyennes sont recalculées automatiquement après enregistrement.
                Le rattrapage remplace intégralement la moyenne initiale.
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('enseignant.releve', $matiere->id) }}"
                   class="btn-secondary-inptic">
                    <i class="fas fa-list-alt"></i> Voir le relevé
                </a>
                <button type="submit" class="btn-primary-inptic">
                    <i class="fas fa-save"></i>
                    Enregistrer toutes les notes
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
    // Recherche dans le tableau
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var val = this.value.toLowerCase();
        document.querySelectorAll('#saisirTable tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
        });
    });

    // Aperçu de la moyenne en temps réel
    document.querySelectorAll('.note-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var row    = this.closest('tr');
            var inputs = row.querySelectorAll('.note-input');
            var cc     = parseFloat(inputs[0].value);
            var exam   = parseFloat(inputs[1].value);
            var ratt   = parseFloat(inputs[2].value);
            var moyBadge = row.querySelector('.moy-badge');

            var moy = null;

            if (!isNaN(ratt)) {
                moy = ratt;
            } else if (!isNaN(cc) && !isNaN(exam)) {
                moy = (cc * 0.40) + (exam * 0.60);
            } else if (!isNaN(cc)) {
                moy = cc;
            } else if (!isNaN(exam)) {
                moy = exam;
            }

            if (moy !== null) {
                moy = Math.round(moy * 100) / 100;
                moyBadge.textContent = moy.toFixed(2);
                moyBadge.className = 'moy-badge ';
                if (moy >= 10)      moyBadge.className += 'moy-vert';
                else if (moy >= 6)  moyBadge.className += 'moy-jaune';
                else                moyBadge.className += 'moy-rouge';
            } else {
                moyBadge.textContent = '—';
                moyBadge.className = 'moy-badge moy-null';
            }
        });
    });
</script>
@endsection