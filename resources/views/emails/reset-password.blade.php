<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation mot de passe — INPTIC</title>
</head>
<body style="margin:0; padding:0; background:#f4f5f7; font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    <!-- En-tête -->
                    <tr>
                        <td style="background:#1e2a3a; border-radius:12px 12px 0 0; padding:28px 32px; text-align:center;">
                            <div style="width:60px; height:60px; background:#fff; border-radius:50%; margin:0 auto 14px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                <img src="{{ asset('images/logo_inptic.png') }}"
                                     alt="INPTIC"
                                     style="width:52px; height:52px; object-fit:contain;">
                            </div>
                            <h1 style="color:#fff; font-size:18px; font-weight:700; margin:0 0 4px;">INPTIC</h1>
                            <p style="color:#7eb3ff; font-size:12px; margin:0;">Gestion Bulletins LP ASUR</p>
                        </td>
                    </tr>

                    <!-- Corps -->
                    <tr>
                        <td style="background:#fff; padding:32px;">

                            <h2 style="color:#1e2a3a; font-size:20px; font-weight:700; margin:0 0 8px;">
                                Réinitialisation de votre mot de passe
                            </h2>
                            <p style="color:#6b7280; font-size:14px; margin:0 0 20px; line-height:1.6;">
                                Bonjour <strong style="color:#1e2a3a;">{{ $nomUtilisateur }}</strong>,
                            </p>
                            <p style="color:#374151; font-size:14px; margin:0 0 24px; line-height:1.7;">
                                Vous avez demandé la réinitialisation de votre mot de passe pour l'application
                                <strong>Gestion Bulletins LP ASUR</strong> de l'INPTIC.
                                Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.
                            </p>

                            <!-- Bouton -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:0 0 24px;">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block; background:#2e7df7; color:#fff; text-decoration:none; font-size:14px; font-weight:600; padding:12px 32px; border-radius:8px;">
                                            Réinitialiser mon mot de passe
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Avertissement expiration -->
                            <div style="background:#faeeda; border:1px solid #fac775; border-radius:8px; padding:12px 16px; margin-bottom:20px;">
                                <p style="color:#633806; font-size:12px; margin:0;">
                                    ⏱️ Ce lien expire dans <strong>1 heure</strong>.
                                    Après ce délai, vous devrez faire une nouvelle demande.
                                </p>
                            </div>

                            <!-- Lien alternatif -->
                            <p style="color:#6b7280; font-size:12px; margin:0 0 8px;">
                                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :
                            </p>
                            <p style="background:#f8f9ff; border:1px solid #e5e7eb; border-radius:6px; padding:10px 12px; font-size:11px; color:#2e7df7; word-break:break-all; margin:0 0 24px;">
                                {{ $resetUrl }}
                            </p>

                            <!-- Sécurité -->
                            <div style="background:#fcebeb; border:1px solid #f09595; border-radius:8px; padding:12px 16px;">
                                <p style="color:#791f1f; font-size:12px; margin:0;">
                                    🔒 Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
                                    Votre mot de passe ne sera pas modifié.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Pied de page -->
                    <tr>
                        <td style="background:#f8f9ff; border-radius:0 0 12px 12px; padding:20px 32px; text-align:center; border-top:1px solid #e5e7eb;">
                            <p style="color:#9ca3af; font-size:11px; margin:0 0 4px;">
                                Institut National de la Poste, des Technologies de l'Information et de la Communication
                            </p>
                            <p style="color:#9ca3af; font-size:11px; margin:0;">
                                &copy; {{ date('Y') }} INPTIC — Libreville, Gabon
                            </p>
                            <div style="margin-top:10px; display:flex; align-items:center; justify-content:center; gap:8px;">
                                <div style="width:18px; height:12px; display:inline-flex; flex-direction:column; overflow:hidden; border-radius:1px; vertical-align:middle;">
                                    <div style="flex:1; background:#009e60;"></div>
                                    <div style="flex:1; background:#fcd116;"></div>
                                    <div style="flex:1; background:#003189;"></div>
                                </div>
                                <span style="color:#9ca3af; font-size:10px;">République Gabonaise · Union · Travail · Justice</span>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>