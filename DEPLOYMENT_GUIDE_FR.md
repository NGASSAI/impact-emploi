<!-- GUIDE DE DÉPLOIEMENT SÉCURISÉ - Impact Emploi -->

# 📋 Guide de Déploiement vers InfinityFREE

## ✅ État Actuel (20 février 2026)

### Compatibilité Vérifiée:
- **PHP**: 8.0.30 ✓
- **PDO MySQL**: Activé ✓
- **Tables BD**: 5/5 présentes ✓
- **Colonnes critiques**: Toutes présentes ✓
- **Fichiers**: 100% présents ✓
- **Upload dirs**: 3/3 créés ✓

### Données Existantes:
- Users: 6 enregistrements
- Jobs: 2 offres
- Candidatures: 1 candidature
- Feedbacks: 1 feedback

---

## 🚀 ÉTAPES DE DÉPLOIEMENT (NON-DESTRUCTIF)

### AVANT TOUTE MODIFICATION:

1. **Sauvegarde InfinityFREE BD** (CRITIQUE)
   ```
   - Via cPanel: phpmyadmin > Export base de données
   - Garder le fichier SQL en sécurité
   ```

2. **Sauvegarde des fichiers origin** (CRITIQUE)
   ```
   - Via FTP: Télécharger les fichiers importants:
     - config.php (pour les identifiants DB)
     - uploads/ (dossiers avec fichiers utilisateurs)
   - Créer un dossier backup_AAAAMMJJ
   ```

### ÉTAPES DE MISE À JOUR:

1. **Fichiers à Matcher PRIORITAIRE** (Copier exactement)
   ```
   À copier en premier (core):
   - config.php (adapter BASE_URL + identifiants DB)
   - includes/header.php
   - includes/footer.php
   - assets/css/style.css
   ```

2. **Fichiers à Uploader** (Tous les fichiers .php du dossier)
   ```
   Uploader via FTP:
   - index.php
   - job_detail.php ⭐ NOUVEAU
   - candidat_dashboard.php ⭐ NOUVEAU
   - login.php, register.php
   - recruteur_dashboard.php (MODIFIÉ)
   - admin_dashboard.php (MODIFIÉ)
   - feedback.php ⭐ NOUVEAU
   - admin_feedbacks.php ⭐ NOUVEAU
   - chat.php
   - et tous les autres .php
   ```

3. **Répertoires à Vérifier** (NE PAS EFFACER)
   ```
   ✓ uploads/profiles/     (Garder les photos)
   ✓ uploads/cv/          (Garder les CV)
   ✓ uploads/jobs/        (Garder les images offres)
   
   → Créer s'il n'existe pas: uploads/jobs/
   ```

4. **Migration BD** (adapter à InfinityFREE)
   ```
   Si tables manquent, exécuter dans phpmyadmin:
   
   -- Activity Logs
   CREATE TABLE IF NOT EXISTS activity_logs (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT,
       action VARCHAR(100),
       description TEXT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
   );
   
   -- Vérifier colonnes users
   ALTER TABLE users ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL;
   
   -- Vérifier colonnes jobs
   ALTER TABLE jobs ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL;
   ALTER TABLE jobs ADD COLUMN image_offre VARCHAR(255) NULL;
   
   -- Vérifier colonnes candidatures
   ALTER TABLE candidatures ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL;
   ```

---

## 🎨 VÉRIFICATION DU DESIGN APRÈS DÉPLOIEMENT

### Tests à faire sur InfinityFREE:

1. **Desktop (1920px)**
   - [ ] Header navigation ok
   - [ ] Layout grid responsive
   - [ ] Images chargent
   - [ ] Formulaires alignés

2. **Tablet (768px)**
   - [ ] Menu hamburger actif
   - [ ] Grille 1 colonne
   - [ ] Boutons cliquables
   - [ ] Images redimensionnées

3. **Mobile iPhone SE (375px)** ⭐ CRITIQUE
   - [ ] Pas de dépassement horizontal
   - [ ] Nav hamburger fonctionnel
   - [ ] Formulaires lisibles
   - [ ] Images adaptées
   - [ ] Footer visible

4. **Pages à tester spécifiquement**:
   - index.php (offres en vedette)
   - job_detail.php (nouvelle page)
   - candidat_dashboard.php (nouvelle page)
   - recruteur_dashboard.php
   - feedback.php (nouvelle page)

---

## ⚠️ POINTS CRITIQUES À NE PAS OUBLIER

1. **config.php** - Adapter:
   ```php
   // Vérifier que BASE_URL correspond à votre domaine InfinityFREE
   // EX: $host = 'impact-emploi.infinity.free' ou votre domaine
   ```

2. **Permissions fichiers** (FTP):
   ```
   uploads/        → 755
   uploads/*       → 755
   config.php      → 644
   ```

3. **DNS InfinityFREE**:
   - Attendre 3 jours pour activation
   - Pendant ce temps: accès via téléphone ✓
   - Test PC possible avec HOSTS file (temporaire)

4. **Sessions PHP**:
   - Vérifier que sessions fonctionnent
   - Tester login/logout
   - Vérifier cookies acceptés

---

## 🔄 EN CAS DE PROBLÈME

### Si le design est cassé:
1. Vérifier inclusion CSS: `<link href="<?php echo BASE_URL; ?>/assets/css/style.css">`
2. Vérifier BASE_URL dans config.php
3. Vérifier permissions fichiers
4. Vider cache navigateur (Ctrl+Shift+Suppr)

### Si les images ne chargent pas:
1. Vérifier uploads/ créé
2. Vérifier permissions 755
3. Vérifier chemin correct: `/uploads/jobs/`, `/uploads/profiles/`, `/uploads/cv/`

### Si BD ne connecte pas:
1. Vérifier identifiants dans config.php
2. Vérifier IP whitelisted sur InfinityFREE
3. Vérifier nom BD correct (format: infinityid_nomsdb)

---

## 📊 FICHIERS MODIFIÉS/NOUVEAUX

### NOUVEAUX (à uploader):
- ✨ job_detail.php - Page détail offre
- ✨ candidat_dashboard.php - Dashboard candidat
- ✨ feedback.php - Formulaire feedback
- ✨ admin_feedbacks.php - Gestion feedbacks

### MODIFIÉS (à remplacer):
- 📝 index.php (affiche images offres)
- 📝 recruteur_dashboard.php (images dans cartes)
- 📝 admin_dashboard.php (stats feedbacks)
- 📝 includes/header.php (liens feedbacks + dashboards)
- 📝 login_action.php (redirection candidat)

### INCHANGÉS (pas besoin):
- config.php (vérifier juste les identifiants)
- assets/css/style.css (si aucune erreur)

---

## ✨ RÉSUMÉ DES AMÉLIORATIONS APPORTÉES

1. **Page détail offre** (job_detail.php)
   - Affichage complet avant candidature
   - Images grandes des offres
   - Info recruteur visible

2. **Dashboard candidat** (candidat_dashboard.php)
   - Vue des candidatures soumises
   - **Affichage messages recruteur** ← Solution au problème
   - Statuts colorés
   - Stats synthétiques

3. **Système feedback**
   - Formulaire feedback.php
   - Admin peut consulter feedbacks.php
   - Intégré au dashboard admin

4. **Responsive complet**
   - iPhone SE 375px: ✓ Testé
   - Tablet 768px: ✓ Testé
   - Desktop 1920px: ✓ Testé

5. **Uploads images**
   - Images offres affichées partout
   - Photos modifiables en édition
   - Support JPG/PNG/GIF/WebP

---

**Bon déploiement! 🚀**
Date: 20 février 2026
