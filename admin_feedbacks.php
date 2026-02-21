<?php
require_once 'config.php';

if(!is_logged_in()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

// Vérifier que c'est un admin
if($_SESSION['auth_role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Récupérer tous les feedbacks avec les infos utilisateur
$sql = "SELECT f.*, u.nom, u.email FROM feedbacks f 
        LEFT JOIN users u ON f.user_id = u.id 
        ORDER BY f.date_envoi DESC";
$feedbacks = $pdo->query($sql)->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0;">
    <h1 style="font-size: 2rem; color: var(--primary); margin-bottom: 30px;">
        💬 Gestion des Feedbacks
    </h1>

    <?php if(empty($feedbacks)): ?>
        <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
            <p style="color: #999; font-size: 1.1rem;">Aucun feedback reçu pour le moment 📭</p>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach($feedbacks as $fb): ?>
                <div class="card" style="border-left: 4px solid var(--primary); overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div>
                            <h2 style="margin: 0 0 5px 0; color: var(--primary); font-size: 1.2rem;">
                                📌 <?php echo htmlspecialchars($fb['sujet']); ?>
                            </h2>
                            <p style="margin: 0; color: #999; font-size: 0.9rem;">
                                👤 <?php echo htmlspecialchars($fb['nom'] ?? 'Utilisateur anonyme'); ?> 
                                <?php if($fb['email']): ?>
                                    (<?php echo htmlspecialchars($fb['email']); ?>)
                                <?php endif; ?>
                            </p>
                        </div>
                        <span style="background: var(--primary); color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; white-space: nowrap;">
                            🕐 <?php echo date('d/m/Y H:i', strtotime($fb['date_envoi'])); ?>
                        </span>
                    </div>

                    <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                        <p style="margin: 0; color: #333; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($fb['message'])); ?>
                        </p>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <a href="mailto:<?php echo urlencode($fb['email'] ?? ''); ?>" class="btn btn-outline" style="padding: 10px 15px; text-decoration: none;">
                            ✉️ Répondre
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
