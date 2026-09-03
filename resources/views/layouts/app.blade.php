<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'ARMS') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #wrapper {
            display: flex;
            flex: 1;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #1e293b;
            color: #ffffff;
            transition: all 0.3s;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar ul.nav-menu {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }

        #sidebar ul.nav-menu li.nav-item {
            margin-bottom: 0.25rem;
            padding: 0 0.75rem;
        }

        #sidebar ul.nav-menu a.nav-link {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        #sidebar ul.nav-menu a.nav-link:hover,
        #sidebar ul.nav-menu a.nav-link.active {
            color: #ffffff;
            background-color: #2563eb;
        }

        #content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
        }

        .card-custom {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }

        .footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            color: #64748b;
            font-size: 0.875rem;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            #wrapper {
                flex-direction: column;
            }
            #sidebar {
                min-width: 100%;
                max-width: 100%;
                position: relative;
                height: auto;
            }
        }
    </style>
</head>
<body>

    <div id="wrapper">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Navbar -->
            @include('layouts.navbar')

            <!-- Main Content Body -->
            <main class="container-fluid p-4">
                @include('components.alert')
                @yield('content')
            </main>

            <!-- Footer -->
            @include('layouts.footer')
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
