# 🔍 Instructions Déploiement - Correction Erreur 403

## Problème Identifié
Le site fonctionne en local mais erreur 403 en production sur l'accès aux CV des candidats.

## Fichiers à Uploader (Vérification Obligatoire)

### 1. Dossiers Critiques
```
uploads/
├── cv/           ⚠️ DOIT être uploadé manuellement
├── jobs/          ✅ Existe déjà
└── profiles/      ✅ Existe déjà
```

### 2. Fichiers Modifiés à Uploader
```
config.php         ✅ Modifié (v1.3.1)
.htaccess          ✅ Modifié (règle uploads/)
```

### 3. Actions sur le Serveur

#### Étape 1 : Vérifier Structure
1. Connecter-vous au FTP/Panel de votre hébergeur
2. Vérifier que le dossier `uploads/cv/` existe bien
3. Si non, créer manuellement le dossier `cv` dans `uploads`

#### Étape 2 : Permissions (CRUCIAL)
```bash
# Via SSH ou FTP Client
chmod 755 uploads/
chmod 755 uploads/cv/
chmod 644 uploads/cv/*.*
```

#### Étape 3 : Upload Fichiers
1. Uploader `config.php` modifié
2. Uploader `.htaccess` modifié
3. Vérifier que tous les fichiers sont bien présents

#### Étape 4 : Diagnostic
1. Accéder à `votresite.com/diagnostic_403.php`
2. Vérifier tous les points sont verts
3. Si erreur persiste, contacter l'hébergeur

## Causes Possibles d'Erreur 403

### 1. Permissions Serveur
- **Symptôme** : Dossier existe mais inaccessible
- **Solution** : CHMOD 755 sur dossiers, 644 sur fichiers

### 2. Hébergeur Bloquant
- **Symptôme** : Permissions OK mais erreur 403
- **Solution** : Contacter support hébergeur pour débloquer accès

### 3. .htaccess Trop Restrictif
- **Symptôme** : Règles Apache bloquent accès
- **Solution** : Ajout règle `RewriteRule ^uploads/ - [L]` (déjà fait)

### 4. Safe Mode Activé
- **Symptôme** : PHP restreint accès fichiers
- **Solution** : Désactiver safe mode via panel hébergeur

## Test Final
1. Se connecter comme recruteur
2. Aller dans "Tableau de Bord Recruteur"
3. Cliquer sur "💬 Répondre" pour une candidature
4. Vérifier que le CV s'affiche et se télécharge

## Contact Support si Problème Persiste
- **Email** : nathanngassai885@gmail.com
- **Infos à fournir** : URL du diagnostic_403.php + hébergeur
