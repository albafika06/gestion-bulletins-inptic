<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accès refusé — INPTIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f5f7; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .error-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 48px 40px; text-align: center; max-width: 440px; }
        .error-icon { width: 72px; height: 72px; background: #fcebeb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .error-code { font-size: 48px; font-weight: 700; color: #1e2a3a; line-height: 1; margin-bottom: 8px; }
        .error-title { font-size: 18px; font-weight: 600; color: #1e2a3a; margin-bottom: 8px; }
        .error-desc { font-size: 13px; color: #6b7280; margin-bottom: 24px; }
        .btn-home { background: #2e7df7; color: #fff; border: none; border-radius: 8px; padding: 10px 24px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-home:hover { background: #1a6de0; color: #fff; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-lock" style="color:#791f1f; font-size:28px;"></i>
        </div>
        <div class="error-code">403</div>
        <div class="error-title">Accès non autorisé</div>
        <div class="error-desc">
            Vous n'avez pas les permissions nécessaires pour accéder à cette page.
            Contactez l'administrateur si vous pensez qu'il s'agit d'une erreur.
        </div>
        <a href="{{ url('/dashboard') }}" class="btn-home">
            <i class="fas fa-home"></i> Retour au tableau de bord
        </a>
    </div>
</body>
</html>