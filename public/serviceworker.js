const staticCacheName = 'pwa-v3';
const filesToCache = [
    '/',
    '/offline',
    '/manifest.json',
    '/images/icons/by-star-72x72.png',
    '/images/icons/by-star-96x96.png',
    '/images/icons/by-star-128x128.png',
    '/images/icons/by-star-144x144.png',
    '/images/icons/by-star-152x152.png',
    '/images/icons/by-star-192x192.png',
    '/images/icons/by-star-384x384.png',
    '/images/icons/by-star-512x512.png',
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName).then((cache) => cache.addAll(filesToCache))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.startsWith('pwa-') && cacheName !== staticCacheName)
                    .map((cacheName) => caches.delete(cacheName))
            )
        )
    );

    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match('/offline'))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request)
                .then((networkResponse) => {
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                        return networkResponse;
                    }

                    const responseClone = networkResponse.clone();
                    caches.open(staticCacheName).then((cache) => cache.put(event.request, responseClone));

                    return networkResponse;
                })
                .catch(() => caches.match('/offline'));
        })
    );
});

self.addEventListener('push', (event) => {
    let payload = {
        title: 'Bilgi Yildizi',
        body: 'Yeni bir bildirim var.',
        icon: '/images/icons/by-star-192x192.png',
        badge: '/images/icons/by-star-96x96.png',
        data: {
            url: '/',
        },
    };

    if (event.data) {
        try {
            payload = { ...payload, ...event.data.json() };
        } catch (error) {
            payload.body = event.data.text() || payload.body;
        }
    }

    event.waitUntil(
        self.registration.showNotification(payload.title, {
            body: payload.body,
            icon: payload.icon,
            badge: payload.badge,
            image: payload.image,
            tag: payload.tag,
            data: payload.data || {},
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client && client.url === targetUrl) {
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }

            return undefined;
        })
    );
});
