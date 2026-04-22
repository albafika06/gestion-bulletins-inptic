<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:9px; color:#000; }

        .header-table { width:100%; margin-bottom:8px; border-collapse:collapse; }
        .logo-cell { width:130px; text-align:center; vertical-align:middle; padding-right:8px; }
        .logo-cell img { width:70px; height:70px; object-fit:contain; }
        .title-cell { text-align:center; vertical-align:middle; }
        .republic-cell { width:150px; text-align:center; vertical-align:middle; font-size:8px; }

        .etablissement { font-size:8px; font-weight:bold; text-transform:uppercase; line-height:1.4; }
        .direction { font-size:7px; margin-top:3px; color:#555; }
        .bulletin-title { font-size:13px; font-weight:bold; color:#1a237e; margin:4px 0; }
        .annee { font-size:9px; color:#444; }

        .classe-box { border:1px solid #000; padding:4px 8px; margin:5px 0; font-size:9px; background:#f5f5f5; }

        .etudiant-table { width:100%; margin-bottom:6px; border-collapse:collapse; }
        .etudiant-table td { border:1px solid #000; padding:3px 6px; font-size:9px; }
        .etudiant-table .label { background:#e8eaf6; font-weight:bold; width:130px; }

        .notes-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
        .notes-table th { background:#1a237e; color:white; padding:4px 3px; font-size:8px; text-align:center; border:1px solid #999; }
        .notes-table td { padding:3px 4px; font-size:8px; border:1px solid #ddd; text-align:center; }
        .notes-table td.libelle { text-align:left; padding-left:5px; }
        .ue-header { background:#e8eaf6; font-weight:bold; }
        .ue-header td { padding:4px 5px; border:1px solid #bbb; font-size:8px; }
        .moyenne-row { background:#f5f5f5; font-weight:bold; }
        .moy-vert  { color:#2e7d32; font-weight:bold; }
        .moy-jaune { color:#e65100; font-weight:bold; }
        .moy-rouge { color:#c62828; font-weight:bold; }

        .stats-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
        .stats-table th { background:#37474f; color:white; padding:4px; font-size:8px; text-align:center; border:1px solid #999; }
        .stats-table td { padding:3px 5px; font-size:8px; border:1px solid #ddd; text-align:center; }
        .stats-table .label { background:#f5f5f5; font-weight:bold; text-align:left; }

        .rang-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
        .rang-table td { border:1px solid #000; padding:3px 6px; font-size:8px; text-align:center; }
        .rang-table .label { background:#e8eaf6; font-weight:bold; text-align:left; }

        .credits-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
        .credits-table th { background:#1a237e; color:white; padding:4px; font-size:8px; text-align:center; border:1px solid #999; }
        .credits-table td { padding:3px 5px; font-size:8px; border:1px solid #ddd; text-align:center; }

        .decision-box { border:2px solid #1a237e; padding:5px 10px; font-weight:bold; font-size:10px; color:#1a237e; margin-bottom:6px; }

        .signature-table { width:100%; margin-top:12px; }
        .signature-table td { text-align:center; font-size:8px; padding:3px; }

        .footer-note { font-size:7px; font-style:italic; text-align:center; margin-top:8px; border-top:1px solid #ccc; padding-top:4px; color:#555; }

        .section-title { font-size:9px; font-weight:bold; color:#1a237e; background:#e8eaf6; padding:3px 6px; margin-bottom:4px; border-left:3px solid #1a237e; }
    </style>
</head>
<body>

    {{-- EN-TÊTE --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(file_exists(public_path('images/logo_inptic.png')))
                    <img src="{{ public_path('images/logo_inptic.png') }}" alt="INPTIC">
                @endif
                <div class="etablissement">INPTIC</div>
                <div class="direction">Direction des Études et de la Pédagogie</div>
            </td>
            <td class="title-cell">
                <div class="bulletin-title">Bulletin de Notes — Semestre 5</div>
                <div class="annee">Année universitaire : {{ $annee }}</div>
            </td>
            <td class="republic-cell">
                <strong>RÉPUBLIQUE GABONAISE</strong><br>
                ————————————<br>
                Union · Travail · Justice<br>
                ————————————
            </td>
        </tr>
    </table>

    {{-- CLASSE --}}
    <div class="classe-box">
        <strong>Formation :</strong> Licence Professionnelle Réseaux et Télécommunications —
        Option Administration et Sécurité des Réseaux (ASUR)
    </div>

    {{-- IDENTITÉ ÉTUDIANT --}}
    <table class="etudiant-table">
        <tr>
            <td class="label">Nom(s) et Prénom(s)</td>
            <td><strong>{{ $etudiant->nom }} {{ $etudiant->prenom }}</strong></td>
            <td class="label">Date de naissance</td>
            <td>{{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : '—' }}</td>
            <td class="label">Lieu de naissance</td>
            <td>{{ $etudiant->lieu_naissance ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Matricule</td>
            <td><strong>{{ $etudiant->matricule }}</strong></td>
            <td class="label">Baccalauréat</td>
            <td>{{ $etudiant->type_bac ?? '—' }}</td>
            <td class="label">Établissement origine</td>
            <td>{{ $etudiant->etablissement_origine ?? '—' }}</td>
        </tr>
    </table>

    {{-- TABLEAU DES NOTES --}}
    <div class="section-title">Détail des notes</div>
    <table class="notes-table">
        <thead>
            <tr>
                <th style="width:32%; text-align:left; padding-left:5px;">Matière</th>
                <th style="width:6%;">Coeff</th>
                <th style="width:6%;">Crédits</th>
                <th style="width:10%;">CC (40%)</th>
                <th style="width:10%;">Examen (60%)</th>
                <th style="width:10%;">Rattrapage</th>
                <th style="width:8%;">Moy. Étud.</th>
                <th style="width:8%;">Moy. Classe</th>
                <th style="width:5%;">Min</th>
                <th style="width:5%;">Max</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semestre->unitesEnseignement as $ue)
            @php $moyUE = $moyennesUEs[$ue->id] ?? null; @endphp

            <tr class="ue-header">
                <td colspan="10">{{ $ue->code }} : {{ strtoupper($ue->libelle) }}</td>
            </tr>

            @foreach($ue->matieres as $matiere)
            @php
                $mm      = $moyennesMatieres[$matiere->id] ?? null;
                $stat    = $stats[$matiere->id] ?? null;
                $moy     = $mm ? $mm->moyenne_finale : null;
                $noteCC  = $mm ? $mm->note_cc : null;
                $noteEx  = $mm ? $mm->note_examen : null;
                $noteRat = $mm ? $mm->note_rattrapage : null;
                $couleur = '';
                if ($moy !== null) {
                    if ($moy >= 10)    $couleur = 'moy-vert';
                    elseif ($moy >= 6) $couleur = 'moy-jaune';
                    else               $couleur = 'moy-rouge';
                }
            @endphp
            <tr>
                <td class="libelle">
                    {{ $matiere->libelle }}
                    @if($mm && $mm->rattrapage_utilise)
                        <em style="color:#e65100;"> (Ratt.)</em>
                    @endif
                </td>
                <td>{{ $matiere->coefficient }}</td>
                <td>{{ $matiere->credits }}</td>
                <td>{{ $noteCC !== null ? number_format($noteCC, 2) : '—' }}</td>
                <td>{{ $noteEx !== null ? number_format($noteEx, 2) : '—' }}</td>
                <td>{{ $noteRat !== null ? number_format($noteRat, 2) : '—' }}</td>
                <td class="{{ $couleur }}">
                    {{ $moy !== null ? number_format($moy, 2) : '—' }}
                </td>
                <td>{{ $stat ? number_format($stat->moyenne_classe, 2) : '—' }}</td>
                <td>{{ $stat ? number_format($stat->note_min, 2) : '—' }}</td>
                <td>{{ $stat ? number_format($stat->note_max, 2) : '—' }}</td>
            </tr>
            @endforeach

            {{-- Ligne moyenne UE --}}
            <tr class="moyenne-row">
                <td class="libelle" style="padding-left:10px;">
                    <strong>Moyenne {{ $ue->code }}</strong>
                </td>
                <td>
                    <strong>{{ $ue->matieres->sum('coefficient') }}</strong>
                </td>
                <td>
                    <strong>{{ $moyUE ? $moyUE->credits_ue : '—' }}</strong>
                </td>
                <td colspan="3"></td>
                <td>
                    @if($moyUE && $moyUE->moyenne_ue !== null)
                        @php
                            $c = $moyUE->moyenne_ue >= 10 ? 'moy-vert' : ($moyUE->moyenne_ue >= 6 ? 'moy-jaune' : 'moy-rouge');
                        @endphp
                        <strong class="{{ $c }}">{{ number_format($moyUE->moyenne_ue, 2) }}</strong>
                    @else —
                    @endif
                </td>
                <td colspan="3"></td>
            </tr>
            @endforeach

            {{-- Absences --}}
            @php $totalAbsences = $absences->sum('heures'); @endphp
            @if($totalAbsences > 0)
            <tr>
                <td class="libelle" colspan="3" style="color:#c62828;">
                    <em>Pénalités absences ({{ $totalAbsences }}h × 0.01 pt/h)</em>
                </td>
                <td colspan="3" style="color:#c62828; text-align:center;">
                    -{{ number_format($totalAbsences * 0.01, 2) }} pt
                </td>
                <td colspan="4"></td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- STATISTIQUES DU SEMESTRE --}}
    <div class="section-title">Statistiques du Semestre 5 — Promotion {{ $annee }}</div>
    <table class="stats-table">
        <thead>
            <tr>
                <th style="width:30%; text-align:left; padding-left:5px;">Indicateur</th>
                <th>Étudiant</th>
                <th>Moyenne Classe</th>
                <th>Note Min Classe</th>
                <th>Note Max Classe</th>
                <th>Rang</th>
                <th>Effectif</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Semestre 5</td>
                <td>
                    @if($resultat && $resultat->moyenne_semestre !== null)
                        @php
                            $ms = $resultat->moyenne_semestre;
                            $c  = $ms >= 10 ? 'moy-vert' : ($ms >= 6 ? 'moy-jaune' : 'moy-rouge');
                        @endphp
                        <strong class="{{ $c }}">{{ number_format($ms, 2) }}</strong>
                    @else —
                    @endif
                </td>
                <td>
                    {{ $resultat && $resultat->stat_moyenne_classe
                        ? number_format($resultat->stat_moyenne_classe, 2)
                        : '—' }}
                </td>
                <td>
                    {{ $resultat && $resultat->stat_min
                        ? number_format($resultat->stat_min, 2)
                        : '—' }}
                </td>
                <td>
                    {{ $resultat && $resultat->stat_max
                        ? number_format($resultat->stat_max, 2)
                        : '—' }}
                </td>
                <td>
                    <strong>
                        {{ $resultat && $resultat->rang
                            ? $resultat->rang . 'e / ' . $nbEtudiants
                            : 'N/A' }}
                    </strong>
                </td>
                <td>{{ $nbEtudiants }}</td>
            </tr>
        </tbody>
    </table>

    {{-- MENTION --}}
    <table class="rang-table">
        <tr>
            <td class="label" style="width:20%;">Mention</td>
            <td style="width:30%;">
                @if($resultat && $resultat->moyenne_semestre >= 10)
                    @php
                        $ms = $resultat->moyenne_semestre;
                        $mention = $ms >= 16 ? 'Très Bien' : ($ms >= 14 ? 'Bien' : ($ms >= 12 ? 'Assez Bien' : 'Passable'));
                    @endphp
                    <strong>{{ $mention }}</strong>
                @else
                    <span style="color:#c62828;">—</span>
                @endif
            </td>
            <td class="label" style="width:20%;">Crédits validés S5</td>
            <td style="width:30%;">
                <strong>{{ $resultat ? $resultat->credits_acquis : 0 }} / 30</strong>
                @if($resultat)
                    — <em>{{ $resultat->valide ? 'Semestre validé' : 'Semestre non validé' }}</em>
                @endif
            </td>
        </tr>
    </table>

    {{-- VALIDATION DES UE --}}
    <div class="section-title">État de validation des Unités d'Enseignement</div>
    <table class="credits-table">
        <thead>
            <tr>
                <th style="width:30%;">UE</th>
                <th style="width:15%;">Moyenne UE</th>
                <th style="width:15%;">Crédits acquis</th>
                <th style="width:15%;">Crédits totaux</th>
                <th style="width:25%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semestre->unitesEnseignement as $ue)
            @php $moyUE = $moyennesUEs[$ue->id] ?? null; @endphp
            <tr>
                <td style="text-align:left; padding-left:5px;">
                    {{ $ue->code }} — {{ $ue->libelle }}
                </td>
                <td>
                    @if($moyUE && $moyUE->moyenne_ue !== null)
                        @php $c = $moyUE->moyenne_ue >= 10 ? 'moy-vert' : ($moyUE->moyenne_ue >= 6 ? 'moy-jaune' : 'moy-rouge'); @endphp
                        <span class="{{ $c }}">{{ number_format($moyUE->moyenne_ue, 2) }}</span>
                    @else —
                    @endif
                </td>
                <td>{{ $moyUE ? $moyUE->credits_acquis : 0 }}</td>
                <td>{{ $moyUE ? $moyUE->credits_ue : '—' }}</td>
                <td>
                    @if($moyUE)
                        @if($moyUE->statut == 'ACQUISE')
                            <span class="moy-vert">✓ Acquise</span>
                        @elseif($moyUE->statut == 'COMPENSEE')
                            <span class="moy-jaune">~ Acquise par compensation</span>
                        @else
                            <span class="moy-rouge">✗ Non acquise</span>
                        @endif
                    @else —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- DÉCISION --}}
    <div class="decision-box">
        Décision du Jury — Semestre 5 :
        @if($resultat)
            @if($resultat->valide)
                <span class="moy-vert">Semestre 5 VALIDÉ</span>
            @else
                <span class="moy-rouge">Semestre 5 NON VALIDÉ</span>
            @endif
        @else
            En attente de délibération
        @endif
    </div>

    {{-- SIGNATURE --}}
    <table class="signature-table">
        <tr>
            <td style="width:50%; text-align:left;">
                Fait à Libreville, le {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
            </td>
            <td style="width:50%; text-align:center;">
                LE DIRECTEUR DES ÉTUDES ET DE LA PÉDAGOGIE
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-top:40px; text-align:center;">
                <strong>{{ config('app.directeur_dep', '') }}</strong>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Il ne sera délivré qu'un seul et unique exemplaire de ce bulletin de notes.
        L'étudiant est prié d'en faire plusieurs copies légalisées avant tout dépôt administratif.
    </div>

</body>
</html>