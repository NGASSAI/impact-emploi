<?php
// Service Worker Loader - Sert le SW avec les bons en-têtes pour PWA Android
header('Service-Worker-Allowed: /');
header('Content-Type: application/javascript');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Access-Control-Allow-Origin: *');

// Lire et afficher le fichier SW
readfile(__DIR__ . '/sw.js');

