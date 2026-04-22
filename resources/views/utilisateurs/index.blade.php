@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page_title', 'Gestion des Utilisateurs')
@section('page_sub', 'Comptes d\'accès à l\'application')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-users-cog me-2" style="color:#2e7df7;"></i>Utilisateurs</h1>
        <p>Gérer les comptes d'accès à l'application</p>
    </div>
    <a href="{{ route('utilisateurs.create') }}" class="btn-primary-inptic">
        <i class="fas fa-plus"></i> Ajouter un utilisateur
    </a>
</div>

<div class="card-white">
    <div class="table-responsive">
        <table class="table table-inptic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Login</th>
                    <th>Nom d'affichage</th>
                    <th>Email</th>
                    <th class="text-center">Rôle</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Dernière connexion</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $i => $u)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $u->login }}</strong></td>
                    <td>{{ $u->nom_affichage }}</td>
                    <td style="font-size:12px;">{{ $u->email ?? '—' }}</td>
                    <td class="text-center">
                        @if($u->role == 'ADMIN')
                            <span class="badge-bleu">Admin</span>
                        @elseif($u->role == 'ENSEIGNANT')
                            <span style="background:#e1d5e7; color:#3c3489; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; display:inline-block;">Enseignant</span>
                        @elseif($u->role == 'SECRETARIAT')
                            <span class="badge-vert">Secrétariat</span>
                        @elseif($u->role == 'ETUDIANT')
                            <span class="badge-jaune">Étudiant</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($u->actif)
                            <span class="badge-vert">Actif</span>
                        @else
                            <span class="badge-rouge">Inactif</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size:12px; color:#6b7280;">
                        {{ $u->derniere_connexion ? $u->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">

                            {{-- Modifier --}}
                            <a href="{{ route('utilisateurs.edit', $u->id) }}"
                               class="tb-btn" title="Modifier">
                                <i class="fas fa-edit" style="font-size:12px;"></i>
                            </a>

                            {{-- Affecter matières (enseignant uniquement) --}}
                            @if($u->role == 'ENSEIGNANT')
                            <a href="{{ route('utilisateurs.affecter-matieres', $u->id) }}"
                               class="tb-btn"
                               style="background:#e6f1fb; border-color:#b5d4f4; color:#0c447c;"
                               title="Affecter des matières">
                                <i class="fas fa-chalkboard-teacher" style="font-size:12px;"></i>
                            </a>
                            @endif

                            {{-- Reset mot de passe --}}
                            <button type="button"
                                    class="tb-btn"
                                    style="background:#faeeda; border-color:#fac775; color:#633806;"
                                    title="Réinitialiser le mot de passe"
                                    onclick="document.getElementById('reset-modal-{{ $u->id }}').style.display='flex'">
                                <i class="fas fa-key" style="font-size:12px;"></i>
                            </button>

                            {{-- Désactiver --}}
                            @if($u->id !== Auth::id())
                            <form method="POST" action="{{ route('utilisateurs.destroy', $u->id) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Désactiver cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button class="tb-btn"
                                        style="background:#fcebeb; border-color:#f7c1c1; color:#791f1f;"
                                        title="Désactiver">
                                    <i class="fas fa-ban" style="font-size:12px;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding:40px; color:#9ca3af;">
                        <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modals Reset Mot de Passe --}}
@foreach($utilisateurs as $u)
<div id="reset-modal-{{ $u->id }}"
     style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:32px; width:100%; max-width:420px; border:1px solid #e5e7eb;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h5 style="font-size:15px; font-weight:600; color:#1e2a3a; margin:0;">
                <i class="fas fa-key me-2" style="color:#2e7df7;"></i>
                Réinitialiser le mot de passe
            </h5>
            <button onclick="document.getElementById('reset-modal-{{ $u->id }}').style.display='none'"
                    style="background:none; border:none; color:#6b7280; cursor:pointer; font-size:18px;">
                &times;
            </button>
        </div>

        <div style="background:#f8f9ff; border-radius:8px; padding:10px 14px; margin-bottom:18px; font-size:12px; color:#6b7280;">
            Compte : <strong style="color:#1e2a3a;">{{ $u->nom_affichage }}</strong>
            ({{ $u->login }})
        </div>

        <form method="POST" action="{{ route('utilisateurs.reset-password', $u->id) }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="form-label-inptic">Nouveau mot de passe</label>
                <input type="password"
                       name="nouveau_mot_de_passe"
                       class="form-control-inptic"
                       placeholder="Minimum 6 caractères"
                       required>
            </div>
            <div style="margin-bottom:20px;">
                <label class="form-label-inptic">Confirmer le mot de passe</label>
                <input type="password"
                       name="nouveau_mot_de_passe_confirmation"
                       class="form-control-inptic"
                       placeholder="Répéter le mot de passe"
                       required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn-primary-inptic">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <button type="button"
                        class="btn-secondary-inptic"
                        onclick="document.getElementById('reset-modal-{{ $u->id }}').style.display='none'">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection