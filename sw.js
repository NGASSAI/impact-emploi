/**
 * Service Worker - PWA pour Impact Emploi
 * Gère le cache, le hors-ligne et les mises à jour
 * Version optimisée v6 - Avec ASSETS_TO_CACHE pour Android
 */

const CACHE_NAME = 'impact-emploi-v7';
const STATIC_CACHE = 'impact-emploi-static-v7';
const DYNAMIC_CACHE = 'impact-emploi-dynamic-v7';
const IMAGE_CACHE = 'impact-emploi-images-v7';

// Tableau des assets à mettre en cache pour PWA Android
const ASSETS_TO_CACHE = [
    './',
    './index.php',
    './manifest.json',
    './assets/css/style.css',
    './assets/img/icon-192.png',
    './assets/img/icon-512.png',
    './sw.js'
];

// Debugging
function logSW(message, type = 'log') {
    const timestamp = new Date().toISOString();
    const logMessage = `[SW ${timestamp}] ${message}`;
    if (type === 'error') console.error(logMessage);
    else if (type === 'warn') console.warn(logMessage);
    else console.log(logMessage);
}

// Fichiers essentiels pour le fonctionnement hors-ligne
const ESSENTIAL_FILES = [
    '/',
    '/index.php',
    '/manifest.json',
    '/assets/css/style.css',
    '/assets/img/icon-192.png',
    '/assets/img/icon-512.png',
    '/sw.js'
];

// Installation du Service Worker
self.addEventListener('install', event => {
    logSW('Installing Service Worker v7...');
    event.waitUntil(
        Promise.all([
            // Cache des fichiers essentiels
            caches.open(STATIC_CACHE).then(cache => {
                logSW('Caching essential files');
                return cache.addAll(ESSENTIAL_FILES).catch(err => {
                    logSW(`Failed to cache some files: ${err}`, 'warn');
                });
            }),
            // Pré-cache des images
            caches.open(IMAGE_CACHE).then(cache => {
                return Promise.allSettled([
                    cache.add('./assets/img/icon-192.png').catch(() => {}),
                    cache.add('./assets/img/icon-512.png').catch(() => {})
                ]);
            })
        ]).then(() => {
            logSW('Service Worker installed successfully');
            return self.skipWaiting();
        })
    );
});

// Activation - Nettoyage des anciens caches
self.addEventListener('activate', event => {
    logSW('Activating Service Worker v7...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    const isCurrentCache = 
                        cacheName === STATIC_CACHE || 
                        cacheName === DYNAMIC_CACHE || 
                        cacheName === IMAGE_CACHE ||
                        cacheName === CACHE_NAME;
                    
                    if (!isCurrentCache && cacheName.startsWith('impact-emploi-')) {
                        logSW(`Deleting old cache: ${cacheName}`);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            logSW('Service Worker activated');
            return self.clients.claim();
        })
    );
});

// Gestion des requêtes
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorer les requêtes non-GET et requêtes externes
    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // Stratégie pour les images - Cache First avec expiration
    if (isImageRequest(request)) {
        event.respondWith(cacheFirstStrategy(request, IMAGE_CACHE));
        return;
    }

    // Stratégie pour les fichiers statiques - Cache First with Fallback
    if (isStaticFile(request)) {
        event.respondWith(cacheFirstStrategy(request, STATIC_CACHE));
        return;
    }

    // Stratégie pour les pages - Network First avec cache
    event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
});

// Vérifier si requête d'image
function isImageRequest(request) {
    const url = request.url.toLowerCase();
    return url.includes('/uploads/') || 
           url.includes('/assets/img/') ||
           url.match(/\.(jpg|jpeg|png|gif|webp|svg|ico)(\?|$)/);
}

// Vérifier si fichier statique
function isStaticFile(request) {
    const url = request.url.toLowerCase();
    return url.includes('/assets/css/') || 
           url.includes('/assets/js/') ||
           url.match(/\.(css|js|woff|woff2|ttf|eot)(\?|$)/);
}

