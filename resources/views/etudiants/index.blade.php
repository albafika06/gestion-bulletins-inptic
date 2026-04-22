@extends('layouts.app')

@section('title', 'Étudiants')
@section('page_title', 'Gestion des Étudiants')
@section('page_sub', 'Liste des étudiants inscrits · ' . $annee)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-graduate me-2" style="color:#2e7df7;"></i>Étudiants</h1>
        <p>{{ $etudiants->count() }} étudiant(s) inscrit(s) pour l'année <strong>{{ $annee }}</strong></p>
    </div>
    <a href="{{ route('etudiants.create') }}" class="btn-primary-inptic">
        <i class="fas fa-plus"></i> Ajouter un étudiant
    </a>
</div>

<div class="card-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span style="font-size:13px; color:#6b7280;">
            <strong>{{ $etudiants->count() }}</strong> étudiant(s) trouvé(s)
        </span>
        <input type="text" id="searchInput" class="form-control-inptic"
               placeholder="🔍 Rechercher un étudiant..."
               style="width:280px;">
    </div>

    <div class="table-responsive">
        <table class="table table-inptic" id="tableEtudiants">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Matricule</th>
                    <th>Nom et Prénom</th>
                    <th>Date de naissance</th>
                    <th>Lieu de naissance</th>
                    <th class="text-center">Sexe</th>
                    <th>Bac</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($etudiants as $i => $etudiant)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $etudiant->matricule }}</strong></td>
                    <td>{{ $etudiant->nom }} {{ $etudiant->prenom }}</td>
                    <td>{{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : '—' }}</td>
                    <td>{{ $etudiant->lieu_naissance ?? '—' }}</td>
                    <td class="text-center">
                        @if($etudiant->sexe == 'M')
                            <span class="badge-bleu">M</span>
                        @elseif($etudiant->sexe == 'F')
                            <span class="badge-rouge">F</span>
                        @else —
                        @endif
                    </td>
                    <td>{{ $etudiant->type_bac ?? '—' }}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('etudiants.show', $etudiant->id) }}"
                               class="tb-btn" title="Voir la fiche">
                                <i class="fas fa-eye" style="font-size:12px;"></i>
                            </a>
                            <a href="{{ route('etudiants.edit', $etudiant->id) }}"
                               class="tb-btn" title="Modifier">
                                <i class="fas fa-edit" style="font-size:12px;"></i>
                            </a>
                            <form method="POST" action="{{ route('etudiants.destroy', $etudiant->id) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Supprimer cet étudiant ?')">
                                @csrf @method('DELETE')
                                <button class="tb-btn" style="border:1px solid #f7c1c1; background:#fcebeb; color:#791f1f;" title="Supprimer">
                                    <i class="fas fa-trash" style="font-size:12px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding:40px; color:#9ca3af;">
                        <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                        Aucun étudiant enregistré pour cette année.
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
        document.querySelectorAll('#tableEtudiants tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
        });
    });
</script>
@endsection