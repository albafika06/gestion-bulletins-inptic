@extends('layouts.app')
@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('page_sub', 'INPTIC — Mes notifications')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-bell me-2" style="color:#2e7df7;"></i>Notifications</h1>
        <p>{{ $notifications->count() }} notification(s) récente(s)</p>
    </div>
    @if($notifications->where('lu', false)->count() > 0)
        <form method="POST" action="{{ route('notifications.tout-lu') }}">
            @csrf
            <button type="submit" class="btn-secondary-inptic">
                <i class="fas fa-check-double"></i> Tout marquer comme lu
            </button>
        </form>
    @endif
</div>

<div class="card-white">

    @if($notifications->isEmpty())
        <div style="text-align:center; padding:60px 20px; color:#6b7280;">
            <i class="fas fa-bell-slash" style="font-size:44px; display:block; margin-bottom:14px; color:#d1d5db;"></i>
            <div style="font-size:14px; font-weight:600; color:#374151; margin-bottom:4px;">Aucune notification</div>
            <div style="font-size:12px;">Vous n'avez pas encore reçu de notification.</div>
        </div>

    @else
        <div style="display:flex; flex-direction:column; gap:6px;">
            @foreach($notifications as $notif)
                <div style="
                    display:flex;
                    align-items:flex-start;
                    gap:14px;
                    padding:14px 16px;
                    border-radius:8px;
                    border:1px solid {{ $notif->lu ? '#e5e7eb' : '#bfdbfe' }};
                    background:{{ $notif->lu ? '#fff' : '#eff6ff' }};
                ">
                    {{-- Icône --}}
                    <div style="
                        width:38px; height:38px; flex-shrink:0;
                        border-radius:50%;
                        background:{{ $notif->lu ? '#f4f5f7' : '#dbeafe' }};
                        display:flex; align-items:center; justify-content:center;
                    ">
                        <i class="fas fa-bell"
                           style="font-size:14px; color:{{ $notif->lu ? '#9ca3af' : '#2e7df7' }};"></i>
                    </div>

                    {{-- Contenu --}}
                    <div style="flex:1; min-width:0;">
                        <div style="
                            font-size:13px;
                            color:#1e2a3a;
                            font-weight:{{ $notif->lu ? '400' : '600' }};
                            margin-bottom:4px;
                            line-height:1.4;
                        ">
                            {{ $notif->message }}
                        </div>
                        <div style="font-size:11px; color:#9ca3af;">
                            <i class="fas fa-clock me-1"></i>
                            {{ $notif->created_at->diffForHumans() }}
                            &nbsp;·&nbsp;
                            {{ $notif->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">

                        {{-- Bouton Voir (si lien disponible) --}}
                        @if($notif->lien)
                            <a href="{{ $notif->lien }}"
                               class="btn-primary-inptic"
                               style="font-size:11px; padding:5px 12px;">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        @endif

                        {{-- Marquer lu --}}
                        @if(!$notif->lu)
                            <form method="POST" action="{{ route('notifications.lu', $notif->id) }}">
                                @csrf
                                <button type="submit"
                                        class="btn-secondary-inptic"
                                        style="font-size:11px; padding:5px 10px;"
                                        title="Marquer comme lu">
                                    <i class="fas fa-check"></i> Lu
                                </button>
                            </form>
                        @else
                            <span style="font-size:11px; color:#27500a; padding:5px 4px; white-space:nowrap;">
                                <i class="fas fa-check-circle"></i> Lu
                            </span>
                        @endif

                        {{-- Supprimer --}}
                        <form method="POST"
                              action="{{ route('notifications.destroy', $notif->id) }}"
                              onsubmit="return confirm('Supprimer cette notification ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn-danger-inptic"
                                    style="font-size:11px; padding:5px 10px;"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection