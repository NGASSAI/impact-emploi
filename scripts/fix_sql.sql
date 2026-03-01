-- Script SQL pour corriger l'erreur "Column not found: 1054 Unknown column 'candidate_notification_seen'"
-- Exécuter ce script dans phpMyAdmin ou console MySQL

-- Ajouter la colonne candidate_notification_seen si elle n'existe pas
ALTER TABLE candidatures ADD COLUMN IF NOT EXISTS candidate_notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at;

-- Ajouter la colonne notification_seen si elle n'existe pas
ALTER TABLE candidatures ADD COLUMN IF NOT EXISTS notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at;

-- Vérifier les colonnes
SHOW COLUMNS FROM candidatures;

