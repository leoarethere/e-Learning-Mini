<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Sistem E-Learning')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .sidebar {
                min-height: calc(100vh - 56px);
                background-color: #f8f9fa;
            }
            .progress-tracker {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .bg-gradient-primary {
                background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
            }
            .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
            .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
            .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
            .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom Scripts -->
        <script>
            // Progress tracking
            function markAsCompleted(materialId) {
                fetch(`/progress/${materialId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        completed: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }

            // Auto-save progress
            document.addEventListener('DOMContentLoaded', function() {
                // Mark material as read when user scrolls to bottom
                const materialContent = document.getElementById('material-content');
                if (materialContent) {
                    const materialId = materialContent.getAttribute('data-material-id');
                    window.addEventListener('scroll', function() {
                        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                            if (materialId) {
                                markAsCompleted(materialId);
                            }
                        }
                    });
                }
            });
        </script>
        
        @yield('scripts')
    </body>
</html>