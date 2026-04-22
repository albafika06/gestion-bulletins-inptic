@extends('layouts.app')
@section('title', 'Mes Bulletins')
@section('page_title', 'Mes Bulletins')
@section('page_sub', 'Année ' . $annee)

@section('content')

@php
    // Récupérer $estPublie directement dans la vue si non passé par le controller
    if (!isset($estPublie)) {
        $anneeCheck  = config('app.annee_courante', '2025/2026');
        $ra          = \App\Models\ResultatAnnuel::where('etudiant_id', $etudiant->id)
                            ->where('annee_univ', $anneeCheck)
                            ->first();
        $estPublie   = $ra && $ra->publie_etudiant;
    }
@endphp

<div class="page-header">
    <h1><i class="fas fa-file-alt me-2" style="color:#2e7df7;"></i>Mes Bulletins</h1>
    <p>{{ $etudiant->nom }} {{ $etudiant->prenom }} · <strong>{{ $etudiant->matricule }}</strong></p>
</div>

{{-- ── SECTION BULLETINS PDF ── --}}
@if(!$estPublie)
<div class="card-white text-center" style="padding:56px 32px; margin-bottom:24px;">
    <div style="width:72px; height:72px; background:#faeeda; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
        <i class="fas fa-clock" style="color:#633806; font-size:28px;"></i>
    </div>
    <div style="font-size:18px; font-weight:600; color:#1e2a3a; margin-bottom:10px;">
        Bulletins non disponibles
    </div>
    <div style="font-size:13px; color:#6b7280; max-width:420px; margin:0 auto; line-height:1.7;">
        Vos bulletins ne sont pas encore disponibles. L'administration publiera
        vos résultats après la délibération du jury. Revenez consulter cette
        page ultérieurement.
    </div>
</div>

@else

<div style="background:#eaf3de; border:1px solid #97c459; border-radius:8px; padding:10px 16px; margin-bottom:24px; font-size:13px; color:#27500a; display:flex; align-items:center; gap:8px;">
    <i class="fas fa-check-circle"></i>
    Vos bulletins sont disponibles. Vous pouvez les télécharger ci-dessous.
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card-white text-center" style="padding:32px 24px;">
            <div style="width:56px; height:56px; background:#fcebeb; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fas fa-file-pdf" style="color:#791f1f; font-size:24px;"></i>
            </div>
            <div style="font-weight:600; color:#1e2a3a; font-size:15px; margin-bottom:6px;">
                Bulletin Semestre 5
            </div>
            <div style="font-size:12px; color:#6b7280; margin-bottom:20px;">
                Résultats complets du Semestre 5
            </div>
            <a href="{{ route('etudiant.bulletin', 's5') }}" target="_blank"
               class="btn-primary-inptic"
               style="justify-content:center; width:100%; background:#c62828;">
                <i class="fas fa-download"></i> Télécharger S5
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-white text-center" style="padding:32px 24px;">
            <div style="width:56px; height:56px; background:#fcebeb; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fas fa-file-pdf" style="color:#791f1f; font-size:24px;"></i>
            </div>
            <div style="font-weight:600; color:#1e2a3a; font-size:15px; margin-bottom:6px;">
                Bulletin Semestre 6
            </div>
            <div style="font-size:12px; color:#6b7280; margin-bottom:20px;">
                Résultats complets du Semestre 6
            </div>
            <a href="{{ route('etudiant.bulletin', 's6') }}" target="_blank"
               class="btn-primary-inptic"
               style="justify-content:center; width:100%; background:#c62828;">
                <i class="fas fa-download"></i> Télécharger S6
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-white text-center" style="padding:32px 24px;">
            <div style="width:56px; height:56px; background:#e6f1fb; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fas fa-graduation-cap" style="color:#0c447c; font-size:24px;"></i>
            </div>
            <div style="font-weight:600; color:#1e2a3a; font-size:15px; margin-bottom:6px;">
                Bulletin Annuel
            </div>
            <div style="font-size:12px; color:#6b7280; margin-bottom:20px;">
                Résultats annuels et décision du jury
            </div>
            <a href="{{ route('etudiant.bulletin', 'annuel') }}" target="_blank"
               class="btn-primary-inptic"
               style="justify-content:center; width:100%;">
                <i class="fas fa-download"></i> Télécharger Annuel
            </a>
        </div>
    </div>
