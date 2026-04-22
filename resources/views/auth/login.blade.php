<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — INPTIC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2f8;
            position: relative;
            overflow: hidden;
        }
        canvas#hexCanvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
        }
        .login-wrapper {
            display: flex;
            width: 900px;
            max-width: 95%;
            min-height: 540px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,0.14);
            position: relative;
            z-index: 10;
        }
        .login-left {
            width: 320px;
            background: #1e2a3a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 28px;
            flex-shrink: 0;
        }
        .logo-circle {
            width: 96px; height: 96px;
            background: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            border: 3px solid rgba(255,255,255,0.15);
            overflow: hidden;
        }
        .logo-circle img { width: 82px; height: 82px; object-fit: contain; }
        .left-title { color: #fff; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 6px; }
        .left-sub { color: #7eb3ff; font-size: 11px; text-align: center; line-height: 1.7; margin-bottom: 28px; }
        .flag-badge {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex; align-items: center; gap: 10px;
            width: 100%;
        }
        .rep-flag { width: 26px; height: 17px; display: flex; flex-direction: column; border-radius: 2px; overflow: hidden; flex-shrink: 0; }
        .rep-flag div { flex: 1; }
        .rep-text { font-size: 10px; color: #7eb3ff; line-height: 1.4; }
        .rep-text strong { color: #fff; display: block; font-size: 11px; }
        .left-footer { color: #5a7a9f; font-size: 10px; text-align: center; margin-top: 32px; }
        .login-right {
            flex: 1; background: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 40px;
        }
        .form-card { width: 100%; max-width: 380px; }
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-icon {
            width: 52px; height: 52px; background: #1e2a3a;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .form-title { font-size: 22px; font-weight: 700; color: #1e2a3a; margin-bottom: 5px; }
        .form-sub { font-size: 12px; color: #6b7280; }
        .form-group { margin-bottom: 18px; }
        .form-lbl { font-size: 12px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; }
        .input-wrap {
            display: flex; align-items: center;
            border: 1.5px solid #e5e7eb;
            border-radius: 9px; overflow: hidden;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .input-wrap:focus-within {
            border-color: #2e7df7;
            box-shadow: 0 0 0 3px rgba(46,125,247,0.1);
        }
        .input-icon {
            padding: 0 14px; color: #9ca3af;
            background: #f9fafb; height: 46px;
            display: flex; align-items: center;
            border-right: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .input-wrap input {
            border: none; outline: none;
            padding: 12px 14px; font-size: 14px;
            color: #1e2a3a; flex: 1; background: #fff;
        }
        .alert-error {
            background: #fcebeb; border: 1px solid #f09595;
            color: #791f1f; border-radius: 9px;
            padding: 11px 14px; font-size: 13px;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success {
            background: #eaf3de; border: 1px solid #97c459;
            color: #27500a; border-radius: 9px;
            padding: 11px 14px; font-size: 13px;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .btn-login {
            width: 100%; background: #2e7df7; color: #fff;
            border: none; border-radius: 9px;
            padding: 13px; font-size: 14px; font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 6px;
        }
        .btn-login:hover { background: #1a6de0; }
        .btn-login:active { transform: scale(0.99); }
        .form-footer { text-align: center; margin-top: 22px; font-size: 11px; color: #9ca3af; }
        @media (max-width: 700px) {
            .login-left { display: none; }
            .login-wrapper { width: 100%; border-radius: 0; min-height: 100vh; }
            .login-right { padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <canvas id="hexCanvas"></canvas>

    <div class="login-wrapper">
        <div class="login-left">
            <div class="logo-circle">
                <img src="{{ asset('images/logo_inptic.png') }}" alt="Logo INPTIC">
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

        <div class="login-right">
            <div class="form-card">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-graduation-cap" style="color:#fff; font-size:20px;"></i>
                    </div>
                    <div class="form-title">Connexion</div>
                    <div class="form-sub">Accédez à votre espace LP ASUR</div>
                </div>

                @if($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-lbl">Login</label>
                        <div class="input-wrap">
                            <div class="input-icon"><i class="fas fa-user"></i></div>
                            <input type="text" name="login"
                                   value="{{ old('login') }}"
                                   placeholder="Votre identifiant" autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-lbl">Mot de passe</label>
                        <div class="input-wrap">
                            <div class="input-icon"><i class="fas fa-lock"></i></div>
                            <input type="password" name="mot_de_passe"
                                   placeholder="Votre mot de passe">
                        </div>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </button>
                </form>

                <div class="form-footer">
                    &copy; {{ date('Y') }} INPTIC — Gestion Bulletins LP ASUR
                </div>
            </div>
        </div>
    </div>

    <script>
        var canvas = document.getElementById('hexCanvas');
        var ctx    = canvas.getContext('2d');
        var t      = 0;

        function resize() {
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        function hexPath(cx, cy, r) {
            ctx.beginPath();
            for (var i = 0; i < 6; i++) {
                var angle = (Math.PI / 3) * i - Math.PI / 6;
                var x = cx + r * Math.cos(angle);
                var y = cy + r * Math.sin(angle);
                if (i === 0) ctx.moveTo(x, y);
                else         ctx.lineTo(x, y);
            }
            ctx.closePath();
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            var r      = 30;
            var w      = r * Math.sqrt(3);
            var h      = r * 1.5;
            t         += 0.004;

            var offsetX = (t * 18) % w;
            var offsetY = (t * 10) % (h * 2);

            var cols = Math.ceil(canvas.width  / w) + 3;
            var rows = Math.ceil(canvas.height / h) + 3;

            for (var row = -2; row < rows; row++) {
                for (var col = -2; col < cols; col++) {
                    var cx = col * w + (row % 2 === 0 ? 0 : w / 2) - offsetX;
                    var cy = row * h - offsetY;

                    var wave  = Math.sin(cx * 0.015 + cy * 0.01 + t * 2) * 0.5 + 0.5;
                    var alpha = 0.04 + wave * 0.10;

                    hexPath(cx, cy, r - 1);
                    ctx.strokeStyle = 'rgba(46,125,247,' + alpha.toFixed(3) + ')';
                    ctx.lineWidth   = 1;
                    ctx.stroke();

                    if (wave > 0.80) {
                        hexPath(cx, cy, r - 1);
                        ctx.fillStyle = 'rgba(46,125,247,' + (wave * 0.04).toFixed(3) + ')';
                        ctx.fill();
                    }
                }
            }
            requestAnimationFrame(draw);
        }
        draw();
    </script>

</body>
</html>