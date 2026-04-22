@extends('layouts.app')
@section('title', 'Import/Export')
@section('page_title', 'Import / Export')
@section('page_sub', 'Année ' . $annee . ' — ' . $etudiants . ' étudiant(s)')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-file-excel me-2" style="color:#2e7df7;"></i>Import / Export</h1>
    <p>Année <strong>{{ $annee }}</strong> — <strong>{{ $etudiants }}</strong> étudiant(s)</p>
</div>

<div class="row g-4">

    <!-- Import -->
    <div class="col-md-6">
        <div class="card-white h-100">
            <div class="card-white-title">
                <i class="fas fa-file-upload"></i>
                Importer des notes depuis Excel
            </div>

            <div style="background:#e6f1fb; border:1px solid #b5d4f4; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:12px; color:#0c447c;">
                <strong>Format attendu du fichier Excel :</strong><br>
                Le fichier doit avoir une ligne d'en-tête avec les colonnes suivantes :
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-inptic table-sm">
                    <thead>
                        <tr>
                            <th>matricule</th>
                            <th>matiere</th>
                            <th>cc</th>
                            <th>examen</th>
                            <th>rattrapage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ASUR-001</td>
                            <td>VIRT ou Virtualisation</td>
                            <td>12</td><td>14</td><td>—</td>
                        </tr>
                        <tr>
                            <td>ASUR-002</td>
                            <td>BDD-SQL</td>
                            <td>10</td><td>8</td><td>11</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('import.notes') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label-inptic">
                        Fichier Excel <span style="color:#e24b4a;">*</span>
                    </label>
                    <input type="file" name="fichier"
                           class="form-control-inptic"
                           accept=".xlsx,.xls,.csv" required>
                    <small style="color:#6b7280; font-size:11px; margin-top:4px; display:block;">
                        Formats acceptés : .xlsx, .xls, .csv (max 5 Mo)
                    </small>
                </div>
                <button type="submit" class="btn-primary-inptic w-100" style="justify-content:center;">
                    <i class="fas fa-upload"></i> Importer les notes
                </button>
            </form>
        </div>
    </div>

    <!-- Export -->
    <div class="col-md-6">
        <div class="card-white h-100">
            <div class="card-white-title">
                <i class="fas fa-file-download"></i>
                Exporter le relevé de notes
            </div>

            <p style="font-size:13px; color:#6b7280; margin-bottom:20px;">
                Téléchargez un fichier Excel complet contenant toutes les notes,
                moyennes, crédits et décisions de jury pour tous les étudiants
                de l'année <strong>{{ $annee }}</strong>.
            </p>

            <div style="background:#f8f9ff; border:1px solid #e5e7eb; border-radius:8px; padding:14px 16px; margin-bottom:20px;">
                <div style="font-size:12px; font-weight:600; color:#1e2a3a; margin-bottom:8px;">
                    Le fichier exporté contiendra :
                </div>
                <div style="font-size:12px; color:#6b7280; display:flex; flex-direction:column; gap:5px;">
                    <span><i class="fas fa-check" style="color:#2e7df7; margin-right:6px;"></i>Notes CC, Examen et Rattrapage par matière</span>
                    <span><i class="fas fa-check" style="color:#2e7df7; margin-right:6px;"></i>Moyennes calculées par matière</span>
                    <span><i class="fas fa-check" style="color:#2e7df7; margin-right:6px;"></i>Moyennes et crédits S5 et S6</span>
                    <span><i class="fas fa-check" style="color:#2e7df7; margin-right:6px;"></i>Moyenne annuelle, décision et mention</span>
                </div>
            </div>

            <a href="{{ route('export.releve') }}"
               class="btn-primary-inptic w-100"
               style="justify-content:center; background:#1b5e20;">
                <i class="fas fa-download"></i>
                Télécharger le relevé complet (.xlsx)
            </a>
        </div>
    </div>
</div>

@endsection