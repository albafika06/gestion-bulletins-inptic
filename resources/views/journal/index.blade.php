@extends('layouts.app')
@section('title', 'Journal d\'activité')
@section('page_title', 'Journal d\'activité')
@section('page_sub', 'Historique complet des actions')

@section('head')
<style>
    .timeline { position:relative; padding-left:24px; }
    .timeline::before { content:''; position:absolute; left:8px; top:0; bottom:0; width:1.5px; background:#e5e7eb; }
    .tl-item { position:relative; margin-bottom:10px; }
    .tl-dot { position:absolute; left:-20px; top:6px; width:10px; height:10px; border-radius:50%; border:2px solid #fff; }
    .tl-card { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:11px 14px; transition:border-color 0.15s; }
    .tl-card:hover { border-color:#2e7df7; }
    .tl-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:4px; }
    .tl-user { display:flex; align-items:center; gap:8px; }
    .tl-avatar { width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:8px; font-weight:700; color:#fff; flex-shrink:0; }
    .tl-name { font-size:11px; font-weight:600; color:#1e2a3a; }
    .tl-time { font-size:10px; color:#9ca3af; }
    .tl-action { font-size:11px; color:#374151; line-height:1.5; }
    .tl-detail { font-size:10px; color:#6b7280; background:#f8f9ff; border-radius:5px; padding:3px 8px; display:inline-block; margin-top:4px; }
    .cb-select { width:15px; height:15px; accent-color:#2e7df7; cursor:pointer; }
    .stats-mini { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
    .stat-mini { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:12px; text-align:center; }
    .stat-mini-num { font-size:20px; font-weight:700; color:#1e2a3a; }
    .stat-mini-lbl { font-size:10px; color:#6b7280; margin-top:2px; }
</style>
@endsection

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-history me-2" style="color:#2e7df7;"></i>Journal d'activité</h1>
        <p>Historique complet de toutes les actions effectuées</p>
    </div>
    <form method="POST" action="{{ route('journal.vider') }}"
          onsubmit="return confirm('Vider TOUT le journal ? Cette action est irréversible.')">
        @csrf
        <button type="submit" class="btn-danger-inptic">
            <i class="fas fa-trash"></i> Vider tout le journal
        </button>
    </form>
</div>

{{-- Stats du jour --}}
<div class="stats-mini">
    <div class="stat-mini">
        <div class="stat-mini-num" style="color:#2e7df7;">{{ $stats['today_total'] }}</div>
        <div class="stat-mini-lbl">Actions aujourd'hui</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-num" style="color:#27500a;">{{ $stats['today_notes'] }}</div>
        <div class="stat-mini-lbl">Notes saisies</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-num" style="color:#633806;">{{ $stats['today_bulletins'] }}</div>
        <div class="stat-mini-lbl">Bulletins publiés</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-num" style="color:#374151;">{{ $stats['today_connexions'] }}</div>
        <div class="stat-mini-lbl">Connexions</div>
    </div>
</div>

{{-- Filtres --}}
<div class="card-white mb-3">
    <form method="GET" action="{{ route('journal.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
        <select name="type" class="form-control-inptic" style="width:180px;">
            <option value="">Tous les types</option>
            <option value="NOTE_SAISIE"     {{ request('type') == 'NOTE_SAISIE'     ? 'selected' : '' }}>Notes</option>
            <option value="BULLETIN_PUBLIE" {{ request('type') == 'BULLETIN_PUBLIE' ? 'selected' : '' }}>Bulletins</option>
            <option value="CONNEXION"       {{ request('type') == 'CONNEXION'       ? 'selected' : '' }}>Connexions</option>
            <option value="ETUDIANT_AJOUTE" {{ request('type') == 'ETUDIANT_AJOUTE' ? 'selected' : '' }}>Étudiants</option>
            <option value="IMPORT_EXCEL"    {{ request('type') == 'IMPORT_EXCEL'    ? 'selected' : '' }}>Import Excel</option>
            <option value="RELEVE_PUBLIE"   {{ request('type') == 'RELEVE_PUBLIE'   ? 'selected' : '' }}>Relevés</option>
            <option value="RESET_PASSWORD"  {{ request('type') == 'RESET_PASSWORD'  ? 'selected' : '' }}>Reset mdp</option>
        </select>
        <select name="role" class="form-control-inptic" style="width:160px;">
            <option value="">Tous les rôles</option>
            <option value="ADMIN"       {{ request('role') == 'ADMIN'       ? 'selected' : '' }}>Admin</option>
            <option value="ENSEIGNANT"  {{ request('role') == 'ENSEIGNANT'  ? 'selected' : '' }}>Enseignant</option>
            <option value="SECRETARIAT" {{ request('role') == 'SECRETARIAT' ? 'selected' : '' }}>Secrétariat</option>
        </select>
        <input type="date" name="date" value="{{ request('date') }}"
               class="form-control-inptic" style="width:160px;">
        <button type="submit" class="btn-primary-inptic">
            <i class="fas fa-filter"></i> Filtrer
        </button>
        <a href="{{ route('journal.index') }}" class="btn-secondary-inptic">
            <i class="fas fa-times"></i> Réinitialiser
        </a>
    </form>
</div>

{{-- Journal --}}
<div class="card-white">
    <form method="POST" action="{{ route('journal.destroy-selection') }}" id="selectionForm">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="card-white-title mb-0">
                <i class="fas fa-list"></i>
                {{ $journaux->total() }} entrée(s) au total
            </div>
            <button type="submit"
                    class="btn-danger-inptic"
                    style="font-size:11px; padding:6px 12px;"
                    onclick="return confirm('Supprimer la sélection ?')"
                    id="btnSupprimerSelection">
                <i class="fas fa-trash"></i> Supprimer la sélection
            </button>
        </div>

        @if($journaux->count() > 0)
        <div class="timeline">
            @foreach($journaux as $journal)
            @php
                $couleurs = [
                    'vert'   => ['bg' => '#eaf3de', 'color' => '#27500a', 'dot' => '#27500a'],
                    'bleu'   => ['bg' => '#e6f1fb', 'color' => '#0c447c', 'dot' => '#2e7df7'],
                    'jaune'  => ['bg' => '#faeeda', 'color' => '#633806', 'dot' => '#e65100'],
                    'rouge'  => ['bg' => '#fcebeb', 'color' => '#791f1f', 'dot' => '#c62828'],
                    'violet' => ['bg' => '#e1d5e7', 'color' => '#3c3489', 'dot' => '#3c3489'],
                    'gris'   => ['bg' => '#f1efe8', 'color' => '#444441', 'dot' => '#9ca3af'],
                ];
                $c   = $couleurs[$journal->couleur] ?? $couleurs['gris'];
                $avatarColor = match($journal->utilisateur?->role ?? '') {
                    'ADMIN'       => '#2e7df7',
                    'ENSEIGNANT'  => '#3c3489',
                    'SECRETARIAT' => '#27500a',
                    default       => '#9ca3af',
                };
            @endphp
            <div class="tl-item">
                <div class="tl-dot" style="background:{{ $c['dot'] }};"></div>
                <div class="tl-card">
                    <div class="tl-header">
                        <div class="tl-user">
                            <input type="checkbox" name="ids[]"
                                   value="{{ $journal->id }}"
                                   class="cb-select">
                            <div class="tl-avatar" style="background:{{ $avatarColor }};">
                                {{ strtoupper(substr($journal->utilisateur?->nom_affichage ?? 'SY', 0, 2)) }}
                            </div>
                            <div>
                                <div class="tl-name">
                                    {{ $journal->utilisateur?->nom_affichage ?? 'Système' }}
                                </div>
                                <div style="font-size:9px; color:#6b7280;">
                                    {{ $journal->utilisateur?->role ?? '' }}
                                </div>
                            </div>
                            <span style="background:{{ $c['bg'] }}; color:{{ $c['color'] }}; padding:2px 9px; border-radius:10px; font-size:9px; font-weight:500;">
                                <i class="fas {{ $journal->icone }}" style="font-size:9px;"></i>
                                {{ str_replace('_', ' ', $journal->action) }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="tl-time">
                                {{ $journal->created_at?->format('d/m/Y H:i') }}
                            </div>
                            <form method="POST"
                                  action="{{ route('journal.destroy', $journal->id) }}"
                                  onsubmit="return confirm('Supprimer cette entrée ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="width:24px; height:24px; border-radius:5px; background:#fcebeb; border:1px solid #f7c1c1; color:#791f1f; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-times" style="font-size:9px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="tl-action">{{ $journal->description }}</div>
                    @if($journal->details)
                        <div class="tl-detail">{{ $journal->details }}</div>
                    @endif
                    @if($journal->ip_address)
                        <div style="font-size:9px; color:#9ca3af; margin-top:3px;">
                            <i class="fas fa-map-marker-alt" style="font-size:8px;"></i>
                            IP : {{ $journal->ip_address }}
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $journaux->links() }}
        </div>
        @else
        <div class="text-center" style="padding:48px; color:#9ca3af;">
            <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
            Aucune activité enregistrée.
        </div>
        @endif
    </form>
</div>

@endsection