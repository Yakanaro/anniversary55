@props(['title', 'description' => null])

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#0d0809">
        <title>{{ $title }}</title>
        @if ($description)
            <meta name="description" content="{{ $description }}">
            <meta property="og:title" content="{{ $title }}">
            <meta property="og:description" content="{{ $description }}">
            <meta property="og:image" content="{{ asset('images/irina.jpg') }}">
        @endif

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        {{ $slot }}

        <footer class="site-footer">Created by <b>Sergey Druzhinin</b></footer>
    </body>
</html>
