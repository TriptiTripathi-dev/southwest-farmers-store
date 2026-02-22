<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Store' }} - Fresh Retail</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    
    <style>
        :root {
            /* Light Green Theme Palette */
            --theme-primary: #10b981;       /* Emerald Green */
            --theme-primary-hover: #059669; /* Darker Emerald */
            --theme-light: #ecfdf5;         /* Very Light Green Background */
            --theme-dark: #064e3b;          /* Deep Green for text/footer */
            --theme-text: #1e293b;          /* Standard dark slate */
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--theme-text);
            background-color: #f8fafc;
        }

        /* Custom Utilities for Theme */
        .text-theme { color: var(--theme-primary) !important; }
        .bg-theme-light { background-color: var(--theme-light) !important; }
        .bg-theme-dark { background-color: var(--theme-dark) !important; }
        
        .btn-theme {
            background-color: var(--theme-primary);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-theme:hover {
            background-color: var(--theme-primary-hover);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-outline-theme {
            color: var(--theme-primary);
            border: 2px solid var(--theme-primary);
            background: transparent;
            transition: all 0.3s ease;
        }
        .btn-outline-theme:hover {
            background-color: var(--theme-primary);
            color: white;
        }

        .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-lift:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; 
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    
    @include('layouts.website.navbar')

    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    @include('layouts.website.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>