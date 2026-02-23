/**
 * Service Worker - PWA pour Impact Emploi
 * Gère le cache, le hors-ligne et les mises à jour
 */

const CACHE_NAME = 'impact-emploi-v1';
const ASSETS_TO_CACHE = [];

// Installation du Service Worker
self.addEventListener('install', event => {
    console.log('Installing Service Worker...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('Caching assets');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

// Activation du Service Worker
self.addEventListener('activate', event => {
    console.log('Activating Service Worker...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Stratégie Cache First avec Network Fallback
self.addEventListener('fetch', event => {
    const { request } = event;

    // N'intercepter que les GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Ignorer les requêtes vers des ressources externes
    if (!request.url.includes(self.location.origin)) {
        return;
    }

    event.respondWith(
        caches.match(request).then(response => {
            // Si trouvé en cache, le retourner
            if (response) {
                return response;
            }

            // Sinon, essayer de le récupérer du serveur
            return fetch(request).then(response => {
                // Vérifier que la réponse est valide
                if (!response || response.status !== 200 || response.type === 'error') {
                    return response;
                }

                // Cloner et mettre en cache
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(request, responseClone);
                });

                return response;
            }).catch(() => {
                // En cas d'erreur réseau et pas en cache, retourner une page hors-ligne
                return new Response(
                    '<h1>Mode hors-ligne</h1><p>Vous êtes hors-ligne. Veuillez vérifier votre connexion internet.</p>',
                    { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                );
            });
        })
    );
});
