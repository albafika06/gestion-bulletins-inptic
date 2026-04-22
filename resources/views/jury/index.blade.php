@extends('layouts.app')
@section('title', 'Jury')
@section('page_title', 'Décisions du Jury')
@section('page_sub', 'Année universitaire ' . $annee)

@section('head')
<style>
    .decision-diplome { background:#eaf3de; color:#27500a; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
    .decision-reprise { background:#faeeda; color:#633806; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
    .decision-redouble { background:#fcebeb; color:#791f1f; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
    .decision-attente { background:#f1efe8; color:#444441; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
    .mention-badge { background:#e6f1fb; color:#0c447c; padding:2px 8px; border-radius:10px; font-size:11px; }
</style>
@endsection

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-gavel me-2" style="color:#2e7df7;"></i>Décisions du Jury</h1>
        <p>Année universitaire : <strong>{{ $annee }}</strong></p>
    </div>
    <form method="POST" action="{{ route('jury.valider') }}">
        @csrf
        <button type="submit" class="btn-primary-inptic"
                onclick="return confirm('Recalculer toutes les moyennes pour toute la promotion ?')">
            <i class="fas fa-sync"></i> Recalcul complet promotion
        </button>
    </form>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f1fb;">
                <i class="fas fa-users" style="color:#0c447c;"></i>
            </div>
            <div><div class="stat-num">{{ $stats['total'] }}</div><div class="stat-lbl">Total étudiants</div></div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf3de;">
                <i class="fas fa-graduation-cap" style="color:#27500a;"></i>
            </div>
            <div><div class="stat-num">{{ $stats['diplomes'] }}</div><div class="stat-lbl">Diplômés</div></div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon" style="background:#faeeda;">
                <i class="fas fa-redo" style="color:#633806;"></i>
            </div>
            <div><div class="stat-num">{{ $stats['reprise_soutenance'] }}</div><div class="stat-lbl">Reprise soutenance</div></div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fcebeb;">
                <i class="fas fa-times-circle" style="color:#791f1f;"></i>
            </div>
            <div><div class="stat-num">{{ $stats['redoublants'] }}</div><div class="stat-lbl">Redoublants</div></div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f1efe8;">
                <i class="fas fa-clock" style="color:#444441;"></i>
            </div>
            <div><div class="stat-num">{{ $stats['en_attente'] }}</div><div class="stat-lbl">En attente</div></div>
        </div>
    </div>
</div>

<!-- Tableau -->
<div class="card-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span style="font-size:13px; color:#6b7280;"><strong>{{ $etudiants->count() }}</strong> étudiant(s)</span>
        <input type="text" id="searchInput" class="form-control-inptic"
               placeholder="🔍 Rechercher..." style="width:260px;">
    </div>
    <div class="table-responsive">
        <table class="table table-inptic" id="juryTable">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Matricule</th>
                    <th>Nom et Prénom</th>
                    <th class="text-center">Moy. S5</th>
                    <th class="text-center">Moy. S6</th>
                    <th class="text-center">Moy. Annuelle</th>
                    <th class="text-center">Crédits</th>
                    <th class="text-center">Mention</th>
                    <th class="text-center">Décision</th>
                    <th class="text-center">Bulletin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($etudiants as $etudiant)
                @php
                    $ra = $etudiant->resultatAnnuel;
                    $mentions = ['TRES_BIEN'=>'Très Bien','BIEN'=>'Bien','ASSEZ_BIEN'=>'Assez Bien','PASSABLE'=>'Passable','AUCUNE'=>'—'];
                @endphp
                <tr>
                    <td>{{ $ra?->rang_annuel ?? '—' }}</td>
                    <td><strong>{{ $etudiant->matricule }}</strong></td>
                    <td>{{ $etudiant->nom }} {{ $etudiant->prenom }}</td>
                    <td class="text-center">
                        @if($ra?->moyenne_s5 !== null)
                            @php $c = $ra->moyenne_s5 >= 10 ? '#27500a' : ($ra->moyenne_s5 >= 6 ? '#633806' : '#791f1f'); @endphp
                            <strong style="color:{{ $c }};">{{ number_format($ra->moyenne_s5, 2) }}</strong>
                        @else —
                        @endif
                    </td>
                    <td class="text-center">
                        @if($ra?->moyenne_s6 !== null)
                            @php $c = $ra->moyenne_s6 >= 10 ? '#27500a' : ($ra->moyenne_s6 >= 6 ? '#633806' : '#791f1f'); @endphp
                            <strong style="color:{{ $c }};">{{ number_format($ra->moyenne_s6, 2) }}</strong>
                        @else —
                        @endif
                    </td>
                    <td class="text-center">
                        @if($ra?->moyenne_annuelle !== null)
                            @php $c = $ra->moyenne_annuelle >= 10 ? '#27500a' : ($ra->moyenne_annuelle >= 6 ? '#633806' : '#791f1f'); @endphp
                            <strong style="color:{{ $c }};">{{ number_format($ra->moyenne_annuelle, 2) }}</strong>
                        @else —
                        @endif
                    </td>
                    <td class="text-center">{{ $ra?->credits_acquis ?? 0 }}/60</td>
                    <td class="text-center">
                        @if($ra && $ra->mention != 'AUCUNE')
                            <span class="mention-badge">{{ $mentions[$ra->mention] ?? '—' }}</span>
                        @else —
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$ra || $ra->decision_jury == 'EN_ATTENTE')
                            <span class="decision-attente">En attente</span>
                        @elseif($ra->decision_jury == 'DIPLOME')
                            <span class="decision-diplome">✅ Diplômé(e)</span>
                        @elseif($ra->decision_jury == 'REPRISE_SOUTENANCE')
                            <span class="decision-reprise">⚡ Reprise soutenance</span>
                        @elseif($ra->decision_jury == 'REDOUBLE')
                            <span class="decision-redouble">❌ Redouble</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('bulletins.annuel', $etudiant->id) }}"
                           target="_blank" class="tb-btn" title="Bulletin annuel"
                           style="background:#fcebeb; border-color:#f7c1c1; color:#791f1f;">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding:40px; color:#9ca3af;">
                        <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                        Aucun étudiant enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var val = this.value.toLowerCase();
        document.querySelectorAll('#juryTable tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
        });
    });
</script>
@endsection