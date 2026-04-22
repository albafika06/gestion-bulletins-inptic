@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Vue d\'ensemble · Année ' . config('app.annee_courante', '2025/2026'))

@section('head')
<style>
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }

    /* Timeline journal */
    .jl-timeline { position:relative; padding-left:24px; }
    .jl-timeline::before { content:''; position:absolute; left:8px; top:0; bottom:0; width:1.5px; background:#e5e7eb; }
    .jl-item { position:relative; margin-bottom:10px; }
    .jl-dot { position:absolute; left:-20px; top:10px; width:10px; height:10px; border-radius:50%; border:2px solid #fff; }
    .jl-card { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:10px; transition:border-color 0.15s; }
    .jl-card:hover { border-color:#2e7df7; }
    .jl-avatar { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:#fff; flex-shrink:0; }
    .jl-body { flex:1; min-width:0; }
    .jl-title { font-size:12px; font-weight:600; color:#1e2a3a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .jl-sub { font-size:10px; color:#6b7280; margin-top:2px; }
    .jl-right { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
    .jl-badge { padding:2px 9px; border-radius:10px; font-size:9px; font-weight:500; }
    .jl-time { font-size:10px; color:#9ca3af; }

    /* Absences secrétariat */
    .abs-row { display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:8px; border:1px solid #e5e7eb; margin-bottom:6px; background:#fff; }
    .abs-etudiant { font-size:12px; font-weight:600; color:#1e2a3a; flex:1; }
    .abs-matiere { font-size:10px; color:#6b7280; }
    .abs-heures { font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px; }

    /* Actions rapides */
    .actions-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .action-btn { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; text-decoration:none; transition:all 0.15s; color:#1e2a3a; }
    .action-btn:hover { border-color:#2e7df7; background:#f0f6ff; color:#2e7df7; text-decoration:none; }
    .action-btn-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .action-btn-label { font-size:12px; font-weight:500; }

    /* Matière enseignant */
    .mat-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; background:#fff; transition:border-color 0.15s; }
    .mat-card:hover { border-color:#2e7df7; }
    .stat-mini-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:10px; }
    .stat-mini { background:#f8f9ff; border-radius:8px; padding:8px; text-align:center; }
    .stat-mini-val { font-size:13px; font-weight:700; color:#1e2a3a; }
    .stat-mini-lbl { font-size:9px; color:#6b7280; margin-top:2px; }
    .rep-row { display:flex; gap:10px; margin-bottom:12px; flex-wrap:wrap; }
    .rep-item { display:flex; align-items:center; gap:4px; font-size:11px; }
    .rep-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
    .progress-bar-wrap { background:#f0f2f5; border-radius:10px; height:6px; overflow:hidden; }

    /* UE étudiant */
    .ue-etudiant { border:1px solid #e5e7eb; border-radius:8px; padding:12px 14px; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between; }
    .ue-etudiant-name { font-size:12px; font-weight:600; color:#1e2a3a; }
    .ue-etudiant-sub { font-size:10px; color:#6b7280; margin-top:2px; }
    .mat-note-row { display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; margin-bottom:6px; background:#f8f9ff; border:1px solid #e5e7eb; }
    .mat-note-name { font-size:12px; color:#1e2a3a; flex:1; font-weight:500; }
    .mat-moy { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; min-width:50px; text-align:center; }
    .sem-label { font-size:10px; font-weight:600; color:#2e7df7; margin:12px 0 8px; padding-bottom:4px; border-bottom:1px solid #e5e7eb; text-transform:uppercase; letter-spacing:0.5px; }

    @media (max-width:1024px) { .stats-grid { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:600px)  { .stats-grid { grid-template-columns:1fr; } .actions-grid { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')

{{-- ════════════════════════════════════
     ADMIN
════════════════════════════════════ --}}
@if(Auth::user()->isAdmin())

<div class="page-header">
    <h1><i class="fas fa-th-large me-2" style="color:#2e7df7;"></i>Tableau de bord</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom_affichage }}</strong> ·
        <span style="color:#2e7df7;">ADMIN</span>
    </p>
</div>

{{-- 4 Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6f1fb;">
            <i class="fas fa-user-graduate" style="color:#0c447c;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['total_etudiants'] ?? 0 }}</div>
            <div class="stat-lbl">Étudiants inscrits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eaf3de;">
            <i class="fas fa-graduation-cap" style="color:#27500a;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['diplomes'] ?? 0 }}</div>
            <div class="stat-lbl">Diplômés</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fcebeb;">
            <i class="fas fa-redo" style="color:#791f1f;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['redoublants'] ?? 0 }}</div>
            <div class="stat-lbl">Redoublants</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#faeeda;">
            <i class="fas fa-clock" style="color:#633806;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['en_attente'] ?? 0 }}</div>
            <div class="stat-lbl">En attente jury</div>
        </div>
    </div>
</div>

{{-- Journal 5 dernières actions --}}
<div class="card-white">
    <div class="card-white-title d-flex justify-content-between align-items-center">
        <span><i class="fas fa-history me-2"></i>Journal d'activité — 5 dernières actions</span>
        <a href="{{ route('journal.index') }}"
           style="font-size:12px;color:#2e7df7;font-weight:500;text-decoration:none;">
            Voir tout le journal <i class="fas fa-chevron-right" style="font-size:10px;"></i>
        </a>
    </div>

    @php
        $journalRecent = $stats['journal_recent'] ?? collect();
        $couleurs = [
            'bleu'   => ['bg'=>'#e6f1fb','color'=>'#0c447c','dot'=>'#2e7df7'],
            'vert'   => ['bg'=>'#eaf3de','color'=>'#27500a','dot'=>'#27500a'],
            'jaune'  => ['bg'=>'#faeeda','color'=>'#633806','dot'=>'#e65100'],
            'rouge'  => ['bg'=>'#fcebeb','color'=>'#791f1f','dot'=>'#c62828'],
            'violet' => ['bg'=>'#ede9f7','color'=>'#3c3489','dot'=>'#3c3489'],
            'gris'   => ['bg'=>'#f1efe8','color'=>'#444441','dot'=>'#9ca3af'],
        ];
    @endphp

    @if($journalRecent->count() > 0)
    <div class="jl-timeline">
        @foreach($journalRecent as $j)
        @php
            $c = $couleurs[$j->couleur] ?? $couleurs['gris'];
            $avatarColor = match($j->utilisateur?->role ?? '') {
                'ADMIN'       => '#2e7df7',
                'ENSEIGNANT'  => '#3c3489',
                'SECRETARIAT' => '#27500a',
                default       => '#9ca3af',
            };
        @endphp
        <div class="jl-item">
            <div class="jl-dot" style="background:{{ $c['dot'] }};"></div>
            <div class="jl-card">
                <div class="jl-avatar" style="background:{{ $avatarColor }};">
                    {{ strtoupper(substr($j->utilisateur?->nom_affichage ?? 'SY', 0, 2)) }}
                </div>
                <div class="jl-body">
                    <div class="jl-title">
                        <i class="fas {{ $j->icone }}" style="font-size:10px;color:{{ $c['dot'] }};margin-right:5px;"></i>
                        {{ $j->description }}
                    </div>
                    <div class="jl-sub">
                        {{ $j->utilisateur?->nom_affichage ?? 'Système' }}
                        @if($j->utilisateur?->role) · {{ $j->utilisateur->role }} @endif
                        @if($j->details) · {{ $j->details }} @endif
                    </div>
                </div>
                <div class="jl-right">
                    <span class="jl-badge" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};">
                        {{ str_replace('_', ' ', $j->action) }}
                    </span>
                    <span class="jl-time">
                        @if($j->created_at?->isToday())
                            {{ $j->created_at->format('H\hi') }}
                        @else
                            Hier {{ $j->created_at?->format('H\hi') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center" style="padding:32px;color:#9ca3af;font-size:13px;">
        <i class="fas fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
        Aucune activité enregistrée.
    </div>
    @endif
</div>

@endif {{-- fin Admin --}}


{{-- ════════════════════════════════════
     SECRÉTARIAT
════════════════════════════════════ --}}
@if(Auth::user()->isSecretariat())

<div class="page-header">
    <h1><i class="fas fa-th-large me-2" style="color:#2e7df7;"></i>Tableau de bord</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom_affichage }}</strong> ·
        <span style="color:#2e7df7;">SECRÉTARIAT</span>
    </p>
</div>

{{-- 4 Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6f1fb;">
            <i class="fas fa-user-graduate" style="color:#0c447c;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['total_etudiants'] ?? 0 }}</div>
            <div class="stat-lbl">Étudiants inscrits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eaf3de;">
            <i class="fas fa-file-alt" style="color:#27500a;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['bulletins_publies'] ?? 0 }}</div>
            <div class="stat-lbl">Bulletins publiés</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#faeeda;">
            <i class="fas fa-file" style="color:#633806;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['bulletins_non_publies'] ?? 0 }}</div>
            <div class="stat-lbl">Bulletins non publiés</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fcebeb;">
            <i class="fas fa-user-times" style="color:#791f1f;"></i>
        </div>
        <div>
            <div class="stat-num">{{ $stats['absences_non_justifiees'] ?? 0 }}</div>
            <div class="stat-lbl">Absences non justifiées</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

    {{-- 5 dernières absences --}}
    <div class="card-white">
        <div class="card-white-title d-flex justify-content-between align-items-center">
            <span><i class="fas fa-user-times me-2"></i>5 dernières absences</span>
            <a href="{{ route('absences.index') }}"
               style="font-size:12px;color:#2e7df7;font-weight:500;text-decoration:none;">
                Voir tout <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            </a>
        </div>

        @php $dernieresAbsences = $stats['dernieres_absences'] ?? collect(); @endphp

        @if($dernieresAbsences->count() > 0)
            @foreach($dernieresAbsences as $abs)
            @php
                $absBg    = $abs->justifie ? '#eaf3de' : '#fcebeb';
                $absColor = $abs->justifie ? '#27500a' : '#791f1f';
            @endphp
            <div class="abs-row">
                <div style="width:32px;height:32px;border-radius:50%;background:{{ $absBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-user" style="font-size:12px;color:{{ $absColor }};"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="abs-etudiant">
                        {{ $abs->etudiant?->nom ?? '—' }} {{ $abs->etudiant?->prenom ?? '' }}
                    </div>
                    <div class="abs-matiere">
                        {{ $abs->matiere?->libelle ?? 'Matière inconnue' }}
                        · {{ ucfirst($abs->semestre ?? '') }}
                    </div>
                </div>
                <div>
                    <span class="abs-heures" style="background:{{ $absBg }};color:{{ $absColor }};">
                        {{ $abs->heures }}h
                    </span>
                </div>
                <div>
                    @if($abs->justifie)
                        <span style="font-size:9px;background:#eaf3de;color:#27500a;padding:2px 8px;border-radius:10px;">Justifiée</span>
                    @else
                        <span style="font-size:9px;background:#fcebeb;color:#791f1f;padding:2px 8px;border-radius:10px;">Non justifiée</span>
                    @endif
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center" style="padding:24px;color:#9ca3af;font-size:13px;">
                <i class="fas fa-check-circle" style="font-size:1.5rem;display:block;margin-bottom:8px;color:#27500a;"></i>
                Aucune absence enregistrée.
            </div>
        @endif
    </div>

    {{-- Actions rapides --}}
    <div class="card-white">
        <div class="card-white-title">
            <i class="fas fa-bolt me-2"></i>Actions rapides
        </div>
        <div class="actions-grid">
            <a href="{{ route('etudiants.create') }}" class="action-btn">
                <div class="action-btn-icon" style="background:#e6f1fb;">
                    <i class="fas fa-user-plus" style="color:#0c447c;font-size:14px;"></i>
                </div>
                <span class="action-btn-label">Ajouter un étudiant</span>
            </a>
            <a href="{{ route('absences.index') }}" class="action-btn">
                <div class="action-btn-icon" style="background:#fcebeb;">
                    <i class="fas fa-user-times" style="color:#791f1f;font-size:14px;"></i>
                </div>
                <span class="action-btn-label">Gérer les absences</span>
            </a>
            <a href="{{ route('bulletins.index') }}" class="action-btn">
                <div class="action-btn-icon" style="background:#eaf3de;">
                    <i class="fas fa-file-alt" style="color:#27500a;font-size:14px;"></i>
                </div>
                <span class="action-btn-label">Bulletins PDF</span>
            </a>
            <a href="{{ route('etudiants.index') }}" class="action-btn">
                <div class="action-btn-icon" style="background:#faeeda;">
                    <i class="fas fa-users" style="color:#633806;font-size:14px;"></i>
                </div>
                <span class="action-btn-label">Liste étudiants</span>
            </a>
            <a href="{{ route('import.index') }}" class="action-btn">
                <div class="action-btn-icon" style="background:#ede9f7;">
                    <i class="fas fa-file-excel" style="color:#3c3489;font-size:14px;"></i>
                </div>
                <span class="action-btn-label">Import Excel</span>
            </a>
            <a href="{{ route('jury.index') }}" class="action-btn">
                <div class="action-btn-icon" style="background:#e6f1fb;">
                    <i class="fas fa-gavel" style="color:#0c447c;font-size:14px;"></i>
                </div>
                <span class="action-btn-label">Décisions Jury</span>
            </a>
        </div>
    </div>

</div>

@endif {{-- fin Secrétariat --}}


{{-- ════════════════════════════════════
     ENSEIGNANT
════════════════════════════════════ --}}
@if(Auth::user()->isEnseignant())

<div class="page-header">
    <h1><i class="fas fa-chalkboard-teacher me-2" style="color:#2e7df7;"></i>Tableau de bord</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom_affichage }}</strong> · Enseignant</p>
</div>

@php
    $mesMatieres = $stats['mes_matieres'] ?? collect();
    $statsMat    = $stats['stats_matieres'] ?? [];
@endphp

@if($mesMatieres->count() > 0)

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f1fb;">
                <i class="fas fa-book" style="color:#0c447c;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $mesMatieres->count() }}</div>
                <div class="stat-lbl">Matières assignées</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf3de;">
                <i class="fas fa-user-graduate" style="color:#27500a;"></i>
            </div>
            <div>
                <div class="stat-num">{{ $stats['total_etudiants'] ?? 0 }}</div>
                <div class="stat-lbl">Étudiants dans la promotion</div>
            </div>
        </div>
    </div>
</div>

<div class="card-white">
    <div class="card-white-title">
        <i class="fas fa-list"></i> Mes matières assignées
    </div>
    <div class="row g-3">
        @foreach($mesMatieres as $em)
        @php
            $sm      = $statsMat[$em->matiere_id] ?? [];
            $pct     = $sm['pct'] ?? 0;
            $couleur = $sm['couleur'] ?? '#c62828';
            $saisies = $sm['saisies'] ?? 0;
        @endphp
        <div class="col-md-4">
            <div class="mat-card">
                <div style="font-size:10px;color:#6b7280;margin-bottom:4px;">
                    {{ $em->matiere->ue->code }} · {{ $em->matiere->ue->semestre->libelle ?? '' }}
                </div>
                <div style="font-weight:600;color:#1e2a3a;font-size:13px;margin-bottom:3px;">
                    {{ $em->matiere->libelle }}
                </div>
                <div style="font-size:11px;color:#6b7280;margin-bottom:12px;">
                    Coeff: {{ $em->matiere->coefficient }} · {{ $em->matiere->credits }} crédit(s)
                </div>
                <div style="display:flex;justify-content:space-between;font-size:11px;color:#6b7280;margin-bottom:4px;">
                    <span>Progression saisie</span>
                    <span style="color:{{ $couleur }};font-weight:600;">{{ $saisies }}/{{ $stats['total_etudiants'] ?? 0 }}</span>
                </div>
                <div class="progress-bar-wrap" style="margin-bottom:14px;">
                    <div style="height:100%;border-radius:10px;width:{{ $pct }}%;background:{{ $couleur }};transition:width 0.5s;"></div>
                </div>
                <div style="border-top:1px solid #e5e7eb;margin-bottom:12px;"></div>
                <div class="stat-mini-grid">
                    <div class="stat-mini">
                        <div class="stat-mini-val">{{ isset($sm['moy_classe']) ? number_format($sm['moy_classe'],2) : '—' }}</div>
                        <div class="stat-mini-lbl">Moy. classe</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-val" style="color:{{ isset($sm['note_max']) ? '#27500a' : '#6b7280' }};">{{ isset($sm['note_max']) ? number_format($sm['note_max'],2) : '—' }}</div>
                        <div class="stat-mini-lbl">Note max</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-val" style="color:{{ isset($sm['note_min']) ? '#791f1f' : '#6b7280' }};">{{ isset($sm['note_min']) ? number_format($sm['note_min'],2) : '—' }}</div>
                        <div class="stat-mini-lbl">Note min</div>
                    </div>
                </div>
                @if(isset($sm['nb_valides']))
                <div class="rep-row">
                    <div class="rep-item"><div class="rep-dot" style="background:#27500a;"></div><span style="font-weight:600;color:#27500a;">{{ $sm['nb_valides'] }}</span><span style="color:#6b7280;">validés</span></div>
                    <div class="rep-item"><div class="rep-dot" style="background:#e65100;"></div><span style="font-weight:600;color:#e65100;">{{ $sm['nb_faibles'] }}</span><span style="color:#6b7280;">faibles</span></div>
                    <div class="rep-item"><div class="rep-dot" style="background:#c62828;"></div><span style="font-weight:600;color:#c62828;">{{ $sm['nb_insuff'] }}</span><span style="color:#6b7280;">insuff.</span></div>
                </div>
                @endif
                <div class="d-flex gap-2">
                    <a href="{{ route('enseignant.saisir', $em->matiere_id) }}" class="btn-primary-inptic" style="flex:1;justify-content:center;font-size:11px;padding:7px 8px;">
                        <i class="fas fa-pen"></i> Saisir
                    </a>
                    <a href="{{ route('enseignant.releve', $em->matiere_id) }}" class="btn-secondary-inptic" style="flex:1;justify-content:center;font-size:11px;padding:7px 8px;">
                        <i class="fas fa-list-alt"></i> Relevé
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@else
<div class="card-white text-center" style="padding:48px;">
    <div style="width:64px;height:64px;background:#faeeda;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <i class="fas fa-exclamation-triangle" style="color:#633806;font-size:24px;"></i>
    </div>
    <div style="font-size:16px;font-weight:600;color:#1e2a3a;margin-bottom:8px;">Aucune matière assignée</div>
    <div style="font-size:13px;color:#6b7280;">Contactez l'administrateur pour qu'il vous assigne vos matières.</div>
</div>
@endif
@endif {{-- fin Enseignant --}}


{{-- ════════════════════════════════════
     ÉTUDIANT
════════════════════════════════════ --}}
@if(Auth::user()->isEtudiant())

<div class="page-header">
    <h1><i class="fas fa-user-graduate me-2" style="color:#2e7df7;"></i>Mon espace</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom_affichage }}</strong></p>
</div>

@php
    $ra       = $stats['resultat_annuel'] ?? null;
    $etudiant = $stats['etudiant'] ?? null;
    $anneeEtu = config('app.annee_courante', '2025/2026');
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6f1fb;"><i class="fas fa-chart-line" style="color:#0c447c;"></i></div>
        <div><div class="stat-num">{{ $ra?->moyenne_s5 ? number_format($ra->moyenne_s5,2) : '—' }}</div><div class="stat-lbl">Moyenne S5</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eaf3de;"><i class="fas fa-chart-bar" style="color:#27500a;"></i></div>
        <div><div class="stat-num">{{ $ra?->moyenne_s6 ? number_format($ra->moyenne_s6,2) : '—' }}</div><div class="stat-lbl">Moyenne S6</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#faeeda;"><i class="fas fa-star" style="color:#633806;"></i></div>
        <div><div class="stat-num">{{ $ra?->moyenne_annuelle ? number_format($ra->moyenne_annuelle,2) : '—' }}</div><div class="stat-lbl">Moyenne annuelle</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fcebeb;"><i class="fas fa-award" style="color:#791f1f;"></i></div>
        <div><div class="stat-num">{{ $ra?->credits_acquis ?? 0 }}/60</div><div class="stat-lbl">Crédits acquis</div></div>
    </div>
</div>

@if($etudiant)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

    <div class="card-white">
        <div class="card-white-title"><i class="fas fa-layer-group"></i> Récapitulatif de mes UE</div>
        @php $semestres = \App\Models\Semestre::with(['unitesEnseignement'])->orderBy('ordre')->get(); @endphp
        @foreach($semestres as $semestre)
        <div class="sem-label">{{ $semestre->libelle }}</div>
        @foreach($semestre->unitesEnseignement as $ue)
        @php
            $moyUE = \App\Models\MoyenneUE::where('etudiant_id',$etudiant->id)->where('ue_id',$ue->id)->where('annee_univ',$anneeEtu)->first();
            $statutBadge = '—'; $statutClass = 'badge-gris';
            if($moyUE) {
                if($moyUE->statut=='ACQUISE')      { $statutBadge='Acquise';    $statutClass='badge-vert'; }
                elseif($moyUE->statut=='COMPENSEE') { $statutBadge='Compensée'; $statutClass='badge-jaune'; }
                elseif($moyUE->statut=='NON_ACQUISE'){ $statutBadge='Non acquise';$statutClass='badge-rouge'; }
            }
        @endphp
        <div class="ue-etudiant">
            <div>
                <div class="ue-etudiant-name">{{ $ue->code }} — {{ $ue->libelle }}</div>
                <div class="ue-etudiant-sub">
                    {{ $moyUE?->credits_acquis ?? 0 }}/{{ $moyUE?->credits_ue ?? $ue->credits_total ?? '?' }} crédits
                    @if($moyUE?->moyenne_ue) · Moy: {{ number_format($moyUE->moyenne_ue,2) }}/20 @endif
                </div>
            </div>
            <span class="{{ $statutClass }}">{{ $statutBadge }}</span>
        </div>
        @endforeach
        @endforeach
    </div>

    <div class="card-white">
        <div class="card-white-title"><i class="fas fa-list-alt"></i> Mes notes par matière</div>
        @foreach($semestres as $semestre)
        <div class="sem-label">{{ $semestre->libelle }}</div>
        @foreach($semestre->unitesEnseignement as $ue)
        @foreach($ue->matieres->where('actif',1) as $matiere)
        @php
            $mm  = \App\Models\MoyenneMatiere::where('etudiant_id',$etudiant->id)->where('matiere_id',$matiere->id)->where('annee_univ',$anneeEtu)->first();
            $moy = $mm?->moyenne_finale;
            $moyBg='#f1efe8'; $moyColor='#888780';
            if($moy!==null){ if($moy>=10){$moyBg='#eaf3de';$moyColor='#27500a';}elseif($moy>=6){$moyBg='#faeeda';$moyColor='#633806';}else{$moyBg='#fcebeb';$moyColor='#791f1f';} }
        @endphp
        <div class="mat-note-row">
            <div class="mat-note-name">{{ $matiere->libelle }}</div>
            <span class="mat-moy" style="background:{{ $moyBg }};color:{{ $moyColor }};">{{ $moy!==null ? number_format($moy,2) : '—' }}</span>
        </div>
        @endforeach
        @endforeach
        @endforeach
    </div>

</div>
@endif

@if($ra && $ra->publie_etudiant)
<div class="card-white">
    <div class="card-white-title"><i class="fas fa-gavel"></i> Décision du Jury</div>
    @php
        $decisions=['DIPLOME'=>'Diplômé(e)','REPRISE_SOUTENANCE'=>'Reprise Soutenance','REDOUBLE'=>'Redouble','EN_ATTENTE'=>'En attente'];
        $decBg=$ra->decision_jury=='DIPLOME'?'#eaf3de':($ra->decision_jury=='REDOUBLE'?'#fcebeb':'#faeeda');
        $decColor=$ra->decision_jury=='DIPLOME'?'#27500a':($ra->decision_jury=='REDOUBLE'?'#791f1f':'#633806');
    @endphp
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div style="background:{{ $decBg }};color:{{ $decColor }};border-radius:10px;padding:12px 24px;font-size:15px;font-weight:600;">
            {{ $decisions[$ra->decision_jury] ?? '—' }}
        </div>
        @if($ra->mention && $ra->mention!='AUCUNE')
        <div>
            <div style="font-size:11px;color:#6b7280;margin-bottom:3px;">Mention</div>
            <span class="badge-bleu" style="font-size:13px;">{{ $ra->mention }}</span>
        </div>
        @endif
        <div>
            <div style="font-size:11px;color:#6b7280;margin-bottom:3px;">Rang annuel</div>
            <span style="font-size:15px;font-weight:600;color:#1e2a3a;">{{ $ra->rang_annuel ?? 'Non classé' }}</span>
        </div>
        <div class="ms-auto">
            <a href="{{ route('etudiant.bulletins') }}" class="btn-primary-inptic">
                <i class="fas fa-file-alt"></i> Voir mes bulletins
            </a>
        </div>
    </div>
</div>
@else
<div class="card-white text-center" style="padding:32px;">
    <div style="font-size:13px;color:#6b7280;">
        <i class="fas fa-clock me-2" style="color:#fac775;"></i>
        Les résultats du jury ne sont pas encore disponibles.
    </div>
</div>
@endif

@endif {{-- fin Étudiant --}}

@endsection