<!DOCTYPE html>
<html lang="es-AR" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta
            name="description"
            content="Gestioná reparaciones, clientes y estados de trabajos desde un solo lugar. EnReparacion es un sistema de gestión para servicios técnicos en Argentina."
        >
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="canonical" href="{{ url('/') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="bg-background text-text-primary">
        @yield('main')
        <x-toast />
    </body>
</html>
