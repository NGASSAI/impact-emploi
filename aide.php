<?php
require_once 'includes/csrf.php';
generateCSRFToken();
require_once 'includes/header.php';
?>

<div class="help-page">
    <!-- Back Link -->
    <div class="help-back">
        <a href="index.php" class="back-link">
            <span>←</span> Retour à l'accueil
        </a>
    </div>

    <!-- Header -->
    <div class="help-hero">
        <div class="help-hero-icon">❓</div>
        <h1>Centre d'aide Impact Emploi</h1>
        <p>Bienvenue ! Trouvez ici toutes les réponses à vos questions sur l'utilisation de la plateforme.</p>
    </div>

    <!-- Quick Search -->
    <div class="help-search-box">
        <input type="text" id="helpSearch" placeholder="🔍 Rechercher une信息..." onkeyup="filterHelp()">
    </div>

    <!-- Main Grid -->
    <div class="help-grid">
        <!-- Section Chercheur d'emploi -->
        <div class="help-card" data-category="candidat">
            <div class="help-card-header">
                <span class="help-card-icon">💼</span>
                <h2>Pour les chercheurs d'emploi</h2>
            </div>
            
            <div class="help-item">
                <h3>🔍 Recherche d'offres</h3>
                <p>Utilisez les filtres sur la page d'accueil pour trouver l'offre parfaite :</p>
                <ul class="help-list">
                    <li><strong>Métier :</strong> Entrez un mot-clé</li>
                    <li><strong>Localité :</strong> Filtrez par ville/région</li>
                    <li><strong>Type de contrat :</strong> CDI, CDD, Stage, Freelance</li>
                </ul>
            </div>

            <div class="help-item">
                <h3>📤 Partager une offre</h3>
                <p>Partagez les offres qui vous intéressent :</p>
                <ul class="help-list">
                    <li><strong>💬 WhatsApp :</strong> Envoyez au recruteur ou à vos amis</li>
                    <li><strong>📧 Email :</strong> Contact direct par email</li>
                    <li><strong>📋 Copier le lien :</strong> Partagez sur les réseaux</li>
                    <li><strong>📤 Partager :</strong> Menu natif de votre téléphone</li>
                </ul>
            </div>

            <div class="help-item">
                <h3>📱 Navigation mobile</h3>
                <p>Cliquez sur ≡ (trois lignes) en haut à gauche pour ouvrir le menu. Le menu se ferme automatiquement après un clic sur un lien.</p>
            </div>

            <div class="help-item">
                <h3>👤 Créer un compte</h3>
                <p>Cliquez sur "S'inscrire", remplissez vos informations (nom, prénom, email, téléphone) et choisissez votre rôle.</p>
            </div>

            <div class="help-item">
                <h3>⚙️ Gérer mon profil</h3>
                <p>Accédez à votre profil pour modifier vos informations, ajouter une photo et cochez "WhatsApp" pour être contacté par ce moyen.</p>
            </div>

            <div class="help-item">
                <h3>🔔 Notifications</h3>
                <p>Des messages toast apparaissent en haut de l'écran pour confirmer vos actions. Ils disparaissent automatiquement.</p>
            </div>
        </div>

        <!-- Section Recruteur -->
        <div class="help-card" data-category="recruteur">
            <div class="help-card-header">
                <span class="help-card-icon">👔</span>
                <h2>Pour les recruteurs</h2>
            </div>

            <div class="help-item">
                <h3>📝 Publier une offre</h3>
                <p>Connectez-vous, cliquez sur "Publier une offre" et remplissez le formulaire avec titre, description, type de contrat, lieu et salaire.</p>
            </div>

            <div class="help-item">
                <h3>📋 Gérer mes offres</h3>
                <p>Consultez, modifiez ou supprimez vos offres depuis votre profil.</p>
            </div>

            <div class="help-item">
                <h3>📞 Être contacté</h3>
                <p>Assurez-vous que vos coordonnées (téléphone, email) sont à jour dans votre profil pour recevoir les candidatures.</p>
            </div>

            <div class="help-item">
                <h3>💬 Activer WhatsApp</h3>
                <p>Activez WhatsApp dans votre profil pour permettre aux candidats de vous contacter par message.</p>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="help-section">
        <h2 class="section-title">❔ Questions fréquentes</h2>

        <div class="faq-grid">
            <div class="faq-item" data-search="filtre recherche mot-clé">
                <div class="faq-icon">🔍</div>
                <div class="faq-content">
                    <h3>Comment utiliser les filtres de recherche ?</h3>
                    <p>Sur la page d'accueil, entrez un mot-clé, sélectionnez une localité et/ou un type de contrat, puis cliquez sur "Chercher".</p>
                </div>
            </div>

            <div class="faq-item" data-search="partager offre whatsapp email">
                <div class="faq-icon">📤</div>
                <div class="faq-content">
                    <h3>Comment partager une offre ?</h3>
                    <p>En bas de chaque offre : boutons WhatsApp, Email, Copier le lien, et Partager.</p>
                </div>
            </div>

            <div class="faq-item" data-search="mobile menu navigation">
                <div class="faq-icon">📱</div>
                <div class="faq-content">
                    <h3>Navigation sur mobile</h3>
                    <p>Cliquez sur ≡ en haut à gauche. Le menu s'affiche en superposition.</p>
                </div>
            </div>

            <div class="faq-item" data-search="plusieurs offres publier">
                <div class="faq-icon">✨</div>
                <div class="faq-content">
                    <h3>Puis-je publier plusieurs offres ?</h3>
                    <p>Oui ! Vous pouvez publier autant d'offres que vous souhaitez.</p>
                </div>
            </div>

            <div class="faq-item" data-search="connexion login mot de passe">
                <div class="faq-icon">🔑</div>
                <div class="faq-content">
                    <h3>Comment me connecter ?</h3>
                    <p>Cliquez sur "Connexion", entrez email et mot de passe. Créez un compte si nécessaire.</p>
                </div>
            </div>

            <div class="faq-item" data-search="mot de passe oublié">
                <div class="faq-icon">🔄</div>
                <div class="faq-content">
                    <h3>J'ai oublié mon mot de passe</h3>
                    <p>Contactez l'administrateur via le formulaire de contact pour une réinitialisation.</p>
                </div>
            </div>

            <div class="faq-item" data-search="modifier profil contact">
                <div class="faq-icon">✏️</div>
                <div class="faq-content">
                    <h3>Modifier mes informations</h3>
                    <p>Allez dans votre profil → "Mettre à jour mes coordonnées".</p>
                </div>
            </div>

            <div class="faq-item" data-search="offre visible publik">
                <div class="faq-icon">👁️</div>
                <div class="faq-content">
                    <h3>Mon offre est-elle visible ?</h3>
                    <p>Oui, toutes les offres sont visibles par tous les visiteurs (connectés ou non).</p>
                </div>
            </div>

            <div class="faq-item" data-search="supprimer compte">
                <div class="faq-icon">🗑️</div>
                <div class="faq-content">
                    <h3>Supprimer mon compte</h3>
                    <p>Contactez l'administrateur via le formulaire de contact.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="help-support">
        <div class="support-icon">📧</div>
        <h2>Besoin d'aide supplémentaire ?</h2>
        <p>Vous ne trouvez pas la réponse à votre question ?</p>
        <div class="support-links">
            <a href="index.php" class="btn btn-primary">📬 Contacter le support</a>
            <a href="index.php" class="btn btn-outline">ℹ️ À propos</a>
        </div>
        <p class="footer-text">Impact Emploi — Plateforme d'emploi locale et communautaire</p>
    </div>
