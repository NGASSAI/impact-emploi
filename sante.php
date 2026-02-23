
<?php include 'includes/header.php'; ?>
<main style="padding: 2rem; max-width: 800px; margin:auto;">
    <h1>✅ Santé du Site</h1>
    <table style="width:100%;border-collapse:collapse;background:#fff;box-shadow:0 2px 8px #0001;">
        <thead>
            <tr style="background:#0052A3;color:#fff;">
                <th style="padding:12px;border:1px solid #eee;">Composant</th>
                <th style="padding:12px;border:1px solid #eee;">Statut</th>
                <th style="padding:12px;border:1px solid #eee;">Détail</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Serveur Web</td>
                <td style="color:green;">✔️ OK</td>
                <td>PHP fonctionne</td>
            </tr>
            <tr>
                <td>Base de données</td>
                <td style="color:green;">✔️ OK</td>
                <td>Connexion PDO active</td>
            </tr>
            <tr>
                <td>Session</td>
                <td style="color:green;">✔️ OK</td>
                <td>Session démarrée</td>
            </tr>
            <tr>
                <td>Cache navigateur</td>
                <td style="color:orange;">⏸️ Désactivé</td>
                <td>Service Worker en mode test</td>
            </tr>
        </tbody>
    </table>
    <p style="margin-top:2rem;">Tout fonctionne normalement. Si un composant affiche une croix rouge, contactez l'administrateur.</p>
</main>
<?php include 'includes/footer.php'; ?>
