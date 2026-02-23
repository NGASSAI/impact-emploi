<?php
require_once 'includes/csrf.php';
generateCSRFToken();
require_once 'includes/header.php';
?>

<div class="container help-page" style="font-family: Arial, Helvetica, sans-serif; color: #222;">
    <div style="margin-bottom: 40px;">
        <a href="index.php" class="back-link">← Retour à l'accueil</a>
    </div>

    <div class="help-header">
        <h1>❓ Centre d'aide et guides</h1>
        <p style="color:#222; background:none;">Bienvenue dans le centre d'aide d'Impact Emploi.<br>
        Retrouvez ici toutes les explications pour utiliser la plateforme, installer l'application, gérer votre profil, publier ou postuler, et résoudre les problèmes courants.</p>
    </div>

    <div class="help-grid">
        <!-- Section Chercheur d'emploi -->
        <div class="help-card">
            <div class="help-card-icon">💼</div>
            <h2>Pour les chercheurs d'emploi</h2>
            
            <div class="help-item">
                <h3>🔍 Recherche avancée et filtres</h3>
                <p style="color:#222; background:none;">Sur la page d'accueil, utilisez les filtres pour trouver l'offre parfaite :</p>
                <ul style="margin: 10px 0; padding-left: 20px; color: var(--secondary);">
                    <li><strong>Métier/Description :</strong> Tapez un mot-clé</li>
                    <li><strong>Localité/Ville :</strong> Filtrez par région</li>
                    <li><strong>Type de contrat :</strong> CDI, CDD, Stage, Freelance, etc.</li>
                </ul>
            </div>

            <div class="help-item">
                <h3>📤 Partager les offres d'emploi</h3>
                <p style="color:#222; background:none;">Trouvé une offre intéressante ? Partagez-la avec vos amis :</p>
                <ul style="margin: 10px 0; padding-left: 20px; color: var(--secondary);">
                    <li><strong>💬 WhatsApp :</strong> Envoyez directement au recruteur ou partagez avec des amis</li>
                    <li><strong>📧 Email :</strong> Contactez le recruteur directement</li>
                    <li><strong>📋 Copier le lien :</strong> Partagez l'URL sur les réseaux sociaux</li>
                    <li><strong>📤 Partager :</strong> Utilisez le menu de partage natif de votre téléphone</li>
                </ul>
            </div>

            <div class="help-item">
                <h3>📱 Navigation sur mobile</h3>
                <p>Sur mobile, cliquez sur le bouton ≡ (trois lignes) en haut à gauche pour ouvrir le menu. Le menu s'affiche en superposition : cliquez sur un lien pour le fermer automatiquement.<br>
                Si le menu bloque la navigation ou ne se ferme pas, actualisez la page ou signalez le problème via le formulaire de contact.</p>
            </div>

            <div class="help-item">
                <h3>💼 Consulter les offres d'emploi</h3>
                <p>Accédez à la page d'accueil pour parcourir toutes les offres d'emploi disponibles. Cliquez sur une offre pour voir les détails complets, la description du poste et les coordonnées du recruteur.<br>
                Les images des offres sont optimisées pour le chargement rapide, mais peuvent prendre quelques secondes selon votre connexion.<br>
                Vous pouvez contacter le recruteur par téléphone, WhatsApp ou email.</p>
            </div>

            <div class="help-item">
                <h3>Créer un compte</h3>
                <p>Cliquez sur "S'inscrire" en haut de la page. Remplissez vos informations (nom, prénom, email, téléphone). Choisissez le rôle "Chercheur d'emploi" ou "Recruteur" selon votre besoin.<br>
                Après inscription, vérifiez votre email pour activer votre compte.</p>
            </div>

            <div class="help-item">
                <h3>Gérer mon profil</h3>
                <p>Une fois connecté, accédez à votre profil pour modifier vos informations (nom, prénom, email, téléphone, photo, bio).<br>
                Cochez "WhatsApp" si vous souhaitez être contacté par ce moyen.<br>
                Les caractères spéciaux s'affichent correctement dans tous les champs de texte.</p>
            </div>

            <div class="help-item">
                <h3>📲 Notifications et confirmations</h3>
                <p>Vous recevrez des notifications toast (pop-ups en haut) pour confirmer vos actions (envoi de formulaires, mise à jour du profil, copies d'URL, etc.).<br>
                Les erreurs s'affichent aussi via ces notifications.<br>
                Sur mobile, elles sont visibles en haut de l'écran.</p>
            </div>

            <div class="help-item">
                <h3>🌐 Installer l'application et utiliser hors ligne (PWA)</h3>
                <p>Impact Emploi fonctionne également hors ligne : votre navigateur télécharge automatiquement la plateforme.<br>
                Pour installer l'application sur votre téléphone :</p>
                <ul style="margin: 10px 0; padding-left: 20px; color: var(--secondary);">
                    <li><strong>Sur Chrome/Edge (Android) :</strong> Ouvrez le menu <b>⋮</b> (trois points) en haut à droite et choisissez <b>"Installer l'application"</b>. L'icône Impact Emploi s'affichera sur votre écran d'accueil.</li>
                    <li><strong>Sur Safari (iPhone) :</strong> Ouvrez le menu <b>Partager</b> puis choisissez <b>"Ajouter à l'écran d'accueil"</b>. L'icône Impact Emploi s'affichera sur votre écran d'accueil.</li>
                    <li><strong>Sur certains navigateurs :</strong> L'option peut ne pas apparaître ou proposer d'autres applications. Dans ce cas, vérifiez le menu du navigateur ou consultez la documentation de votre navigateur.</li>
                </ul>
                <p style="color:#222; background:none;">Après installation, vous aurez une icône Impact Emploi sur votre écran d'accueil et pourrez utiliser la plateforme même hors connexion (sauf pour les actions nécessitant Internet).<br>
                Si l'icône ou le logo ne s'affiche pas correctement, vérifiez que votre navigateur accepte les icônes PWA ou contactez le support.</p>
            </div>
        </div>

        <!-- Section Recruteur -->
        <div class="help-card">
            <div class="help-card-icon">👔</div>
            <h2>Pour les recruteurs</h2>
            <div class="help-item">
                <h3>Publier une offre d'emploi</h3>
                <p>Connectez-vous à votre compte recruteur. Cliquez sur "Publier une offre" et remplissez le formulaire avec le titre du poste, la description, le type de contrat, le lieu, le salaire, et les qualifications requises.<br>
                Ajoutez une image pour rendre votre offre plus attractive (chargement optimisé).</p>
            </div>
            <div class="help-item">
                <h3>Gérer mes offres</h3>
                <p>Sur votre profil, consultez la liste de toutes vos offres publiées. Vous pouvez voir la date de publication, modifier ou supprimer une offre, et vérifier les informations.</p>
            </div>
            <div class="help-item">
                <h3>Être contacté par les candidats</h3>
                <p>Assurez-vous que votre numéro de téléphone et votre email sont à jour dans votre profil. Les candidats pourront vous contacter directement via le numéro affiché sur vos offres, par WhatsApp ou par email.</p>
            </div>
            <div class="help-item">
                <h3>Améliorer votre profil</h3>
                <p>Activez WhatsApp dans votre profil pour permettre aux candidats de vous contacter aussi par message.<br>
                Plus votre profil est complet (photo, description, coordonnées), plus les candidats seront enclins à postuler.</p>
            </div>
        </div>
    </div>

    <!-- Questions communes -->
    <div class="help-section">
        <h2>❔ Questions fréquemment posées</h2>

        <div class="faq-item">
            <h3>🔍 Comment utiliser les filtres de recherche ?</h3>
            <p>Sur la page d'accueil, remplissez les champs de recherche :</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Entrez un mot-clé (métier, compétence, entreprise)</li>
                <li>Sélectionnez une localité si vous voulez chercher dans une région spécifique</li>
                <li>Choisissez un type de contrat (CDI, CDD, Stage, Freelance, Tous les types)</li>
                <li>Cliquez sur "Chercher" pour voir les résultats</li>
            </ul>
        </div>

        <div class="faq-item">
            <h3>📤 Comment partager une offre ?</h3>
            <p>En bas de chaque offre d'emploi, vous trouverez 4 boutons :</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>💬 WhatsApp :</strong> Ouvre WhatsApp pour contacter le recruteur ou partager avec des amis</li>
                <li><strong>📧 Email :</strong> Prépare un email à envoyer au recruteur</li>
                <li><strong>📋 Copier le lien :</strong> Copie l'URL de l'offre dans votre presse-papiers</li>
                <li><strong>📤 Partager :</strong> Utilise le menu de partage natif de votre système</li>
            </ul>
        </div>

        <div class="faq-item">
            <h3>📱 Navigation sur mobile</h3>
            <p>Sur les petits écrans (&lt; 769px), cliquez sur le bouton ≡ (trois lignes) en haut à gauche pour ouvrir le menu. Le menu s'affiche en superposition : cliquez sur un lien pour le fermer automatiquement.<br>
            Si le menu bloque la navigation, actualisez la page ou signalez le problème via le formulaire de contact.</p>
        </div>

        <div class="faq-item">
            <h3>✨ Qu'est-ce que le texte motivant en mobile ?</h3>
            <p>Quand vous n'êtes pas connecté et que vous accédez à la plateforme sur mobile, vous verrez un message animé pour vous encourager à vous inscrire. Sur desktop, vous verrez les boutons "Connexion" et "S'inscrire" normaux.</p>
        </div>

        <div class="faq-item">
            <h3>📲 Notifications et confirmations</h3>
            <p>Les notifications toast sont de petits messages qui apparaissent en haut de la page pour confirmer une action (succès, erreur, avertissement). Elles disparaissent automatiquement après quelques secondes. Sur mobile, elles sont visibles en haut de l'écran.</p>
        </div>

        <div class="faq-item">
            <h3>🌐 Installer l'application et utiliser hors ligne (PWA)</h3>
            <p>Impact Emploi est une Progressive Web App (PWA). Votre navigateur télécharge automatiquement la plateforme.<br>
            Pour installer l'application, suivez les instructions selon votre navigateur (voir plus haut).<br>
            Même sans connexion, vous pouvez consulter les offres d'emploi téléchargées. Les actions qui nécessitent une mise à jour (candidature, création de compte) nécessitent Internet.<br>
            Si l'icône ou le logo ne s'affiche pas correctement, contactez le support.</p>
        </div>

        <div class="faq-item">
            <h3>Puis-je publier plusieurs offres?</h3>
            <p>Oui! Vous pouvez publier autant d'offres que vous le souhaitez. Chaque offre apparaîtra sur la plateforme et sera disponible pour les candidats.</p>
        </div>

        <div class="faq-item">
            <h3>Comment me connecter?</h3>
            <p>Cliquez sur "Connexion" en haut de la page, entrez votre email et votre mot de passe, puis cliquez sur "Se connecter". Si vous n'avez pas de compte, créez-en un en cliquant sur "S'inscrire".</p>
        </div>

        <div class="faq-item">
            <h3>J'ai oublié mon mot de passe, comment faire?</h3>
            <p>Actuellement, vous devez contacter l'administrateur du site. Utilisez le formulaire de contact (disponible en bas de page) pour demander une réinitialisation de mot de passe.</p>
        </div>

        <div class="faq-item">
            <h3>Comment modifier mes informations de contact?</h3>
            <p>Allez dans votre profil (connecté) et accédez à la section "Mettre à jour mes coordonnées". Modifiez votre numéro de téléphone et cochez "WhatsApp" si applicable.</p>
        </div>

        <div class="faq-item">
            <h3>Mon offre est-elle visible à tous?</h3>
            <p>Oui, toutes les offres publiées sont visibles sur la page d'accueil pour tous les visiteurs (connectés ou non). C'est ainsi que les candidats peuvent vous découvrir.</p>
        </div>

        <div class="faq-item">
            <h3>Comment supprimer mon compte?</h3>
            <p>Pour des raisons de sécurité, contactez l'administrateur via le formulaire de contact pour demander la suppression de votre compte.</p>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="help-support">
        <h2>📧 Besoin de plus d'aide?</h2>
        <p>Si vous ne trouvez pas la réponse à votre question, n'hésitez pas à <a href="index.php" class="link">contacter le support</a> ou consultez notre page <a href="index.php" class="link">À propos</a>.</p>
        <p style="margin-top: 15px; color: var(--secondary); font-size: 0.95rem;">Impact Emploi — Plateforme d'emploi locale et communautaire</p>
    </div>
</div>

<!-- Message PWA/app mobile -->

<div id="pwa-prompt" style="display:none; background: #f0f9ff; border: 2px solid #0284c7; color: #222; padding: 18px; border-radius: 10px; margin: 30px auto; max-width: 500px; text-align: center; font-size: 1.1rem;">
    <span id="pwa-message">📲 Pour une expérience optimale, installez Impact Emploi sur votre téléphone !<br>
    <span style="font-size:0.95em; color:var(--secondary);">Selon votre navigateur, l'option <b>Installer l'application</b> ou <b>Ajouter à l'écran d'accueil</b> peut apparaître dans le menu <b>⋮</b> ou <b>Partager</b>. Si ce n'est pas le cas, consultez la documentation de votre navigateur.</span></span><br><br>
    <button id="pwa-install-btn" style="display:none;background:#0284c7;color:#fff;border:none;padding:10px 22px;border-radius:6px;font-size:1em;cursor:pointer;">Installer l'application</button>
    <span id="pwa-fallback" style="display:none;font-size:0.98em;color:#0284c7;"></span>
</div>

<script>
// Gestion native de l'installation PWA (beforeinstallprompt) + fallback
let deferredPrompt = null;
const pwaPrompt = document.getElementById('pwa-prompt');
const installBtn = document.getElementById('pwa-install-btn');
const fallbackMsg = document.getElementById('pwa-fallback');
const pwaMessage = document.getElementById('pwa-message');

function isStandalone() {
    return (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true);
}

function showFallback() {
    fallbackMsg.style.display = 'block';
    fallbackMsg.innerHTML = 'Ouvrez le menu <b>Partager</b> de votre navigateur puis choisissez <b>"Ajouter à l\'écran d\'accueil"</b>.';
}

window.addEventListener('DOMContentLoaded', function() {
    if (isStandalone()) {
        // Déjà installée, ne rien afficher
        pwaPrompt.style.display = 'none';
        return;
    }
    // Si mobile
    if (window.matchMedia('(max-width: 800px)').matches) {
        pwaPrompt.style.display = 'block';
    }
    // Fallback si beforeinstallprompt ne se déclenche pas
    setTimeout(function() {
        if (!deferredPrompt && pwaPrompt.style.display === 'block') {
            installBtn.style.display = 'none';
            showFallback();
        }
    }, 3500);
});

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (window.matchMedia('(max-width: 800px)').matches) {
        pwaPrompt.style.display = 'block';
        installBtn.style.display = 'inline-block';
        fallbackMsg.style.display = 'none';
    }
});

