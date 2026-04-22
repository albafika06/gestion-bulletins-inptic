<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — INPTIC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f4f5f7; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .wrapper { display:flex; width:900px; max-width:100%; min-height:520px; border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.12); }
        .left { width:320px; background:#1e2a3a; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 28px; flex-shrink:0; }
        .logo-circle { width:90px; height:90px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:18px; border:3px solid rgba(255,255,255,0.15); overflow:hidden; }
        .logo-circle img { width:78px; height:78px; object-fit:contain; }
        .left-title { color:#fff; font-size:20px; font-weight:700; text-align:center; margin-bottom:6px; }
        .left-sub { color:#7eb3ff; font-size:12px; text-align:center; line-height:1.6; margin-bottom:28px; }
        .flag-badge { background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:10px 14px; display:flex; align-items:center; gap:10px; width:100%; }
        .rep-flag { width:24px; height:16px; display:flex; flex-direction:column; border-radius:2px; overflow:hidden; flex-shrink:0; }
        .rep-flag div { flex:1; }
        .rep-text { font-size:10px; color:#7eb3ff; line-height:1.4; }
        .rep-text strong { color:#fff; display:block; font-size:11px; }
        .left-footer { color:#5a7a9f; font-size:10px; text-align:center; margin-top:28px; }
        .right { flex:1; background:#fff; display:flex; align-items:center; justify-content:center; padding:40px; }
        .form-card { width:100%; max-width:380px; }
        .icon-circle { width:56px; height:56px; background:#faeeda; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
        .form-title { font-size:22px; font-weight:700; color:#1e2a3a; text-align:center; margin-bottom:6px; }
        .form-sub { font-size:12px; color:#6b7280; text-align:center; margin-bottom:20px; line-height:1.6; }
        .info-box { background:#e6f1fb; border:1px solid #b5d4f4; border-radius:8px; padding:11px 14px; font-size:12px; color:#0c447c; margin-bottom:16px; display:flex; align-items:flex-start; gap:8px; line-height:1.5; }
        .form-lbl { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px; }
        .input-wrap { display:flex; align-items:center; border:1.5px solid #e5e7eb; border-radius:9px; overflow:hidden; transition:border-color 0.15s, box-shadow 0.15s; margin-bottom:16px; }
        .input-wrap:focus-within { border-color:#2e7df7; box-shadow:0 0 0 3px rgba(46,125,247,0.1); }
        .input-icon { padding:0 13px; color:#9ca3af; background:#f9fafb; height:44px; display:flex; align-items:center; border-right:1px solid #e5e7eb; font-size:14px; }
        .input-wrap input { border:none; outline:none; padding:10px 14px; font-size:14px; color:#1e2a3a; flex:1; background:#fff; }
        .alert-success { background:#eaf3de; border:1px solid #97c459; color:#27500a; border-radius:8px; padding:11px 14px; font-size:12px; margin-bottom:16px; display:flex; align-items:flex-start; gap:8px; line-height:1.5; }
        .alert-error { background:#fcebeb; border:1px solid #f09595; color:#791f1f; border-radius:8px; padding:11px 14px; font-size:12px; margin-bottom:16px; display:flex; align-items:flex-start; gap:8px; }
        .btn-send { width:100%; background:#2e7df7; color:#fff; border:none; border-radius:9px; padding:12px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.15s; display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-send:hover { background:#1a6de0; }
        .back-link { display:block; text-align:center; margin-top:14px; font-size:12px; color:#6b7280; text-decoration:none; transition:color 0.15s; }
        .back-link:hover { color:#2e7df7; }
        @media (max-width:700px) { .left { display:none; } .wrapper { width:100%; border-radius:0; min-height:100vh; } }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="left">
        <div class="logo-circle">
            <img src="{{ asset('images/logo_inptic.png') }}" alt="INPTIC">
        </div>
        <div class="left-title">INPTIC</div>
        <div class="left-sub">
            Institut National de la Poste,<br>
            des Technologies de l'Information<br>
            et de la Communication
        </div>
        <div class="flag-badge">
            <div class="rep-flag">
                <div style="background:#009e60;"></div>
                <div style="background:#fcd116;"></div>
                <div style="background:#003189;"></div>
            </div>
            <div class="rep-text">
                <strong>République Gabonaise</strong>
                Union · Travail · Justice
            </div>
        </div>
        <div class="left-footer">&copy; {{ date('Y') }} INPTIC · Tous droits réservés</div>
    </div>

    <div class="right">
        <div class="form-card">

            <div class="icon-circle">
                <i class="fas fa-key" style="color:#633806; font-size:22px;"></i>
            </div>
            <div class="form-title">Mot de passe oublié</div>
            <div class="form-sub">
                Entrez votre adresse email et nous vous<br>
                enverrons un lien de réinitialisation.
            </div>

            @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle" style="margin-top:1px; flex-shrink:0;"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle" style="flex-shrink:0;"></i>
                {{ session('error') }}
            </div>
            @endif

            @if(session('info') && !session('success'))
            <div class="info-box">
                <i class="fas fa-info-circle" style="flex-shrink:0; margin-top:1px;"></i>
                {{ session('info') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle" style="flex-shrink:0;"></i>
                {{ $errors->first() }}
            </div>
            @endif

            @if(!session('success'))
            <div class="info-box">
                <i class="fas fa-envelope" style="flex-shrink:0; margin-top:1px;"></i>
                Le lien sera envoyé directement dans votre boîte Gmail. Vérifiez aussi vos spams.
            </div>

            <form method="POST" action="{{ route('password.send') }}">
                @csrf
                <div>
                    <label class="form-lbl">Adresse email</label>
                    <div class="input-wrap">
                        <div class="input-icon"><i class="fas fa-envelope"></i></div>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="votre@gmail.com"
                               autofocus>
                    </div>
                </div>
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                    Envoyer le lien de réinitialisation
                </button>
            </form>
            @endif

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
            </a>

        </div>
    </div>

</div>

</body>
</html>