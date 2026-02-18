<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    <div>
        <?php
        $is_admin_link = false;
        if (isset($_SESSION['user_id'])) {
            try {
                // récupérer le prénom et nom de l'utilisateur
                $stmt = $db->prepare('SELECT prenom, nom, email, role FROM users WHERE id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $row = $stmt->fetch();
                if ($row) {
                    $initiales = strtoupper($row['prenom'][0] ?? '') . strtoupper($row['nom'][0] ?? '');
                    $nom_complet = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
                    echo "<span class='user-badge' title='$nom_complet'>$initiales</span>";
                    // Montrer le lien admin si role == 'admin' ou email correspond à DEFAULT_ADMIN_EMAIL
                    if ((!empty($row['role']) && $row['role'] === 'admin') || (defined('DEFAULT_ADMIN_EMAIL') && $row['email'] === DEFAULT_ADMIN_EMAIL)) {
                        $is_admin_link = true;
                    }
                }
            } catch (Exception $e) { /* silent */ }
        }
        ?>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($is_admin_link): ?>
                <a href="admin_dashboard.php">📊 Tableau de Bord</a>
            <?php endif; ?>
            <a href="mon_espace.php">Mon Espace</a>
            <a href="profil.php">Mon Profil</a>
            <a href="suggestions.php">Suggestions</a>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <div class="nav-auth">
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">S'inscrire</a>
            </div>
        <?php endif; ?>
    </div>
<nav>
    <a href="index.php">Impact Emploi</a>
    <div>
        <?php
        if (isset($_SESSION['user_id'])) {
            try {
                // récupérer le prénom et nom de l'utilisateur
                $stmt = $db->prepare('SELECT prenom, nom FROM users WHERE id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $row = $stmt->fetch();
                if ($row) {
                    $initiales = strtoupper($row['prenom'][0] ?? '') . strtoupper($row['nom'][0] ?? '');
                    $nom_complet = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
                    echo "<span class='user-badge' title='$nom_complet'>$initiales</span>";
                }
            } catch (Exception $e) { /* silent */ }
        }
        ?>

        <a href="index.php">🏠 Accueil</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['user_id'] == 1): ?>
                <a href="admin_dashboard.php">📊 Tableau de Bord</a>
            <?php endif; ?>
            <a href="mon_espace.php">Mon Espace</a>
            <a href="profil.php">Mon Profil</a>
            <a href="suggestions.php">Suggestions</a>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <div class="nav-auth">
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">S'inscrire</a>
            </div>
        <?php endif; ?>
    </div>
 </nav>
 
 <div class="container">