<?php
require_once 'includes/header.php';
?>

<div class="container help-page">
    <div style="margin-bottom: 40px;">
        <a href="index.php" class="back-link">← Retour à l'accueil</a>
    </div>

    <div class="help-header">
        <h1>❓ Centre d'aide et guides</h1>
        <p>Bienvenue dans le centre d'aide d'Impact Emploi. Trouvez les réponses à vos questions et apprenez à naviguer sur la plateforme.</p>
    </div>

    <div class="help-grid">
        <!-- Section Chercheur d'emploi -->
        <div class="help-card">
            <div class="help-card-icon">💼</div>
            <h2>Pour les chercheurs d'emploi</h2>
            
            <div class="help-item">
                <h3>Consulter les offres d'emploi</h3>
                <p>Accédez à la page d'accueil pour parcourir toutes les offres d'emploi disponibles. Cliquez sur une offre pour voir les détails complets, la description du poste et les coordonnées du recruteur.</p>
            </div>

            <div class="help-item">
                <h3>Créer un compte</h3>
                <p>Cliquez sur "S'inscrire" en haut de la page. Remplissez vos informations (nom, prénom, email) et choisissez le rôle "Chercheur d'emploi". Vérifiez votre email après l'inscription.</p>
            </div>

            <div class="help-item">
                <h3>Gérer mon profil</h3>
                <p>Une fois connecté, accédez à votre profil pour ajouter votre numéro de téléphone. Si vous utilisez WhatsApp, indiquez-le pour que les recruteurs puissent vous contacter facilement.</p>
            </div>

            <div class="help-item">
                <h3>Contacter un recruteur</h3>
                <p>Sur chaque offre, vous trouverez le numéro de téléphone et les coordonnées du recruteur. Appelez ou écrivez directement pour postuler ou poser des questions.</p>
            </div>
        </div>

        <!-- Section Recruteur -->
        <div class="help-card">
            <div class="help-card-icon">👔</div>
            <h2>Pour les recruteurs</h2>
            
            <div class="help-item">
                <h3>Publier une offre d'emploi</h3>
                <p>Connectez-vous à votre compte recruteur. Cliquez sur "Publier une offre" et remplissez le formulaire avec le titre du poste, la description, le type de contrat et les qualifications requises.</p>
            </div>

            <div class="help-item">
                <h3>Gérer mes offres</h3>
                <p>Sur votre profil, consultez la liste de toutes vos offres publiées. Vous pouvez voir la date de publication et accéder à chaque offre pour vérifier les informations.</p>
            </div>

            <div class="help-item">
                <h3>Être contacté par les candidats</h3>
                <p>Assurez-vous que votre numéro de téléphone est à jour dans votre profil. Les candidats pourront vous contacter directement via le numéro affiché sur vos offres.</p>
            </div>

            <div class="help-item">
                <h3>Améliorer votre profil</h3>
                <p>Activez WhatsApp dans votre profil pour permettre aux candidats de vous contacter aussi par message. Plus votre profil est complet, plus les candidats seront enclins à postuler.</p>
            </div>
        </div>
    </div>

    <!-- Questions communes -->
    <div class="help-section">
        <h2>❔ Questions fréquemment posées</h2>

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
            <h3>My offre est-elle visible à tous?</h3>
            <p>Oui, toutes les offres publiées sont visibles sur la page d'accueil pour tous les visiteurs (connectés ou non). C'est ainsi que les candidats peuvent vous découvrir.</p>
        </div>

        <div class="faq-item">
            <h3>Comment supprimer mon compte?</h3>
            <p>Pour des raisons de sécurité, contactez l'administrateur via le formulaire de contact pour demander la suppression de votre compte.</p>
        </div>

        <div class="faq-item">
            <h3>Puis-je publier plusieurs offres?</h3>
            <p>Oui! Vous pouvez publier autant d'offres que vous le souhaitez. Chaque offre apparaîtra sur la plateforme et sera disponible pour les candidats.</p>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="help-support">
        <h2>📧 Besoin de plus d'aide?</h2>
        <p>Si vous ne trouvez pas la réponse à votre question, n'hésitez pas à <a href="index.php" class="link">contacter le support</a> ou consultez notre page <a href="index.php" class="link">À propos</a>.</p>
        <p style="margin-top: 15px; color: var(--secondary); font-size: 0.95rem;">Impact Emploi — Plateforme d'emploi locale et communautaire</p>
    </div>
</div>

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
