@extends('layouts.app')
@section('title', 'Bulletins')
@section('page_title', 'Génération des Bulletins')
@section('page_sub', 'Année universitaire ' . $annee)

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-file-alt me-2" style="color:#2e7df7;"></i>Bulletins de Notes</h1>
        <p>Générer et publier les bulletins par étudiant</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <input type="text" id="searchInput" class="form-control-inptic"
               placeholder="🔍 Rechercher..." style="width:220px;">
        <form method="POST" action="{{ route('bulletins.publier-tous') }}">
            @csrf
            <button type="submit" class="btn-primary-inptic"
                    onclick="return confirm('Publier les bulletins de TOUS les étudiants ?')">
                <i class="fas fa-globe"></i> Publier tout
            </button>
        </form>
    </div>
</div>

<div class="card-white">
    <div id="etudiantsList">
        @forelse($etudiants as $etudiant)
        @php
            $ra = \App\Models\ResultatAnnuel::where('etudiant_id', $etudiant->id)
                    ->where('annee_univ', $annee)->first();
            $publie = $ra && $ra->publie_etudiant;
        @endphp
        <div class="etudiant-item d-flex align-items-center justify-content-between"
             style="padding:14px 16px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:10px; background:#fff; transition:border-color 0.15s;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#1e2a3a,#2e7df7); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; flex-shrink:0;">
                    {{ strtoupper(substr($etudiant->nom, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:600; color:#1e2a3a; font-size:13px;">
                        {{ $etudiant->nom }} {{ $etudiant->prenom }}
                    </div>
                    <div style="color:#6b7280; font-size:11px;">{{ $etudiant->matricule }}</div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">

                <!-- Statut publication -->
                @if($publie)
                    <span class="badge-vert">
                        <i class="fas fa-check-circle me-1"></i>Publié
                    </span>
                @else
                    <span class="badge-gris">
                        <i class="fas fa-eye-slash me-1"></i>Non publié
                    </span>
                @endif

                <!-- PDF S5 -->
                <a href="{{ route('bulletins.s5', $etudiant->id) }}" target="_blank"
                   style="background:#fcebeb; color:#791f1f; border:1px solid #f7c1c1; border-radius:7px; padding:6px 10px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <i class="fas fa-file-pdf"></i> S5
                </a>

                <!-- PDF S6 -->
                <a href="{{ route('bulletins.s6', $etudiant->id) }}" target="_blank"
                   style="background:#fcebeb; color:#791f1f; border:1px solid #f7c1c1; border-radius:7px; padding:6px 10px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <i class="fas fa-file-pdf"></i> S6
                </a>

                <!-- PDF Annuel -->
                <a href="{{ route('bulletins.annuel', $etudiant->id) }}" target="_blank"
                   style="background:#e6f1fb; color:#0c447c; border:1px solid #b5d4f4; border-radius:7px; padding:6px 10px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <i class="fas fa-file-pdf"></i> Annuel
                </a>

                <!-- Bouton Publier / Dépublier -->
                @if($publie)
                    <form method="POST" action="{{ route('bulletins.depublier', $etudiant->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                                style="background:#faeeda; color:#633806; border:1px solid #fac775; border-radius:7px; padding:6px 12px; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                            <i class="fas fa-eye-slash"></i> Dépublier
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('bulletins.publier', $etudiant->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                                style="background:#eaf3de; color:#27500a; border:1px solid #97c459; border-radius:7px; padding:6px 12px; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                            <i class="fas fa-globe"></i> Publier
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center" style="padding:40px; color:#9ca3af;">
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