// Stratégie Cache First avec expiration
async function cacheFirstStrategy(request, cacheName) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        // Vérifier si le cache est expiré (plus de 7 jours pour les images)
        const cacheDate = cachedResponse.headers.get('date');
        if (cacheDate) {
            const cachedTime = new Date(cacheDate).getTime();
            const now = Date.now();
            const maxAge = cacheName === IMAGE_CACHE ? 7 * 24 * 60 * 60 * 1000 : 24 * 60 * 60 * 1000;
            
            if (now - cachedTime > maxAge) {
                // Cache expiré, essayer de récupérer une nouvelle version
                try {
                    const networkResponse = await fetch(request);
                    if (networkResponse && networkResponse.status === 200) {
                        const cache = await caches.open(cacheName);
                        cache.put(request, networkResponse.clone());
                    }
                    return networkResponse;
                } catch (error) {
                    return cachedResponse;
                }
            }
        }
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        if (isImageRequest(request)) {
            return new Response(
                '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect fill="#eee" width="100" height="100"/><text x="50%" y="50%" text-anchor="middle" fill="#999">Image</text></svg>',
                { headers: { 'Content-Type': 'image/svg+xml' } }
            );
        }
        throw error;
    }
}

// Stratégie Network First avec cache
async function networkFirstStrategy(request, cacheName) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        // Page hors-ligne pour navigation
        if (request.mode === 'navigate') {
            return caches.match('./index.php') || new Response(
                getOfflinePage(),
                { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
            );
        }

        return new Response('Offline', { status: 503 });
    }
}

// Page hors-ligne
function getOfflinePage() {
    return `<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hors-ligne - Impact Emploi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0052A3 0%, #004080 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .offline-card { background: white; padding: 40px 30px; border-radius: 16px; text-align: center; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .offline-icon { font-size: 4rem; margin-bottom: 20px; }
        h1 { color: #0052A3; margin-bottom: 15px; font-size: 1.5rem; }
        p { color: #666; margin-bottom: 25px; line-height: 1.6; }
        .retry-btn { background: #0052A3; color: white; border: none; padding: 14px 28px; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s; }
        .retry-btn:hover { background: #004080; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="offline-card">
        <div class="offline-icon">📡</div>
        <h1>Vous êtes hors-ligne</h1>
        <p>Il semble que vous n'avez pas de connexion internet. Vérifiez votre connexion et réessayez.</p>
        <button class="retry-btn" onclick="location.reload()">Réessayer</button>
    </div>
</body>
</html>`;
}

// Vérification des mises à jour
async function checkForUpdates() {
    logSW('Checking for updates...');
    try {
        const response = await fetch('./manifest.json?_=' + Date.now());
        if (response.ok) {
            const manifest = await response.json();
            const clientsList = await self.clients.matchAll();
            clientsList.forEach(client => {
                client.postMessage({
                    type: 'UPDATE_CHECK',
                    timestamp: Date.now(),
                    version: manifest.version || '1.0.0'
                });
            });
        }
    } catch (error) {
        logSW(`Update check failed: ${error}`, 'warn');
    }
}

// Vérification toutes les heures
setInterval(checkForUpdates, 60 * 60 * 1000);

// Messages du main thread
self.addEventListener('message', event => {
    if (event.data?.type === 'CHECK_UPDATE') {
        checkForUpdates();
    }
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    if (event.data?.type === 'CACHE_URLS') {
        caches.open(DYNAMIC_CACHE).then(cache => {
            cache.addAll(event.data.urls).catch(err => logSW(`Failed to cache URLs: ${err}`, 'warn'));
        });
    }
});

// Notifications push
self.addEventListener('push', event => {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'Nouvelle notification',
            icon: './assets/img/icon-192.png',
            badge: './assets/img/icon-192.png',
            vibrate: [100, 50, 100],
            data: { url: data.url || './index.php' }
        };
        event.waitUntil(self.registration.showNotification(data.title || 'Impact Emploi', options));
    }
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url || './index.php'));
});

