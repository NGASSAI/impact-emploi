# 🎯 Impact Emploi - Plateforme de Recrutement Professionnelle

## 📋 Présentation

Impact Emploi est une plateforme complète de gestion d'emploi et de recrutement pour le Congo. Elle connecte les talents avec les entreprises et offre un système de gestion des candidatures intuitif et professionnel.

## ✨ Fonctionnalités

### 👥 Pour les Candidats
- ✅ Inscription et création de profil
- 📝 Consultation des offres d'emploi
- 📤 Téléchargement et soumission de CV
- 📊 Suivi des candidatures
- 🔔 Notifications des recruteurs
- 👤 Gestion du profil personnel

### 🏢 Pour les Recruteurs
- 💼 Création et gestion d'offres d'emploi
- 📋 Tableau de bord des candidatures
- 💬 Communication avec les candidats
- 📊 Statistiques complètes
- ✅ Acceptation/Refus de candidatures
- 📧 Notifications automatiques

### 👨‍💼 Pour les Administrateurs
- 📊 Tableau de bord complet avec statistiques
- 👥 Gestion des utilisateurs
- 🔒 Blocage/Déblocage de comptes
- 📝 Logs d'activité en temps réel
- 🎯 Supervision de toutes les activités
- 🛡️ Gestion de la sécurité

## 🚀 Installation

### Prérequis
- **PHP >= 7.4**
- **MySQL >= 5.7**
- **Apache avec mod_rewrite**

### Étapes d'installation

1. **Cloner ou télécharger le projet**
```bash
cd C:\xampp\htdocs\test1
```

2. **Créer la base de données**
```bash
# Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
# Créer une nouvelle base de données : impact_emploi
# Importer le fichier database.sql
```

Ou via le terminal MySQL:
```bash
mysql -u root -p < database.sql
```

3. **Configurer les droits des dossiers**
```bash
mkdir uploads/cv
mkdir uploads/profiles
chmod 755 uploads/cv
chmod 755 uploads/profiles
```

4. **Modifier config.php si nécessaire**
```php
$host = 'localhost';     // Hôte MySQL
$db   = 'impact_emploi';  // Nom de la base
$user = 'root';          // Utilisateur MySQL
$pass = '';              // Mot de passe MySQL
```

5. **Accéder à l'application**
```
http://localhost/test1/
```

## 🔐 Sécurité

### Comptes Par Défaut

**Administrateur :**
- Email : `nathanngassai885@gmail.com`
- Mot de passe : Le mot de passe par défaut est hashé dans la base de données
- Rôle : Admin

### Mesures de Sécurité Implémentées

✅ **Protection CSRF** - Tokens CSRF sur tous les formulaires
✅ **Hachage de Mots de Passe** - Argon2id (plus sécurisé)
✅ **Validation Entrées** - Nettoyage et validation de toutes les entrées
✅ **Préparation SQL** - Requêtes paramétrées contre l'injection SQL
✅ **Headers de Sécurité** - X-Frame-Options, X-Content-Type-Options, etc.
✅ **Sessions Sécurisées** - HTTPOnly et Secure flags
✅ **Logging d'Activités** - Enregistrement de toutes les actions importantes
✅ **Protection XSS** - Échappement HTML sur toutes les sorties

## 🎨 Architecture et Design

### Structure des Fichiers
```
test1/
├── index.php              # Page d'accueil
├── login.php              # Page de connexion
├── register.php           # Page d'inscription
├── logout.php             # Déconnexion
├── config.php             # Configuration et fonctions de sécurité
├── login_action.php       # Traitement de la connexion
├── postuler.php           # Soumission de candidature
├── profil.php             # Gestion du profil
├── change_password.php    # Changement de mot de passe
├── chat.php               # Réponse aux candidatures
├── admin_dashboard.php    # Tableau de bord admin
├── admin_actions.php      # Actions admin
├── recruteur_dashboard.php# Tableau de bord recruteur
├── create_job.php         # Créer une offre d'emploi
├── edit_job.php           # Modifier une offre d'emploi
├── delete_job.php         # Supprimer une offre d'emploi
├── includes/
│   ├── header.php         # En-tête commun
│   └── footer.php         # Pied de page
├── assets/css/
│   └── style.css          # Feuille de styles moderne
├── uploads/
│   ├── cv/                # CVs des candidats
│   └── profiles/          # Photos de profil
├── database.sql           # Script de création de BD
└── .htaccess              # Configuration Apache
```

