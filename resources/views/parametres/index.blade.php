@extends('layouts.app')
@section('title', 'Paramètres')
@section('page_title', 'Paramètres')
@section('page_sub', 'Configuration des règles de calcul et de validation')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-cog me-2" style="color:#2e7df7;"></i>Paramètres</h1>
    <p>Configuration des règles de calcul et de validation</p>
</div>

<div class="card-white" style="max-width:800px;">

    @if(session('success'))
    <div class="alert-inptic-success mb-3">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('parametres.update') }}">
        @csrf

        @foreach($parametres as $i => $param)
        <div style="background:{{ $param->modifiable ? '#fff' : '#f8f9ff' }}; border:1px solid #e5e7eb; border-radius:8px; padding:14px 18px; margin-bottom:10px; border-left:4px solid {{ $param->modifiable ? '#2e7df7' : '#d1d5db' }};">
            <input type="hidden" name="parametres[{{ $i }}][cle]" value="{{ $param->cle }}">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div style="font-weight:600; color:#1e2a3a; font-size:13px;">{{ $param->cle }}</div>
                    <div style="color:#6b7280; font-size:11px; margin-top:2px;">{{ $param->description }}</div>
                </div>
                <div class="col-md-5">
                    <input type="text"
                           name="parametres[{{ $i }}][valeur]"
                           class="form-control-inptic"
                           value="{{ $param->valeur }}"
                           {{ !$param->modifiable ? 'readonly' : '' }}
                           style="{{ !$param->modifiable ? 'background:#f0f2f5; cursor:not-allowed;' : '' }}">
                </div>
                <div class="col-md-2 text-center">
                    @if($param->modifiable)
                        <span class="badge-vert" style="font-size:11px;">
                            <i class="fas fa-edit me-1"></i>Modifiable
                        </span>
                    @else
                        <span class="badge-gris" style="font-size:11px;">
                            <i class="fas fa-lock me-1"></i>Fixe
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div class="mt-4">
            <button type="submit" class="btn-primary-inptic">
                <i class="fas fa-save"></i> Enregistrer les paramètres
            </button>
        </div>
    </form>
</div>

@endsection