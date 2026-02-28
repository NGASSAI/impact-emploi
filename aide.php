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
        <h1>Besoin d'aide ?</h1>
        <p>Trouvez ici les réponses à vos questions sur l'utilisation d'Impact Emploi.</p>
    </div>

    <!-- Quick Search -->
    <div class="help-search-box">
        <input type="text" id="helpSearch" placeholder="🔍 Rechercher une réponse..." onkeyup="filterHelp()">
    </div>

    <!-- Main Grid -->
    <div class="help-grid">
        <!-- Section Chercheur d'emploi -->
        <div class="help-card" data-category="candidat">
            <div class="help-card-header">
                <span class="help-card-icon">💼</span>
                <h2>Pour trouver un emploi</h2>
            </div>
            
            <div class="help-item">
                <h3>🔍 Chercher une offre</h3>
                <p>Utilisez la recherche sur la page d'accueil avec des mots-clés et filtres.</p>
            </div>

            <div class="help-item">
                <h3>📱 Utiliser sur mobile</h3>
                <p>Le site s'adapte automatiquement à votre téléphone. Cliquez sur ☰ pour le menu.</p>
            </div>

            <div class="help-item">
                <h3>📤 Postuler à une offre</h3>
                <p>Cliquez sur une offre, puis sur "Envoyer ma candidature" avec votre CV.</p>
            </div>

            <div class="help-item">
                <h3>👤 Créer votre compte</h3>
                <p>Inscrivez-vous avec votre email, nom et choisissez "Candidat".</p>
            </div>

            <div class="help-item">
                <h3>📋 Suivre vos candidatures</h3>
                <p>Connectez-vous et consultez l'état de toutes vos candidatures.</p>
            </div>
        </div>

        <!-- Section Recruteur -->
        <div class="help-card" data-category="recruteur">
            <div class="help-card-header">
                <span class="help-card-icon">👔</span>
                <h2>Pour recruter</h2>
            </div>
            
            <div class="help-item">
                <h3>📝 Publier une offre</h3>
                <p>Connectez-vous, cliquez sur "Publier une offre" et remplissez le formulaire.</p>
            </div>

            <div class="help-item">
                <h3>📊 Gérer mes offres</h3>
                <p>Voyez qui a postulé et modifiez vos offres depuis votre espace.</p>
            </div>

            <div class="help-item">
                <h3>📞 Être contacté</h3>
                <p>Les candidats vous contacteront par email, téléphone ou WhatsApp.</p>
            </div>

            <div class="help-item">
                <h3>💬 Activer WhatsApp</h3>
                <p>Dans votre profil, cochez l'option WhatsApp pour être joignable facilement.</p>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="help-section">
        <h2 class="section-title">❔ Questions fréquentes</h2>

        <div class="faq-grid">
            <div class="faq-item" data-search="compte créer">
                <div class="faq-icon">👤</div>
                <div class="faq-content">
                    <h3>Comment créer un compte ?</h3>
                    <p>Cliquez sur "S'inscrire", remplissez vos informations et validez votre email.</p>
                </div>
            </div>

            <div class="faq-item" data-search="mot de passe oublié">
                <div class="faq-icon">🔄</div>
                <div class="faq-content">
                    <h3>J'ai oublié mon mot de passe</h3>
                    <p>Contactez le support par email pour réinitialiser votre mot de passe.</p>
                </div>
            </div>

            <div class="faq-item" data-search="modifier profil">
                <div class="faq-icon">✏️</div>
                <div class="faq-content">
                    <h3>Comment modifier mon profil ?</h3>
                    <p>Connectez-vous et allez dans "Mon profil" pour mettre à jour vos informations.</p>
                </div>
            </div>

            <div class="faq-item" data-search="supprimer compte">
                <div class="faq-icon">🗑️</div>
                <div class="faq-content">
                    <h3>Comment supprimer mon compte ?</h3>
                    <p>Contactez l'administrateur pour demander la suppression de votre compte.</p>
                </div>
            </div>

            <div class="faq-item" data-search="contact recruteur">
                <div class="faq-icon">📞</div>
                <div class="faq-content">
                    <h3>Comment contacter un recruteur ?</h3>
                    <p>Utilisez les coordonnées affichées sur l'offre (email, téléphone, WhatsApp).</p>
                </div>
            </div>

            <div class="faq-item" data-search="offre pas visible">
                <div class="faq-icon">👁️</div>
                <div class="faq-content">
                    <h3>Mon offre n'apparaît pas ?</h3>
                    <p>Vérifiez que votre offre est validée par l'administrateur.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="help-support">
        <div class="support-icon">💬</div>
        <h2>Besoin d'aide supplémentaire ?</h2>
        <p>Notre équipe est là pour vous aider !</p>
        <div class="support-links">
            <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="btn btn-primary">📧 Contacter le support</a>
            <a href="index.php" class="btn btn-outline">🏠 Retour à l'accueil</a>
        </div>
        <p class="footer-text">Impact Emploi — Plateforme d'emploi au Congo</p>
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
    max-width: 900px;
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
    padding: 2rem 1.5rem;
    background: linear-gradient(135deg, var(--primary) 0%, #004080 100%);
    border-radius: 12px;
    color: white;
    margin-bottom: 2rem;
}

.help-hero-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.help-hero h1 {
    margin: 0 0 10px 0;
    font-size: 1.8rem;
    font-weight: 600;
}

.help-hero p {
    margin: 0;
    opacity: 0.9;
    font-size: 1rem;
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
    box-shadow: 0 0 3px rgba(0, 82, 163, 0.1);
}

/* Help Grid */
.help-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 2rem;
}

.help-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.help-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.help-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--primary);
}

.help-card-icon {
    font-size: 1.8rem;
}

.help-card-header h2 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--text-primary);
    font-weight: 600;
}

.help-item {
    margin-bottom: 15px;
}

.help-item:last-child {
    margin-bottom: 0;
}

.help-item h3 {
    font-size: 1rem;
    color: var(--primary);
    margin: 0 0 6px 0;
    font-weight: 600;
}

.help-item p {
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.5;
}

/* FAQ Section */
.help-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.section-title {
    text-align: center;
    font-size: 1.4rem;
    color: var(--primary);
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--secondary);
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.faq-item {
    display: flex;
    gap: 12px;
    padding: 15px;
    background: var(--bg-secondary);
    border-radius: 8px;
    transition: var(--transition);
}

.faq-item:hover {
    background: #f0f9ff;
    transform: translateX(3px);
}

.faq-icon {
    font-size: 1.3rem;
    flex-shrink: 0;
}

.faq-content h3 {
    font-size: 0.95rem;
    color: var(--primary);
    margin: 0 0 5px 0;
    font-weight: 600;
}

.faq-content p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Support Section */
.help-support {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.support-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.help-support h2 {
    margin: 0 0 12px 0;
    font-size: 1.3rem;
    color: var(--text-primary);
}

.help-support > p {
    margin: 0 0 15px 0;
    color: var(--text-secondary);
    font-size: 1rem;
}

.support-links {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    border-radius: 6px;
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
    transform: translateY(-1px);
}

.btn-outline {
    border: 2px solid var(--primary);
    color: var(--primary);
    background: white;
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
    
    .help-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .help-card {
        padding: 15px;
    }
    
    .help-card-header {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .faq-grid {
        grid-template-columns: 1fr;
    }
    
    .faq-item {
        flex-direction: column;
        text-align: center;
        padding: 12px;
    }
    
    .support-links {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
