@extends('layouts.app')
@section('title', 'Notes')
@section('page_title', 'Saisie des Notes')
@section('page_sub', 'Sélectionner un étudiant · Année ' . $annee)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-pen me-2" style="color:#2e7df7;"></i>Saisie des Notes</h1>
        <p>Cliquez sur un étudiant pour saisir ou consulter ses notes</p>
    </div>
    <input type="text" id="searchInput" class="form-control-inptic"
           placeholder="🔍 Rechercher un étudiant..."
           style="width:280px;">
</div>

<div class="card-white">
    <div class="row g-3" id="etudiantsList">
        @forelse($etudiants as $etudiant)
        <div class="col-md-4 col-lg-3 etudiant-item">
            <a href="{{ route('notes.show', $etudiant->id) }}"
               style="display:block; border:1px solid #e5e7eb; border-radius:10px; padding:14px 16px; text-decoration:none; transition:all 0.15s; background:#fff;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,#1e2a3a,#2e7df7); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; flex-shrink:0;">
                        {{ strtoupper(substr($etudiant->nom, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600; color:#1e2a3a; font-size:13px;">
                            {{ $etudiant->nom }} {{ $etudiant->prenom }}
                        </div>
                        <div style="color:#6b7280; font-size:11px;">
                            {{ $etudiant->matricule }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center" style="padding:40px; color:#9ca3af;">
            <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
            Aucun étudiant enregistré.
        </div>
        @endforelse
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var val = this.value.toLowerCase();
        document.querySelectorAll('.etudiant-item').forEach(function(item) {
            item.style.display = item.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
        });
    });
</script>
@endsection