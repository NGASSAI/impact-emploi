-- Script d'initialisation de la base de données Impact Emploi
-- Exécutez ce script pour créer les tables nécessaires

CREATE TABLE IF NOT EXISTS `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) UNIQUE NOT NULL,
  `telephone` varchar(20),
  `password` varchar(255) NOT NULL,
  `role` enum('candidat','recruteur','admin') DEFAULT 'candidat',
  `photo_profil` varchar(255) DEFAULT 'default.png',
  `bio` text,
  `is_blocked` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_recruteur` int NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `salaire` decimal(10,2) NOT NULL,
  `type_contrat` enum('CDI','CDD','Stage','Freelance') DEFAULT 'CDI',
  `competences` text,
  `date_publication` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_recruteur`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `candidatures` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_offre` int NOT NULL,
  `nom_cv` varchar(255) NOT NULL,
  `date_postulation` timestamp DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('En attente','Accepté','Refusé') DEFAULT 'En attente',
  `recruteur_id` int,
  `recruteur_message` text,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_utilisateur`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_offre`) REFERENCES `jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recruteur_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45),
  `user_agent` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer un utilisateur administrateur par défaut
INSERT IGNORE INTO `users` (`nom`, `prenom`, `email`, `telephone`, `password`, `role`, `is_blocked`) VALUES
('Ngassai', 'Nathan', 'nathanngassai885@gmail.com', '+242066817726', '$argon2id$v=19$m=65536,t=4,p=3$S0I0NGcyWnQ4WWpvRTc3dg$YeqJ9HOyIcUY6Gjt1EMtNHG+KXk+BzYmn7zDuTWGWgI', 'admin', 0);

-- Créer les index pour optimiser les performances
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_jobs_recruteur ON jobs(id_recruteur);
CREATE INDEX idx_candidatures_utilisateur ON candidatures(id_utilisateur);
CREATE INDEX idx_candidatures_offre ON candidatures(id_offre);
CREATE INDEX idx_candidatures_recruteur ON candidatures(recruteur_id);
CREATE INDEX idx_activity_user ON activity_logs(user_id);
CREATE INDEX idx_activity_action ON activity_logs(action);