</div>

<script>
// Search/Filter Function
function filterHelp() {
    const searchInput = document.getElementById('helpSearch').value.toLowerCase();
    
    // Filter help cards
    document.querySelectorAll('.help-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchInput) ? 'block' : 'none';
    });
    
    // Filter FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        const searchData = item.getAttribute('data-search') || '';
        const text = item.textContent.toLowerCase();
        if (searchData.includes(searchInput) || text.includes(searchInput)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<!-- CSS pour la page d'aide -->
<style>
    .help-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    /* Back Link */
    .help-back {
        margin-bottom: 1.5rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .back-link:hover {
        color: var(--secondary);
        transform: translateX(-5px);
    }

    /* Hero Section */
    .help-hero {
        text-align: center;
        padding: 2.5rem 1.5rem;
        background: linear-gradient(135deg, var(--primary) 0%, #004080 100%);
        border-radius: 16px;
        color: white;
        margin-bottom: 2rem;
    }

    .help-hero-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .help-hero h1 {
        margin: 0 0 12px 0;
        font-size: 1.8rem;
    }

    .help-hero p {
        margin: 0;
        opacity: 0.9;
        font-size: 1.05rem;
    }

    /* Search Box */
    .help-search-box {
        margin-bottom: 2rem;
    }

    .help-search-box input {
        width: 100%;
        padding: 14px 20px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 1rem;
        transition: var(--transition);
    }

    .help-search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 82, 163, 0.1);
    }

    /* Help Grid */
    .help-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 25px;
        margin-bottom: 3rem;
    }

    .help-card {
        background: white;
        border-radius: 14px;
        padding: 25px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }

    .help-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-3px);
    }

    .help-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid var(--primary);
    }

    .help-card-icon {
        font-size: 2rem;
    }

    .help-card-header h2 {
        margin: 0;
        font-size: 1.25rem;
        color: var(--text-primary);
    }

    .help-item {
        margin-bottom: 22px;
    }

    .help-item:last-child {
        margin-bottom: 0;
    }

    .help-item h3 {
        font-size: 1rem;
        color: var(--primary);
        margin: 0 0 8px 0;
        font-weight: 600;
    }

    .help-item p {
        margin: 0 0 8px 0;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .help-list {
        margin: 8px 0 0 0;
        padding-left: 18px;
        color: var(--text-secondary);
    }

    .help-list li {
        margin-bottom: 5px;
        line-height: 1.5;
    }

    /* FAQ Section */
    .help-section {
        background: white;
        border-radius: 14px;
        padding: 30px;
        margin-bottom: 2.5rem;
        box-shadow: var(--shadow-md);
    }

    .section-title {
        text-align: center;
        font-size: 1.5rem;
        color: var(--text-primary);
        margin: 0 0 30px 0;
        padding-bottom: 15px;
        border-bottom: 3px solid var(--secondary);
    }

    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .faq-item {
        display: flex;
        gap: 15px;
        padding: 20px;
        background: var(--bg-secondary);
        border-radius: 10px;
        transition: var(--transition);
    }

    .faq-item:hover {
        background: #f0f9ff;
        transform: translateX(5px);
    }

    .faq-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .faq-content h3 {
        font-size: 0.95rem;
        color: var(--primary);
        margin: 0 0 6px 0;
        font-weight: 600;
    }

    .faq-content p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Support Section */
    .help-support {
        text-align: center;
        padding: 2.5rem;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 2px solid #0284c7;
        border-radius: 14px;
    }

    .support-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .help-support h2 {
        margin: 0 0 10px 0;
        color: var(--text-primary);
        font-size: 1.3rem;
    }

    .help-support > p {
        margin: 0 0 20px 0;
        color: var(--text-secondary);
    }

    .support-links {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #003d7a;
    }

    .btn-outline {
        border: 2px solid var(--primary);
        color: var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .footer-text {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .help-page {
            padding: 1rem;
        }

        .help-hero {
            padding: 2rem 1rem;
        }

        .help-hero h1 {
            font-size: 1.4rem;
        }

        .help-grid {
            grid-template-columns: 1fr;
        }

        .help-card {
            padding: 20px;
        }

        .help-section {
            padding: 20px;
        }

        .faq-grid {
            grid-template-columns: 1fr;
        }

        .support-links {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>

