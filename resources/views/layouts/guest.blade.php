<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="medieval">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="icon" href="{{ asset('images/logo.jpg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">

        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

        
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        
        @livewireStyles
    </head>
    <body>
        <div class="font-sans text-base-content antialiased min-h-screen"
             style="background: radial-gradient(ellipse at 30% 10%, rgba(107,16,16,0.08), transparent 50%),
                    radial-gradient(ellipse at 70% 90%, rgba(212,175,55,0.05), transparent 50%),
                    #120e0a;">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
