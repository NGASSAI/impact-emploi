<!-- Page de Politique de Confidentialité -->
<?php
require_once 'config.php';
include 'includes/header.php';
?>

<div class="container" style="max-width: 900px; padding: 40px 0;">
    <h1 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 10px;">
        🔒 Politique de Confidentialité
    </h1>
    <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 40px;">
        Votre vie privée est importante pour nous
    </p>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">1. Informations que nous Collectons</h2>
        <p style="color: var(--text-secondary); margin-bottom: 15px;">
            <strong>Informations d'Inscription :</strong>
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary); margin-bottom: 15px;">
            <li>Nom et prénom</li>
            <li>Adresse email</li>
            <li>Numéro de téléphone</li>
            <li>Mot de passe hashé (jamais stocké en texte clair)</li>
        </ul>

        <p style="color: var(--text-secondary); margin-bottom: 15px;">
            <strong>Informations de Profil :</strong>
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary); margin-bottom: 15px;">
            <li>Photo de profil</li>
            <li>Biographie</li>
            <li>CV (pour les candidats)</li>
            <li>Offres d'emploi (pour les recruteurs)</li>
        </ul>

        <p style="color: var(--text-secondary);">
            <strong>Données Techniques :</strong>
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary);">
            <li>Adresse IP</li>
            <li>Type de navigateur</li>
            <li>Pages visitées</li>
            <li>Journaux d'activités</li>
        </ul>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">2. Utilisation des Informations</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Nous utilisons les informations collectées pour:
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary);">
            <li>Fournir et améliorer nos services</li>
            <li>Traiter les candidatures et les offres d'emploi</li>
            <li>Envoyer des notifications et des mises à jour</li>
            <li>Prévenir la fraude et améliorer la sécurité</li>
            <li>Analyser l'utilisation du site</li>
            <li>Respecter les obligations légales</li>
        </ul>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">3. Sécurité des Données</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Nous implementons les mesures de sécurité suivantes:
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary);">
            <li>✅ Chiffrement des mots de passe avec Argon2id</li>
            <li>✅ Protection CSRF sur tous les formulaires</li>
            <li>✅ Validation et nettoyage de toutes les entrées</li>
            <li>✅ Requêtes SQL paramétrées</li>
            <li>✅ Headers de sécurité HTTP</li>
            <li>✅ Journalisation des activités sensibles</li>
        </ul>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">4. Partage des Données</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Vos données ne sont jamais vendues à des tiers. Nous partageons vos informations uniquement:
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary);">
            <li>Avec d'autres utilisateurs (recruteurs/candidats) pour le processus de recrutement</li>
            <li>Avec les autorités si légalement obligatoires</li>
            <li>Pour prévenir la fraude ou le comportement illégal</li>
        </ul>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">5. Durée de Conservation des Données</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Les données sont conservées aussi longtemps que votre compte est actif. 
            Vous pouvez demander la suppression de votre compte et de vos données à tout moment. 
            Les archives de sécurité peuvent être conservées pendant 6 mois supplémentaires.
        </p>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">6. Vos Droits</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Vous avez le droit de:
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary);">
            <li>Accéder à vos données personnelles</li>
            <li>Corriger les informations inexactes</li>
            <li>Demander la suppression de vos données</li>
            <li>Retirer votre consentement</li>
            <li>Exporter vos données</li>
        </ul>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">7. Cookies et Suivi</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Nous utilisons:
        </p>
        <ul style="padding-left: 30px; color: var(--text-secondary);">
            <li><strong>Cookies de Session :</strong> Pour maintenir votre session logée</li>
            <li><strong>Cache Bust Tokens :</strong> Pour forcer les mises à jour de contenu</li>
        </ul>
        <p style="margin-top: 15px; color: var(--text-secondary);">
            Nous n'utilisons pas de cookies de suivi tiers ou d'analytique.
        </p>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h2 style="color: var(--primary); margin-bottom: 20px;">8. Modifications de cette Politique</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Nous pouvons modifier cette Politique de Confidentialité à tout moment. 
            Les modifications importantes vous seront notifiées par email. 
            L'utilisation continue du Site après les modifications signifie votre acceptation.
        </p>
    </div>

    <div class="card">
        <h2 style="color: var(--primary); margin-bottom: 20px;">9. Nous Contacter</h2>
        <p style="line-height: 1.8; color: var(--text-secondary);">
            Pour toute question concernant votre vie privée:
        </p>
        <div style="margin-top: 20px;">
            <p><strong>📧 Email :</strong> <a href="mailto:nathanngassai885@gmail.com" style="color: var(--primary);">nathanngassai885@gmail.com</a></p>
            <p><strong>📱 WhatsApp :</strong> <a href="https://wa.me/242066817726" style="color: var(--primary);">+242 066 817 726</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>