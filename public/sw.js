// Service Worker for PWA - Chat Application
const CACHE_NAME = 'ktk-chat-v1';
const STATIC_CACHE = 'ktk-static-v1';
const DYNAMIC_CACHE = 'ktk-dynamic-v1';

// Assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/chat',
    '/manifest.json',
    '/fav.png',
    '/kotak.svg',
    '/offline.html'
];

// Install event - cache static assets
self.addEventListener('install', function(event) {
    console.log('Service Worker: Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(function(cache) {
                console.log('Service Worker: Caching static assets');
                return cache.addAll(STATIC_ASSETS).catch(err => {
                    console.log('Some assets failed to cache:', err);
                });
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean old caches
self.addEventListener('activate', function(event) {
    console.log('Service Worker: Activating...');
    event.waitUntil(
        caches.keys()
            .then(function(cacheNames) {
                return Promise.all(
                    cacheNames
                        .filter(name => name !== STATIC_CACHE && name !== DYNAMIC_CACHE)
                        .map(name => caches.delete(name))
                );
            })
            .then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', function(event) {
    const request = event.request;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip API calls and form submissions - always go to network
    if (url.pathname.startsWith('/chat/') && url.pathname.includes('/messages')) {
        return;
    }
    if (url.pathname.startsWith('/push/')) {
        return;
    }

    // For HTML pages - network first, then cache
    if (request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then(function(response) {
                    // Clone and cache the response
                    const responseClone = response.clone();
                    caches.open(DYNAMIC_CACHE)
                        .then(cache => cache.put(request, responseClone));
                    return response;
                })
                .catch(function() {
                    return caches.match(request)
                        .then(response => response || caches.match('/offline.html'));
                })
        );
        return;
    }

    // For static assets - cache first, then network
    event.respondWith(
        caches.match(request)
            .then(function(cachedResponse) {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(request)
                    .then(function(response) {
                        // Cache valid responses
                        if (response && response.status === 200) {
                            const responseClone = response.clone();
                            caches.open(DYNAMIC_CACHE)
                                .then(cache => cache.put(request, responseClone));
                        }
                        return response;
                    });
            })
    );
});

// Push notification event
self.addEventListener('push', function(event) {
    console.log('Push notification received', event);
    
    let data = {
        title: 'New Message',
        body: 'You have a new message',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        tag: 'chat-message',
        data: {}
    };

    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/icons/icon-192x192.png',
        badge: data.badge || '/icons/icon-72x72.png',
        tag: data.tag || 'chat-message',
        vibrate: [200, 100, 200],
        data: data.data || {},
        actions: [
            { action: 'open', title: 'Open' },
            { action: 'close', title: 'Close' }
        ],
        requireInteraction: true
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification click event
self.addEventListener('notificationclick', function(event) {
    console.log('Notification clicked', event);
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    const urlToOpen = event.notification.data.url || '/chat';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url.includes('/chat') && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Background sync for offline messages (future enhancement)
self.addEventListener('sync', function(event) {
    if (event.tag === 'send-message') {
        event.waitUntil(sendPendingMessages());
    }
});

async function sendPendingMessages() {
    // Future: implement offline message queue
    console.log('Background sync: sending pending messages');
}