if (installBtn) {
    installBtn.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                pwaMessage.innerHTML = '✅ Merci ! Application installée sur votre appareil.';
                installBtn.style.display = 'none';
                fallbackMsg.style.display = 'none';
            } else {
                pwaMessage.innerHTML = 'Installation annulée. Vous pourrez réessayer plus tard.';
            }
            pwaPrompt.style.display = 'block';
            deferredPrompt = null;
        }
    });
}
</script>

<!-- CSS personnalisé pour la page d'aide -->
<style>
    .help-page { max-width: 900px; }
    
    .help-header {
        text-align: center;
        margin-bottom: 50px;
        padding: 30px 20px;
        background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
        border-radius: 12px;
        border: 2px solid #e0f2fe;
    }

    .help-header h1 {
        font-size: 2rem;
        color: var(--dark);
        margin: 0 0 15px 0;
    }

    .help-header p {
        font-size: 1.05rem;
        color: var(--secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    .help-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .help-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .help-card:hover {
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
        transform: translateY(-2px);
    }

    .help-card-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }

    .help-card h2 {
        font-size: 1.3rem;
        color: var(--dark);
        margin: 0 0 20px 0;
        border-bottom: 3px solid var(--primary);
        padding-bottom: 10px;
    }

    .help-item {
        margin-bottom: 25px;
    }

    .help-item h3 {
        font-size: 1rem;
        color: var(--primary);
        margin: 0 0 8px 0;
        font-weight: 700;
    }

    .help-item p {
        margin: 0;
        color: var(--secondary);
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .help-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 40px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .help-section h2 {
        font-size: 1.5rem;
        color: var(--dark);
        margin: 0 0 25px 0;
        border-bottom: 3px solid var(--primary);
        padding-bottom: 12px;
    }

    .faq-item {
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e5e7eb;
    }

    .faq-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .faq-item h3 {
        font-size: 1.1rem;
        color: var(--primary);
        margin: 0 0 10px 0;
        font-weight: 700;
    }

    .faq-item p {
        margin: 0;
        color: var(--secondary);
        line-height: 1.6;
    }

    .help-support {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 2px solid #0284c7;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
    }

    .help-support h2 {
        margin: 0 0 15px 0;
        color: var(--dark);
        font-size: 1.3rem;
    }

    .help-support p {
        margin: 0;
        color: var(--secondary);
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .help-header h1 { font-size: 1.5rem; }
        .help-header p { font-size: 1rem; }
        .help-grid { grid-template-columns: 1fr; gap: 20px; }
        .help-card, .help-section { padding: 20px; }
        .faq-item { margin-bottom: 18px; padding-bottom: 18px; }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
