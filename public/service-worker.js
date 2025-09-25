const CACHE_NAME = 'easy-rental-v1.0.0';
const urlsToCache = [
    '/',
    '/icons/icon-192x192.png' // Main icon
];

// The 'install' event is fired when the service worker is installed
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// The 'fetch' event is fired for each network request
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response; // If cache exists, return the cached version
                }
                return fetch(event.request); // Otherwise, make the network request
            })
    );
});