</div>
@endif

{{-- ── SECTION RELEVÉS DE NOTES PAR MATIÈRE ── --}}
@php
    $anneeReleve    = config('app.annee_courante', '2025/2026');
    $relevesPublies = \App\Models\EnseignantMatiere::where('annee_univ', $anneeReleve)
                        ->where('releve_publie', true)
                        ->with('matiere.ue.semestre')
                        ->get();
@endphp

<div class="card-white">
    <div class="card-white-title">
        <i class="fas fa-list-alt"></i>
        Relevés de notes disponibles
        @if($relevesPublies->count() > 0)
        <span style="background:#e6f1fb; color:#0c447c; padding:2px 10px; border-radius:10px; font-size:11px; font-weight:500; margin-left:6px;">
            {{ $relevesPublies->count() }} matière(s)
        </span>
        @endif
    </div>

    @if($relevesPublies->count() > 0)
        <div style="font-size:12px; color:#6b7280; margin-bottom:16px;">
            Ces relevés ont été publiés par vos enseignants. Cliquez sur "Voir" pour consulter votre note et votre position dans la classe.
        </div>

        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($relevesPublies as $em)
            @php
                $maMoyenne = \App\Models\MoyenneMatiere::where('etudiant_id', $etudiant->id)
                                ->where('matiere_id', $em->matiere_id)
                                ->where('annee_univ', $anneeReleve)
                                ->value('moyenne_finale');
                $moyClass = 'badge-gris';
                if ($maMoyenne !== null) {
                    if ($maMoyenne >= 10)    $moyClass = 'badge-vert';
                    elseif ($maMoyenne >= 6) $moyClass = 'badge-jaune';
                    else                     $moyClass = 'badge-rouge';
                }
            @endphp
            <div style="
                display:flex; align-items:center; justify-content:space-between;
                padding:14px 16px; border:1px solid #e5e7eb; border-radius:8px;
                background:#fff; flex-wrap:wrap; gap:10px;
            ">
                {{-- Info matière --}}
                <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:200px;">
                    <div style="width:38px; height:38px; background:#e6f1fb; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-book" style="color:#0c447c; font-size:14px;"></i>
                    </div>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#1e2a3a;">
                            {{ $em->matiere->libelle }}
                        </div>
                        <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                            {{ $em->matiere->ue->code ?? '—' }} ·
                            {{ $em->matiere->ue->semestre->libelle ?? '—' }} ·
                            Coeff : {{ $em->matiere->coefficient }}
                        </div>
                    </div>
                </div>

                {{-- Ma note --}}
                <div style="text-align:center; min-width:80px;">
                    <div style="font-size:10px; color:#6b7280; margin-bottom:4px;">Ma moyenne</div>
                    <span class="{{ $moyClass }}" style="font-size:13px; font-weight:600;">
                        {{ $maMoyenne !== null ? number_format($maMoyenne, 2) : '—' }}
                    </span>
                </div>

                {{-- Action --}}
                <div>
                    <a href="{{ route('etudiant.releve', $em->matiere_id) }}"
                       class="btn-primary-inptic" style="font-size:12px; padding:7px 16px;">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    @else
        <div style="text-align:center; padding:32px; color:#9ca3af;">
            <i class="fas fa-hourglass-half" style="font-size:2rem; display:block; margin-bottom:10px; color:#d1d5db;"></i>
            <div style="font-size:13px; font-weight:500; color:#374151; margin-bottom:4px;">
                Aucun relevé disponible pour le moment
            </div>
            <div style="font-size:12px;">
                Vos enseignants publieront les relevés de notes une fois les notes saisies.
            </div>
        </div>
    @endif
</div>

@endsection