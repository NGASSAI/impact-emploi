<?php
/**
 * Système de gestion centralisé des messages (alertes)
 * Affiche les messages de succès, erreur, info sous forme de bannière HTML
 */

function displayAlerts() {
    $html = '';
    
    // Message de succès (GET ?success=motcle)
    if (isset($_GET['success'])) {
        $type = htmlspecialchars($_GET['success']);
        $messages = [
            'postule' => 'Candidature envoyée avec succès ! 🎉',
            'candidature_supprimee' => 'Candidature supprimée ✓',
            'offre_supprimee' => 'Offre supprimée ✓',
            'profil_mis_a_jour' => 'Profil mis à jour ✓',
        ];
        
        $message = $messages[$type] ?? 'Action réussie !';
        $html .= "
        <div class='alert alert-success' role='alert'>
            <strong>✓ Succès :</strong> {$message}
        </div>";
    }
    
    // Message d'erreur (GET ?error=motcle)
    if (isset($_GET['error'])) {
        $type = htmlspecialchars($_GET['error']);
        $messages = [
            'connexion' => 'Vous devez être connecté pour cette action.',
            'fichier' => 'Erreur lors du téléchargement du fichier. Vérifiez que le fichier est valide.',
            'permissions' => 'Erreur de permissions. Impossible d\'enregistrer le fichier.',
            'non_trouve' => 'L\'offre ou la candidature n\'a pas été trouvée.',
            'fichier_trop_gros' => 'Le fichier est trop volumineux (max 5MB).',
            'format_pdf' => 'Seuls les fichiers PDF valides sont acceptés.',
            'deja_postule' => 'Vous avez déjà postulé à cette offre.',
            'dossier' => 'Erreur : impossible de créer le dossier de téléchargement.',
            'base_donnees' => 'Erreur de base de données. Veuillez réessayer.',
        ];
        
        $message = $messages[$type] ?? 'Une erreur s\'est produite.';
        $html .= "
        <div class='alert alert-error' role='alert'>
            <strong>✗ Erreur :</strong> {$message}
        </div>";
    }
    
    // Message d'info (GET ?info=motcle)
    if (isset($_GET['info'])) {
        $type = htmlspecialchars($_GET['info']);
        $messages = [
            'connexion_requise' => 'Connectez-vous pour continuer.',
        ];
        
        $message = $messages[$type] ?? 'Information';
        $html .= "
        <div class='alert alert-info' role='alert'>
            <strong>ℹ️ Info :</strong> {$message}
        </div>";
    }
    
    return $html;
}

?>
