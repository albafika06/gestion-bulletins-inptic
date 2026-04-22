@extends('layouts.app')
@section('title', 'Relevé — ' . $matiere->libelle)
@section('page_title', 'Relevé de Notes')
@section('page_sub', $matiere->libelle . ' · ' . $annee)

@push('styles')
<style>
    .moy-vert  { background:#eaf3de; color:#27500a; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }
    .moy-jaune { background:#faeeda; color:#633806; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }
    .moy-rouge { background:#fcebeb; color:#791f1f; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }
    .moy-null  { background:#f1efe8; color:#888780; padding:3px 10px; border-radius:20px; font-size:12px; display:inline-block; }

    @media print {
        .no-print { display:none !important; }
        .sidebar, .topbar, .main-footer { display:none !important; }
        .main-wrapper { margin-left:0 !important; }
        .page-content { padding:0 !important; }
        .print-header { display:block !important; }
    }
</style>
@endpush

@section('content')

@php
    // Calcul des statistiques à partir des notes envoyées par le controller
    $moyennes  = collect($notes)->map(fn($n) => $n['moyenne'])->filter(fn($m) => $m !== null);
    $nbTotal   = $moyennes->count();
    $nbValides = $moyennes->filter(fn($m) => $m >= 10)->count();
    $moyClasse = $nbTotal > 0 ? round($moyennes->avg(), 2) : null;
    $noteMax   = $nbTotal > 0 ? round($moyennes->max(), 2) : null;
    $noteMin   = $nbTotal > 0 ? round($moyennes->min(), 2) : null;

    // Utiliser StatistiqueMatiere si disponible, sinon calculer à la volée
    $moyClasseAff = $stat?->moyenne_classe ?? $moyClasse;
    $noteMaxAff   = $stat?->note_max       ?? $noteMax;
    $noteMinAff   = $stat?->note_min       ?? $noteMin;
@endphp

{{-- En-tête de page --}}
<div class="page-header d-flex justify-content-between align-items-center no-print">
    <div>
        <h1><i class="fas fa-list-alt me-2" style="color:#2e7df7;"></i>Relevé de Notes</h1>
        <p>
            <strong>{{ $matiere->libelle }}</strong> ·
            {{ $matiere->ue->code }} ·
            {{ $matiere->ue->semestre->libelle }} ·
            Coeff : {{ $matiere->coefficient }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">

        {{-- Bouton publier / badge publié --}}
        @if(!$relevePublie)
            <form method="POST" action="{{ route('enseignant.releve.publier', $matiere->id) }}">
                @csrf
                <button type="submit" class="btn-primary-inptic" style="background:#27500a;">
                    <i class="fas fa-bullhorn"></i> Publier aux étudiants
                </button>
            </form>
        @else
            <span class="badge-vert" style="padding:8px 14px; font-size:12px;">
                <i class="fas fa-check-circle me-1"></i> Publié
            </span>
        @endif

        <a href="{{ route('enseignant.releve.export', $matiere->id) }}"
           class="btn-primary-inptic" style="background:#1b5e20;">
            <i class="fas fa-file-excel"></i> Exporter Excel
        </a>
        <button onclick="window.print()" class="btn-secondary-inptic">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="{{ route('enseignant.saisir', $matiere->id) }}" class="btn-secondary-inptic">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

{{-- Statistiques --}}
<div class="row g-3 mb-4 no-print">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f1fb;">
                <i class="fas fa-users" style="color:#0c447c;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $etudiants->count() }}</div>
                <div class="stat-lbl">Étudiants</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf3de;">
                <i class="fas fa-chart-line" style="color:#27500a;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $moyClasseAff ?? '—' }}</div>
                <div class="stat-lbl">Moyenne classe</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf3de;">
                <i class="fas fa-arrow-up" style="color:#27500a;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $noteMaxAff ?? '—' }}</div>
                <div class="stat-lbl">Note la plus haute</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fcebeb;">
                <i class="fas fa-arrow-down" style="color:#791f1f;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $noteMinAff ?? '—' }}</div>
                <div class="stat-lbl">Note la plus basse</div>
            </div>
        </div>
    </div>
</div>

{{-- Tableau du relevé --}}
<div class="card-white">

    {{-- En-tête visible uniquement à l'impression --}}
    <div class="print-header" style="display:none; text-align:center; margin-bottom:16px;">
        <strong style="font-size:15px;">INPTIC — Relevé de Notes</strong><br>
        <span style="font-size:12px;">{{ $matiere->libelle }} · {{ $annee }}</span>
    </div>

    {{-- Résumé + barre de recherche --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
        <span style="font-size:13px; color:#6b7280;">
            <strong>{{ $etudiants->count() }}</strong> étudiant(s) ·
            <strong style="color:#27500a;">{{ $nbValides }}</strong> validé(s) ·
            <strong style="color:#791f1f;">{{ $nbTotal - $nbValides }}</strong> non validé(s)
        </span>
        <input type="text"
               id="searchInput"
               class="form-control-inptic no-print"
               placeholder="🔍 Rechercher un étudiant..."
               style="width:240px;">
    </div>

    {{-- Tableau --}}
    <div class="table-responsive">
        <table class="table table-inptic" id="releveTable">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:12%;">Matricule</th>
                    <th style="width:25%;">Nom et Prénom</th>
                    <th class="text-center" style="width:12%;">CC (40%)</th>
                    <th class="text-center" style="width:12%;">Examen (60%)</th>
                    <th class="text-center" style="width:12%;">Rattrapage</th>
                    <th class="text-center" style="width:12%;">Moyenne</th>
                    <th class="text-center" style="width:10%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($etudiants as $i => $etudiant)
                    @php
                        $n        = $notes[$etudiant->id] ?? [];
                        $moy      = $n['moyenne'] ?? null;
                        $moyClass = 'moy-null';
                        $statut   = '—';
                        if ($moy !== null) {
                            if ($moy >= 10)    { $moyClass = 'moy-vert';  $statut = '✅ Validé'; }
                            elseif ($moy >= 6) { $moyClass = 'moy-jaune'; $statut = '⚠️ Faible'; }
                            else               { $moyClass = 'moy-rouge'; $statut = '❌ Insuffisant'; }
                        }
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $etudiant->matricule }}</strong></td>
                        <td>{{ $etudiant->nom }} {{ $etudiant->prenom }}</td>
                        <td class="text-center">
                            {{ isset($n['cc']) && $n['cc'] !== null ? number_format($n['cc'], 2) : '—' }}
                        </td>
                        <td class="text-center">
                            {{ isset($n['examen']) && $n['examen'] !== null ? number_format($n['examen'], 2) : '—' }}
                        </td>
                        <td class="text-center">
                            @if(isset($n['rattrapage']) && $n['rattrapage'] !== null)
                                <span style="color:#633806; font-weight:500;">
                                    {{ number_format($n['rattrapage'], 2) }}
                                </span>
                            @else
                                <span style="color:#9ca3af;">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="{{ $moyClass }}">
                                {{ $moy !== null ? number_format($moy, 2) : '—' }}
                            </span>
                        </td>
                        <td class="text-center" style="font-size:12px;">
                            {{ $statut }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding:40px; color:#9ca3af;">
                            Aucun étudiant enregistré pour cette matière.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- Ligne statistiques footer --}}
            @if($nbTotal > 0)
                <tfoot>
                    <tr style="background:#f8f9ff; font-weight:600;">
                        <td colspan="3" style="padding:10px 14px; font-size:12px; color:#1e2a3a;">
                            <i class="fas fa-chart-bar me-1" style="color:#2e7df7;"></i>
                            Statistiques de la promotion
                        </td>
                        <td colspan="3" class="text-center" style="font-size:12px; color:#6b7280;">
                            {{ $nbValides }}/{{ $nbTotal }} validé(s)
                        </td>
                        <td class="text-center">
                            <span class="moy-vert">{{ $moyClasseAff }}</span>
                        </td>
                        <td class="text-center" style="font-size:11px; color:#6b7280;">
                            Max : {{ $noteMaxAff }}<br>
                            Min : {{ $noteMinAff }}
                        </td>
                    </tr>
                </tfoot>
            @endif

        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        var val = this.value.toLowerCase();
        document.querySelectorAll('#releveTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
        });
    });
</script>
@endpush