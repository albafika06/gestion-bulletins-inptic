<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe — INPTIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f4f5f7; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card-form { background:#fff; border-radius:14px; border:1px solid #e5e7eb; padding:40px; width:100%; max-width:420px; }
        .form-icon { width:52px; height:52px; background:#1e2a3a; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
        .form-title { font-size:20px; font-weight:700; color:#1e2a3a; text-align:center; margin-bottom:6px; }
        .form-sub { font-size:12px; color:#6b7280; text-align:center; margin-bottom:24px; }
        .form-lbl { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px; }
        .input-wrap { display:flex; align-items:center; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; margin-bottom:16px; transition:border-color 0.15s; }
        .input-wrap:focus-within { border-color:#2e7df7; box-shadow:0 0 0 3px rgba(46,125,247,0.1); }
        .input-icon { padding:0 12px; color:#9ca3af; font-size:13px; background:#f9fafb; height:42px; display:flex; align-items:center; border-right:1px solid #e5e7eb; }
        .input-wrap input { border:none; outline:none; padding:10px 14px; font-size:13px; color:#1e2a3a; flex:1; background:#fff; }
        .btn-submit { width:100%; background:#2e7df7; color:#fff; border:none; border-radius:8px; padding:11px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.15s; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:6px; }
        .btn-submit:hover { background:#1a6de0; }
        .back-link { display:block; text-align:center; margin-top:16px; font-size:12px; color:#6b7280; text-decoration:none; }
        .back-link:hover { color:#2e7df7; }
        .error-msg { background:#fcebeb; border:1px solid #f09595; color:#791f1f; border-radius:8px; padding:10px 14px; font-size:12px; margin-bottom:16px; }
    </style>
</head>
<body>
    <div class="card-form">
        <div class="form-icon">
            <i class="fas fa-lock" style="color:#fff; font-size:20px;"></i>
        </div>
        <div class="form-title">Nouveau mot de passe</div>
        <div class="form-sub">
            Choisissez un nouveau mot de passe sécurisé
        </div>

        @if($errors->any())
        <div class="error-msg">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="form-lbl">Nouveau mot de passe</label>
                <div class="input-wrap">
                    <div class="input-icon"><i class="fas fa-lock"></i></div>
                    <input type="password" name="mot_de_passe"
                           placeholder="Minimum 6 caractères" autofocus>
                </div>
            </div>

            <div>
                <label class="form-lbl">Confirmer le mot de passe</label>
                <div class="input-wrap">
                    <div class="input-icon"><i class="fas fa-lock"></i></div>
                    <input type="password" name="mot_de_passe_confirmation"
                           placeholder="Répéter le mot de passe">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check"></i>
                Enregistrer le nouveau mot de passe
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
        </a>
    </div>
</body>
</html>