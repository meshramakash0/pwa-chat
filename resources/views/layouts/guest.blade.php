<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

        <!-- Favicon & Icons -->
        <link rel="icon" type="image/png" href="{{ asset('fav.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Mobile-first base styles */
            .login-wrapper {
                min-height: 100vh;
                min-height: 100dvh; /* Dynamic viewport height for mobile browsers */
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <!-- Mobile: full screen, edge-to-edge | Desktop: centered card layout -->
        <div class="login-wrapper flex flex-col justify-center items-center 
                    px-0 py-0 
                    sm:px-4 sm:py-8 
                    md:px-6 md:py-12">
            
            <!-- Logo: smaller on mobile, larger on desktop -->
            <div class="mb-4 sm:mb-6 md:mb-8 pt-8 sm:pt-0">
                <a href="/">
                    <img src="{{ asset('kotak.svg') }}" 
                         alt="Logo" 
                         class="w-24 h-auto sm:w-28 md:w-32" />
                </a>
            </div>

            <!-- Form container: full width on mobile, card style on desktop -->
            <div class="w-full flex-1 sm:flex-none
                        px-6 py-6 
                        sm:max-w-md sm:px-8 sm:py-6 
                        md:max-w-lg md:px-10 md:py-8
                        bg-white 
                        sm:shadow-lg sm:rounded-xl
                        md:shadow-xl">
                {{ $slot }}
            </div>
        </div>

        <!-- Register Service Worker for PWA -->
        <script>
            // Set PWA session marker on login page (user has logged in fresh)
            // This marker will persist until the browser/PWA window is closed
            sessionStorage.setItem('pwa_app_session', Date.now().toString());

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('Service Worker registered for PWA');
                        })
                        .catch(function(error) {
                            console.log('Service Worker registration failed:', error);
                        });
                });
            }
        </script>
    </body>
</html>
