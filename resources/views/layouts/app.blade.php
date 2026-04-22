<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion Bulletins') — INPTIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f4f5f7; display:flex; min-height:100vh; }

        /* ── SIDEBAR ── */
        .sidebar { width:240px; background:#1e2a3a; display:flex; flex-direction:column; position:fixed; top:0; left:0; bottom:0; z-index:1000; transition:width 0.3s ease; overflow:hidden; }
        .sidebar.collapsed { width:64px; }

        .sb-logo { padding:16px; display:flex; align-items:center; gap:10px; border-bottom:1px solid rgba(255,255,255,0.08); flex-shrink:0; min-height:68px; }
        .sb-logo-icon { width:38px; height:38px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; }
        .sb-logo-icon img { width:34px; height:34px; object-fit:contain; }
        .sb-logo-text { color:#fff; font-size:13px; font-weight:600; line-height:1.3; white-space:nowrap; overflow:hidden; transition:opacity 0.2s, width 0.3s; }
        .sb-logo-text span { color:#7eb3ff; font-size:11px; font-weight:400; display:block; }
        .sidebar.collapsed .sb-logo-text { opacity:0; width:0; }

        .sb-toggle { padding:8px 14px; display:flex; justify-content:flex-end; border-bottom:1px solid rgba(255,255,255,0.06); }
        .sidebar.collapsed .sb-toggle { justify-content:center; }
        .sb-toggle button { background:rgba(255,255,255,0.08); border:none; color:#aab8c8; cursor:pointer; border-radius:6px; padding:5px 9px; font-size:13px; transition:background 0.2s; }
        .sb-toggle button:hover { background:rgba(255,255,255,0.15); color:#fff; }

        .sb-nav { flex:1; overflow-y:auto; overflow-x:hidden; padding:8px 0; }
        .sb-nav::-webkit-scrollbar { width:3px; }
        .sb-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.1); border-radius:3px; }

        .sb-section-label { padding:10px 18px 4px; font-size:10px; color:#5a7a9f; text-transform:uppercase; letter-spacing:0.8px; white-space:nowrap; transition:opacity 0.2s; }
        .sidebar.collapsed .sb-section-label { opacity:0; height:0; padding:0; }

        .sb-item { display:flex; align-items:center; gap:12px; padding:10px 18px; color:#b0c4de; font-size:13px; cursor:pointer; transition:all 0.15s; white-space:nowrap; border-left:3px solid transparent; text-decoration:none; }
        .sb-item:hover { background:rgba(255,255,255,0.06); color:#fff; text-decoration:none; }
        .sb-item.active { background:rgba(46,125,247,0.15); color:#7eb3ff; border-left-color:#2e7df7; }
        .sb-item i { width:16px; text-align:center; flex-shrink:0; font-size:14px; }
        .sb-item-text { overflow:hidden; transition:opacity 0.2s, width 0.3s; white-space:nowrap; }
        .sidebar.collapsed .sb-item-text { opacity:0; width:0; }
        .sidebar.collapsed .sb-item { padding:10px; justify-content:center; border-left:none; border-radius:8px; margin:2px 8px; }
        .sidebar.collapsed .sb-item.active { background:rgba(46,125,247,0.2); }

        .sb-submenu { background:rgba(0,0,0,0.15); display:none; }
        .sb-submenu .sb-item { padding-left:38px; font-size:12px; border-left:none; color:#8aaac8; }
        .sb-submenu .sb-item:hover { color:#fff; }
        .sb-submenu .sb-item.active { color:#7eb3ff; background:rgba(46,125,247,0.1); }
        .sidebar.collapsed .sb-submenu { display:none !important; }

        .sb-footer { padding:12px; border-top:1px solid rgba(255,255,255,0.08); flex-shrink:0; }
        .sb-user { display:flex; align-items:center; gap:10px; padding:8px; border-radius:8px; }
        .sb-avatar { width:32px; height:32px; border-radius:50%; background:#2e7df7; display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; font-weight:600; flex-shrink:0; }
        .sb-user-info { overflow:hidden; transition:opacity 0.2s; }
        .sb-user-name { color:#fff; font-size:12px; font-weight:500; white-space:nowrap; }
        .sb-user-role { color:#5a7a9f; font-size:10px; white-space:nowrap; }
        .sidebar.collapsed .sb-user-info { opacity:0; width:0; }

        /* ── MAIN ── */
        .main-wrapper { margin-left:240px; flex:1; display:flex; flex-direction:column; min-height:100vh; transition:margin-left 0.3s ease; }
        .main-wrapper.expanded { margin-left:64px; }

        /* ── TOPBAR ── */
        .topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:0 24px; height:56px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:999; flex-shrink:0; }
        .topbar-title { font-size:14px; font-weight:600; color:#1e2a3a; }
        .topbar-sub { font-size:11px; color:#6b7280; margin-top:1px; }
        .topbar-right { display:flex; align-items:center; gap:10px; }

        .tb-year-badge { background:#f4f5f7; border:1px solid #e5e7eb; border-radius:20px; padding:4px 12px; font-size:11px; color:#374151; white-space:nowrap; }

        .tb-user-dropdown { display:flex; align-items:center; gap:8px; background:#f4f5f7; border:1px solid #e5e7eb; border-radius:20px; padding:4px 12px 4px 4px; cursor:pointer; transition:background 0.15s; user-select:none; }
        .tb-user-dropdown:hover { background:#e9eaec; }
        .tb-avatar { width:28px; height:28px; border-radius:50%; background:#2e7df7; color:#fff; font-size:10px; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .tb-user-name { font-size:12px; color:#374151; font-weight:500; }
        .tb-chevron { font-size:9px; color:#6b7280; transition:transform 0.2s; }

        .dropdown-menu-custom { position:absolute; right:0; top:calc(100% + 6px); background:#fff; border:1px solid #e5e7eb; border-radius:10px; min-width:210px; box-shadow:0 4px 20px rgba(0,0,0,0.08); z-index:9999; overflow:hidden; display:none; }
        .dropdown-menu-custom.open { display:block; }
        .dropdown-user-info { padding:12px 16px; border-bottom:1px solid #f0f0f0; background:#f8f9ff; }
        .dropdown-user-info .name { font-weight:600; color:#1e2a3a; font-size:13px; }
        .dropdown-user-info .role { font-size:11px; color:#6b7280; margin-top:2px; }
        .dropdown-logout { display:flex; align-items:center; gap:10px; padding:11px 16px; font-size:13px; color:#e24b4a; cursor:pointer; width:100%; border:none; background:none; text-align:left; transition:background 0.15s; }
        .dropdown-logout:hover { background:#fcebeb; }

        /* ── CONTENU ── */
        .page-content { padding:24px; flex:1; }
        .page-header { margin-bottom:20px; }
        .page-header h1 { font-size:20px; font-weight:600; color:#1e2a3a; margin-bottom:4px; }
        .page-header p { font-size:12px; color:#6b7280; margin:0; }

        /* ── COMPOSANTS ── */
        .card-white { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:20px; margin-bottom:16px; }
        .card-white-title { font-size:13px; font-weight:600; color:#1e2a3a; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .card-white-title i { color:#2e7df7; font-size:14px; }

        .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px; display:flex; align-items:center; gap:14px; }
        .stat-icon { width:44px; height:44px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
        .stat-num { font-size:24px; font-weight:600; color:#1e2a3a; line-height:1; }
        .stat-lbl { font-size:11px; color:#6b7280; margin-top:3px; }

        .table-inptic th { background:#1e2a3a; color:#fff; font-size:12px; font-weight:500; padding:10px 14px; border:none; }
        .table-inptic td { font-size:13px; padding:10px 14px; vertical-align:middle; border-color:#f0f0f0; }
        .table-inptic tbody tr:hover { background:#f8f9ff; }

        .badge-vert  { background:#eaf3de; color:#27500a; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; display:inline-block; }
        .badge-rouge { background:#fcebeb; color:#791f1f; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; display:inline-block; }
        .badge-jaune { background:#faeeda; color:#633806; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; display:inline-block; }
        .badge-bleu  { background:#e6f1fb; color:#0c447c; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; display:inline-block; }
        .badge-gris  { background:#f1efe8; color:#444441; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; display:inline-block; }

        .btn-primary-inptic { background:#2e7df7; color:#fff; border:none; border-radius:8px; padding:8px 18px; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background 0.15s; }
        .btn-primary-inptic:hover { background:#1a6de0; color:#fff; text-decoration:none; }
        .btn-secondary-inptic { background:#f4f5f7; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:8px 18px; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background 0.15s; }
        .btn-secondary-inptic:hover { background:#e9eaec; color:#1e2a3a; text-decoration:none; }
        .btn-danger-inptic { background:#fcebeb; color:#791f1f; border:1px solid #f7c1c1; border-radius:8px; padding:8px 18px; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background 0.15s; }
        .btn-danger-inptic:hover { background:#f09595; color:#501313; text-decoration:none; }

        .tb-btn { width:32px; height:32px; border-radius:7px; background:#f4f5f7; border:1px solid #e5e7eb; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:#374151; text-decoration:none; font-size:12px; transition:background 0.15s; }
        .tb-btn:hover { background:#e9eaec; color:#1e2a3a; }

        .form-control-inptic { border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; font-size:13px; width:100%; color:#1e2a3a; background:#fff; transition:border-color 0.15s, box-shadow 0.15s; outline:none; }
        .form-control-inptic:focus { border-color:#2e7df7; box-shadow:0 0 0 3px rgba(46,125,247,0.1); }
        .form-label-inptic { font-size:12px; font-weight:600; color:#374151; margin-bottom:5px; display:block; }

        .alert-inptic-success { background:#eaf3de; border:1px solid #97c459; color:#27500a; border-radius:8px; padding:10px 16px; font-size:13px; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .alert-inptic-error { background:#fcebeb; border:1px solid #f09595; color:#791f1f; border-radius:8px; padding:10px 16px; font-size:13px; margin-bottom:16px; display:flex; align-items:center; gap:8px; }

        .main-footer { padding:12px 24px; border-top:1px solid #e5e7eb; background:#fff; font-size:11px; color:#9ca3af; text-align:center; flex-shrink:0; }

        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:#f4f5f7; }
        ::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:3px; }

        @media (max-width:768px) {
            .sidebar { width:64px; }
            .sidebar .sb-logo-text, .sidebar .sb-item-text, .sidebar .sb-section-label, .sidebar .sb-user-info { opacity:0; width:0; }
            .sidebar .sb-item { padding:10px; justify-content:center; border-left:none; border-radius:8px; margin:2px 8px; }
            .sidebar .sb-toggle { justify-content:center; }
            .main-wrapper { margin-left:64px; }
            .tb-year-badge { display:none; }
        }
    </style>
    @yield('head')
</head>
<body>

<!-- ═══════════ SIDEBAR ═══════════ -->
<div class="sidebar" id="mainSidebar">

    <div class="sb-logo">
        <div class="sb-logo-icon">
            @if(file_exists(public_path('images/logo_inptic.png')))
                <img src="{{ asset('images/logo_inptic.png') }}" alt="INPTIC">
            @else
                <i class="fas fa-graduation-cap" style="color:#1e2a3a; font-size:18px;"></i>
            @endif
        </div>
        <div class="sb-logo-text">
            INPTIC
            <span>Gestion LP ASUR</span>
        </div>
    </div>

    <div class="sb-toggle">
        <button onclick="toggleSidebar()" title="Réduire / Agrandir">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="sb-nav">
        @php
            $anneeNav  = config('app.annee_courante', '2025/2026');
            $roleUser  = Auth::user()->role;
            $mesMatNav = collect();
            if ($roleUser === 'ENSEIGNANT') {
                $mesMatNav = \App\Models\EnseignantMatiere::where('utilisateur_id', Auth::id())
                                ->where('annee_univ', $anneeNav)
                                ->with('matiere')
                                ->get();
            }
        @endphp

        <div class="sb-section-label">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span class="sb-item-text">Tableau de bord</span>
        </a>

        @if($roleUser === 'ADMIN' || $roleUser === 'SECRETARIAT')
        <a href="{{ route('etudiants.index') }}"
           class="sb-item {{ request()->routeIs('etudiants.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span class="sb-item-text">Étudiants</span>
        </a>
        <a href="{{ route('notes.index') }}"
           class="sb-item {{ request()->routeIs('notes.*') ? 'active' : '' }}">
            <i class="fas fa-pen"></i>
            <span class="sb-item-text">Notes</span>
        </a>
        <a href="{{ route('absences.index') }}"
           class="sb-item {{ request()->routeIs('absences.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-times"></i>
            <span class="sb-item-text">Absences</span>
        </a>
        @endif

        @if($roleUser === 'ENSEIGNANT')
        <a href="{{ route('enseignant.informations') }}"
           class="sb-item {{ request()->routeIs('enseignant.informations') ? 'active' : '' }}">
            <i class="fas fa-id-card"></i>
            <span class="sb-item-text">Mes informations</span>
        </a>
        @endif

        @if($roleUser === 'ETUDIANT')
        <a href="{{ route('etudiant.informations') }}"
           class="sb-item {{ request()->routeIs('etudiant.informations') ? 'active' : '' }}">
            <i class="fas fa-id-card"></i>
            <span class="sb-item-text">Mes informations</span>
        </a>
        <a href="{{ route('etudiant.notes') }}"
           class="sb-item {{ request()->routeIs('etudiant.notes') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            <span class="sb-item-text">Mes notes</span>
        </a>
        @endif

        <div class="sb-section-label">Résultats</div>

        @if($roleUser === 'ADMIN' || $roleUser === 'SECRETARIAT')
        <a href="{{ route('bulletins.index') }}"
           class="sb-item {{ request()->routeIs('bulletins.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span class="sb-item-text">Bulletins PDF</span>
        </a>
        <a href="{{ route('jury.index') }}"
           class="sb-item {{ request()->routeIs('jury.*') ? 'active' : '' }}">
            <i class="fas fa-gavel"></i>
            <span class="sb-item-text">Décisions Jury</span>
        </a>
        <a href="{{ route('import.index') }}"
           class="sb-item {{ request()->routeIs('import.*') || request()->routeIs('export.*') ? 'active' : '' }}">
            <i class="fas fa-file-excel"></i>
            <span class="sb-item-text">Import / Export</span>
        </a>
        @endif

        @if($roleUser === 'ETUDIANT')
        <a href="{{ route('etudiant.bulletins') }}"
           class="sb-item {{ request()->routeIs('etudiant.bulletins') || request()->routeIs('etudiant.bulletin') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span class="sb-item-text">Mes bulletins</span>
        </a>
        @endif

        @if($roleUser === 'ENSEIGNANT')
        <div class="sb-section-label">Mes matières</div>
        @if($mesMatNav->count() > 0)

            <div class="sb-item {{ request()->routeIs('enseignant.saisir') ? 'active' : '' }}"
                 onclick="toggleSubMenu('submenu-saisir', 'chevron-saisir')">
                <i class="fas fa-pen"></i>
                <span class="sb-item-text" style="display:flex; align-items:center; justify-content:space-between; width:100%;">
                    Saisir les notes
                    <i class="fas fa-chevron-down" id="chevron-saisir" style="font-size:9px; transition:transform 0.2s; margin-left:8px;"></i>
                </span>
            </div>
            <div id="submenu-saisir" class="sb-submenu">
                @foreach($mesMatNav as $em)
                <a href="{{ route('enseignant.saisir', $em->matiere_id) }}"
                   class="sb-item {{ request()->routeIs('enseignant.saisir') && request()->route('matiere') == $em->matiere_id ? 'active' : '' }}">
                    <i class="fas fa-circle" style="font-size:5px;"></i>
                    <span class="sb-item-text">{{ $em->matiere->libelle }}</span>
                </a>
                @endforeach
            </div>

            <div class="sb-item {{ request()->routeIs('enseignant.releve') ? 'active' : '' }}"
                 onclick="toggleSubMenu('submenu-releve', 'chevron-releve')">
                <i class="fas fa-list-alt"></i>
                <span class="sb-item-text" style="display:flex; align-items:center; justify-content:space-between; width:100%;">
                    Relevé de notes
                    <i class="fas fa-chevron-down" id="chevron-releve" style="font-size:9px; transition:transform 0.2s; margin-left:8px;"></i>
                </span>
            </div>
            <div id="submenu-releve" class="sb-submenu">
                @foreach($mesMatNav as $em)
                <a href="{{ route('enseignant.releve', $em->matiere_id) }}"
                   class="sb-item {{ request()->routeIs('enseignant.releve') && request()->route('matiere') == $em->matiere_id ? 'active' : '' }}">
                    <i class="fas fa-circle" style="font-size:5px;"></i>
                    <span class="sb-item-text">{{ $em->matiere->libelle }}</span>
                </a>
                @endforeach
            </div>

        @else
        <div class="sb-item" style="opacity:0.5; cursor:default; pointer-events:none;">
            <i class="fas fa-exclamation-circle"></i>
            <span class="sb-item-text">Aucune matière assignée</span>
        </div>
        @endif
        @endif

        @if($roleUser === 'ADMIN')
        <div class="sb-section-label">Administration</div>
        <a href="{{ route('utilisateurs.index') }}"
           class="sb-item {{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i>
            <span class="sb-item-text">Utilisateurs</span>
        </a>
        <a href="{{ route('journal.index') }}"
           class="sb-item {{ request()->routeIs('journal.*') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span class="sb-item-text">Journal d'activité</span>
        </a>
        <a href="{{ route('parametres.index') }}"
           class="sb-item {{ request()->routeIs('parametres.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span class="sb-item-text">Paramètres</span>
        </a>
        @endif

    </div>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(Auth::user()->nom_affichage, 0, 2)) }}</div>
            <div class="sb-user-info">
                <div class="sb-user-name">{{ Auth::user()->nom_affichage }}</div>
                <div class="sb-user-role">{{ Auth::user()->role }} · INPTIC</div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ MAIN ═══════════ -->
<div class="main-wrapper" id="mainWrapper">

    <!-- Topbar -->
    <div class="topbar">
        <div>
            <div class="topbar-title">@yield('page_title', 'Tableau de bord')</div>
            <div class="topbar-sub">@yield('page_sub', 'INPTIC — LP ASUR')</div>
        </div>
        <div class="topbar-right">
            <span class="tb-year-badge">
                <i class="fas fa-calendar-alt" style="margin-right:5px;"></i>
                {{ config('app.annee_courante', '2025/2026') }}
            </span>

           {{-- Cloche notifications (étudiant + enseignant) --}}
@if(Auth::user()->isEtudiant() || Auth::user()->isEnseignant())
@php $nbNotifs = \App\Http\Controllers\NotificationController::getNonLues(); @endphp
<a href="{{ route('notifications.index') }}"
   style="width:38px; height:38px; background:#f4f5f7; border:1px solid #e5e7eb; border-radius:8px; display:flex; align-items:center; justify-content:center; position:relative; text-decoration:none; transition:background 0.15s;"
   onmouseover="this.style.background='#e9eaec'" onmouseout="this.style.background='#f4f5f7'">
    <i class="fas fa-bell" style="color:#374151; font-size:15px;"></i>
    @if($nbNotifs > 0)
    <span style="position:absolute; top:-5px; right:-5px; background:#e24b4a; color:#fff; border-radius:50%; width:17px; height:17px; font-size:9px; font-weight:700; display:flex; align-items:center; justify-content:center; border:2px solid #fff;">
        {{ $nbNotifs > 9 ? '9+' : $nbNotifs }}
    </span>
    @endif
</a>
@endif

            {{-- Dropdown utilisateur --}}
            <div style="position:relative;">
                <div class="tb-user-dropdown" id="userDropdownToggle" onclick="toggleUserDropdown()">
                    <div class="tb-avatar">{{ strtoupper(substr(Auth::user()->nom_affichage, 0, 2)) }}</div>
                    <span class="tb-user-name">{{ Auth::user()->nom_affichage }}</span>
                    <i class="fas fa-chevron-down tb-chevron" id="dropdownChevron"></i>
                </div>
                <div class="dropdown-menu-custom" id="userDropdownMenu">
                    <div class="dropdown-user-info">
                        <div class="name">{{ Auth::user()->nom_affichage }}</div>
                        <div class="role">{{ Auth::user()->role }} · INPTIC</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-logout">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu -->
    <div class="page-content">
        @if(session('success'))
        <div class="alert-inptic-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-inptic-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif
        @yield('content')
    </div>

    <div class="main-footer">
        &copy; {{ date('Y') }} INPTIC — Gestion Bulletins LP ASUR · Tous droits réservés
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        var sidebar   = document.getElementById('mainSidebar');
        var wrapper   = document.getElementById('mainWrapper');
        var collapsed = sidebar.classList.toggle('collapsed');
        wrapper.classList.toggle('expanded', collapsed);
        localStorage.setItem('sidebarCollapsed', collapsed);
    }

    function toggleUserDropdown() {
        var menu    = document.getElementById('userDropdownMenu');
        var toggle  = document.getElementById('userDropdownToggle');
        var chevron = document.getElementById('dropdownChevron');
        var isOpen  = menu.classList.toggle('open');
        toggle.classList.toggle('show', isOpen);
        chevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function toggleNotifDropdown() {
        var dd = document.getElementById('notifDropdown');
        if (dd) dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
    }

    function marquerLu(id) {
        fetch('/notifications/' + id + '/lu', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
    }

    document.addEventListener('click', function(e) {
        // Fermer dropdown user
        var toggle = document.getElementById('userDropdownToggle');
        var menu   = document.getElementById('userDropdownMenu');
        if (toggle && menu && !toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('open');
            toggle.classList.remove('show');
            document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
        }
        // Fermer dropdown notifs
        var notifToggle = document.getElementById('notifToggle');
        var notifDd     = document.getElementById('notifDropdown');
        if (notifToggle && notifDd && !notifToggle.contains(e.target) && !notifDd.contains(e.target)) {
            notifDd.style.display = 'none';
        }
    });

    function toggleSubMenu(menuId, chevronId) {
        var menu    = document.getElementById(menuId);
        var chevron = document.getElementById(chevronId);
        if (!menu) return;
        var isOpen = menu.style.display === 'block';
        menu.style.display = isOpen ? 'none' : 'block';
        if (chevron) chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        localStorage.setItem(menuId, !isOpen);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.getElementById('mainSidebar').classList.add('collapsed');
            document.getElementById('mainWrapper').classList.add('expanded');
        }
        ['submenu-saisir', 'submenu-releve'].forEach(function(id) {
            var menu    = document.getElementById(id);
            var chevron = document.getElementById('chevron-' + id.replace('submenu-', ''));
            if (menu && localStorage.getItem(id) === 'true') {
                menu.style.display = 'block';
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
        });
        var path = window.location.pathname;
        if (path.includes('/enseignant/saisir')) {
            var m = document.getElementById('submenu-saisir');
            var c = document.getElementById('chevron-saisir');
            if (m) m.style.display = 'block';
            if (c) c.style.transform = 'rotate(180deg)';
        }
        if (path.includes('/enseignant/releve')) {
            var m = document.getElementById('submenu-releve');
            var c = document.getElementById('chevron-releve');
            if (m) m.style.display = 'block';
            if (c) c.style.transform = 'rotate(180deg)';
        }
    });
</script>
@yield('scripts')
</body>
</html>