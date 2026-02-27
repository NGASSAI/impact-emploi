# TODO - Corrections Interface & PWA

## Terminé
- [x] 1. Manifest.json - Utiliser chemins relatifs
- [x] 2. Header.php - Ajouter lien manifest
- [x] 3. Footer.php - Enregistrer Service Worker + scripts JS
- [x] 4. Lightbox CSS - Fullscreen overlay avec object-fit contain
- [x] 5. Navigation Mobile - Bouton X fonctionnel
- [x] 6. Test - Fermeture menu au clic "Profil"

## Détails des corrections

### 1. manifest.json
- Changé `/assets/img/` → `assets/img/` (chemins relatifs)
- Changé `/index.php` → `./index.php`
- Changé `/scope` → `./scope`

### 2. header.php
- Ajout du lien PWA manifest: `<link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">`

### 3. footer.php
- Ajout des scripts: ui-components.js, pwa-install.js
- Ajout de l'enregistrement du Service Worker
- Bouton X (nav-close-btn) avec EventListener click fonctionnel
- Fermeture menu sur clic lien, ESC, resize

### 4. style.css
- Lightbox overlay: fixed fullscreen avec `!important`
- Profile photo: object-fit contain avec grandes dimensions
- Job photo: object-fit contain avec grandes dimensions

### 5. Logique
- Menu ferme automatiquement au clic sur "Profil"
- Service Worker enregistré pour PWA
- Manifest lié dans le head