### Design Responsive
- ✅ Mobile-first approach
- ✅ Optimisé pour tous les appareils (tablettes, téléphones, PC)
- ✅ Animations fluides et modernes
- ✅ Interface intuitive et professionnelle

## 📱 Contacts Professionnels

**Email Admin** : nathanngassai885@gmail.com
**WhatsApp** : +242 066817726

## 🎯 Workflows Principaux

### Inscription et Authentification
```
1. Visiteur → Register
2. Remplir formulaire (validation côté client et serveur)
3. Créer compte avec mot de passe hashé
4. Redirection vers Login
5. Connexion et création de session
6. Redirection selon le rôle
```

### Candidature à une Offre
```
1. Candidat connecté → Voir offres
2. Cliquer "Postuler"
3. Uploader CV (validation PDF, max 5MB)
4. Soumission enregistrée
5. Recruteur reçoit notification
6. Candidat peut suivre le statut
```

### Gestion des Candidatures (Recruteur)
```
1. Recruteur → Tableau de Bord
2. Voir les candidatures
3. Télécharger et revoir le CV
4. Cliquer "Répondre"
5. Changer statut (En attente / Accepté / Refusé)
6. Envoyer un message au candidat
7. Candidat reçoit la réponse
```

## 📊 Tables de Base de Données

### users
```sql
id, nom, prenom, email, telephone, password, role, 
photo_profil, bio, is_blocked, created_at, updated_at
```

### jobs
```sql
id, id_recruteur, titre, description, lieu, salaire,
type_contrat, competences, date_publication, updated_at
```

### candidatures
```sql
id, id_utilisateur, id_offre, nom_cv, date_postulation,
statut, recruteur_id, recruteur_message, updated_at
```

### activity_logs
```sql
id, user_id, action, description, ip_address, 
user_agent, created_at
```

## 🔧 Configuration

### Variables d'Environnement (à adapter)
```php
// config.php
$host = 'localhost';
$db   = 'impact_emploi';
$user = 'root';
$pass = '';

define('ADMIN_EMAIL', 'nathanngassai885@gmail.com');
define('WHATSAPP_NUMBER', '+242066817726');
```

### Limites de Fichiers
- **CV Maximum** : 5 MB (PDF uniquement)
- **Photo de Profil Maximum** : 2 MB (JPEG/PNG/GIF)

## 🛠️ Maintenance

### Sauvegardes
Sauvegardez régulièrement :
- La base de données MySQL
- Le dossier `/uploads/`
- Les fichiers PHP

### Logs
Les logs d'activité sont stockés dans la table `activity_logs`. Consultez-la dans le dashboard admin.

## ℹ️ Notes Importantes

1. **Base de Données** : Créez la base `impact_emploi` avant de commencer
2. **Dossiers d'Upload** : Assurez-vous que `/uploads/cv` et `/uploads/profiles` existent et sont accessibles en écriture
3. **PHP PDO** : Le projet utilise PDO pour les requêtes (plus sûr)
4. **Sessions** : Les sessions PHP doivent être activées
5. **HTTPS** : Déployez en HTTPS en production

## 📞 Support

Pour toute question ou assistance :
- 📧 Email : nathanngassai885@gmail.com
- 💬 WhatsApp : +242 066817726

## 📄 Licence

© 2026 Impact Emploi - Tous droits réservés

---

**Version** : 2.0
**Dernière mise à jour** : Février 2026
**Développé avec** ❤️ au Congo