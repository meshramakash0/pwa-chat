<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KTK Chat') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="application-name" content="KTK Chat">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="KTK Chat">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#075e54">
        <meta name="msapplication-TileColor" content="#075e54">
        <meta name="msapplication-tap-highlight" content="no">

        <!-- Favicon & Icons -->
        <link rel="icon" type="image/png" href="{{ asset('fav.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('icons/icon-192x192.png') }}">

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- iOS Splash Screens -->
        <style>
            /* Prevent pull-to-refresh on iOS */
            html, body {
                overscroll-behavior-y: contain;
            }
        </style>
    </head>
    <body class="font-sans antialiased h-full">
        <div class="h-full bg-gray-100">
            <!-- Page Content -->
            <main class="h-full">
                {{ $slot }}
            </main>
        </div>

        @auth
        <script>
            // Push Notification Registration
            (function() {
                const VAPID_PUBLIC_KEY = '{{ config("webpush.vapid.public_key") }}';

                // Check if push notifications are supported
                if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                    console.log('Push notifications not supported');
                    return;
                }

                // Register service worker
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('Service Worker registered:', registration);
                        return registration;
                    })
                    .then(function(registration) {
                        // Check current subscription status
                        return registration.pushManager.getSubscription()
                            .then(function(subscription) {
                                if (subscription) {
                                    console.log('Already subscribed to push notifications');
                                    return subscription;
                                }

                                // Request notification permission
                                return Notification.requestPermission()
                                    .then(function(permission) {
                                        if (permission !== 'granted') {
                                            console.log('Notification permission denied');
                                            return null;
                                        }

                                        // Subscribe to push notifications
                                        return registration.pushManager.subscribe({
                                            userVisibleOnly: true,
                                            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                                        });
                                    });
                            });
                    })
                    .then(function(subscription) {
                        if (!subscription) return;

                        // Send subscription to server
                        return fetch('/push/subscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(subscription)
                        });
                    })
                    .then(function(response) {
                        if (response && response.ok) {
                            console.log('Push subscription saved to server');
                        }
                    })
                    .catch(function(error) {
                        console.error('Push notification setup error:', error);
                    });

                // Helper function to convert VAPID key
                function urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding)
                        .replace(/-/g, '+')
                        .replace(/_/g, '/');

                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);

                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }
            })();
        </script>
        @endauth
    </body>
</html>
