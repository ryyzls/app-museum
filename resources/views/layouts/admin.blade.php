<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@300;400;500;600&family=DM+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --cream-bg: #f7f4ef;
            --warm-white: #ffffff;
            --charcoal: #2a2826;
            --charcoal-light: #3d3a37;
            --gold: #c9a96e;
            --gold-dark: #a88a5a;
            --border-subtle: #e8e4dc;
            --shadow-soft: 0 4px 24px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream-bg);
            color: var(--charcoal);
        }

        .museum-title {
            font-family: 'Crimson Pro', serif;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 999px;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .glass {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .nav-item {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateX(4px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--charcoal-light) 100%);
            color: white;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .nav-item.active .badge {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gold);
            transform: scaleY(0);
            transition: 0.3s ease;
        }

        .nav-item.active::before {
            transform: scaleY(1);
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            transition: all 0.35s ease;
        }

        .admin-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .primary-button {
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--charcoal-light) 100%);
            color: white;
            transition: 0.3s ease;
        }

        .primary-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .table-row {
            transition: 0.25s ease;
        }

        .table-row:hover {
            background: rgba(0, 0, 0, 0.025);
        }

        .status-badge {
            padding: 10px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.1);
            color: rgb(22 163 74);
        }

        .status-upcoming {
            background: rgba(234, 179, 8, 0.12);
            color: rgb(202 138 4);
        }

        .status-closed {
            background: rgba(239, 68, 68, 0.1);
            color: rgb(220 38 38);
        }

        .admin-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 16px 22px;
            background: rgba(255, 255, 255, 0.7);
            transition: 0.3s ease;
        }

        .admin-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.12);
        }

        .empty-state {
            padding: 100px 20px;
            text-align: center;
        }

        .empty-state h3 {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #6b7280;
        }
    </style>
</head>

<body class="overflow-x-hidden">
    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-[300px] border-r glass sticky top-0 h-screen hidden lg:flex flex-col"
            style="border-color: var(--border-subtle);">

            {{-- LOGO --}}
            <div class="px-8 py-10 border-b" style="border-color: var(--border-subtle);">
                <div class="mb-6">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                        style="background: rgba(201,169,110,0.12);">
                        ✦
                    </div>
                </div>
                <p class="uppercase tracking-[0.3em] text-[11px] mb-2" style="color: var(--gold-dark);">
                    ALPHASEUM
                </p>
                <h1 class="museum-title text-6xl font-light"> Admin </h1>
            </div>

            {{-- NAVIGATION --}}
            <nav class="flex-1 px-6 py-8 space-y-3 overflow-y-auto hide-scrollbar">

                {{-- DASHBOARD --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="text-xl">◫</span> <span class="font-medium"> Dashboard </span>
                </a>

                {{-- ARTWORKS --}}
                <a href="{{ route('admin.artworks.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.artworks.*') ? 'active' : '' }}">
                    <span class="text-xl">▣</span> <span class="font-medium"> Artworks </span>
                    <span class="badge ml-auto px-3 py-1 rounded-full text-xs font-semibold"
                        style="background: rgba(201,169,110,0.18); color: var(--gold-dark);">
                        {{ \App\Models\Artwork::count() }}
                    </span>
                </a>

                {{-- ARTISTS --}}
                <a href="{{ route('admin.artists.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.artists.*') ? 'active' : '' }}">

                    <span class="text-xl">◈</span>

                    <span class="font-medium">
                        Artists
                    </span>

                    <span class="badge ml-auto px-3 py-1 rounded-full text-xs font-semibold"
                        style="background: rgba(201,169,110,0.18); color: var(--gold-dark);">

                        {{ \App\Models\Artist::count() }}

                    </span>

                </a>

                {{-- EXHIBITIONS --}}
                <a href="{{ route('admin.exhibitions.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.exhibitions.*') ? 'active' : '' }}">
                    <span class="text-xl">◩</span> <span class="font-medium"> Exhibitions </span>
                    <span class="badge ml-auto px-3 py-1 rounded-full text-xs font-semibold"
                        style="background: rgba(201,169,110,0.18); color: var(--gold-dark);">
                        {{ \App\Models\Exhibition::count() }}
                    </span>
                </a>

                {{-- TICKETS --}}
                <a href="{{ route('admin.tickets.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                    <span class="text-xl">◌</span> <span class="font-medium"> Tickets </span>
                    <span class="badge ml-auto px-3 py-1 rounded-full text-xs font-semibold"
                        style="background: rgba(201,169,110,0.18); color: var(--gold-dark);">
                        {{ \App\Models\Ticket::count() }}
                    </span>
                </a>


                {{-- user --}}
                <a href="{{ route('admin.users.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.index.*') ? 'active' : '' }}">
                    <span class="text-xl">◌</span> <span class="font-medium"> User </span>
                    <span class="badge ml-auto px-3 py-1 rounded-full text-xs font-semibold"
                        style="background: rgba(201,169,110,0.18); color: var(--gold-dark);">
                        {{ \App\Models\User::count() }}
                    </span>
                </a>


                <div class="my-6 border-t" style="border-color: var(--border-subtle);"></div>

                {{-- TRANSACTIONS-LOG --}}
                <a href="{{ route('admin.transaction-logs.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.transaction-logs.*') ? 'active' : '' }}">

                    <span class="text-xl">⌘</span>

                    <span class="font-medium">
                        Transaction Logs
                    </span>

                </a>

                <a href="{{ route('admin.revenue-report.index') }}"
                    class="nav-item flex items-center gap-4 px-5 py-4 rounded-2xl {{ request()->routeIs('admin.revenue-report.*') ? 'active' : '' }}">

                    <span class="text-xl">◔</span>

                    <span class="font-medium">
                        Revenue Report
                    </span>

                </a>

                {{-- LOGOUT --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="nav-item w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-red-500 hover:bg-red-50 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </nav>

            {{-- PROFILE --}}
            <div class="p-4">
                <div class="admin-card p-4 rounded-[28px]">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-semibold"
                            style="background: linear-gradient(135deg, var(--gold), var(--gold-dark));">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN --}}
        <main class="flex-1 min-w-0">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-20 glass border-b" style="border-color: var(--border-subtle);">
                <div class="px-10 py-6 flex items-center justify-between">

                    {{-- TITLE --}}
                    <div>
                        <div class="flex items-center gap-2 text-sm text-gray-400 mb-1">
                            <span> Admin </span> <span> › </span> <span> @yield('breadcrumb', 'Dashboard') </span>
                        </div>
                        <h2 class="museum-title text-4xl font-light"> @yield('page-title', 'Dashboard') </h2>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex items-center gap-4">

                        {{-- SEARCH --}}
                        <form action="{{ route('admin.exhibitions.index') }}" method="GET"
                            class="flex items-center gap-3">

                            <input type="text" name="search" placeholder="Search exhibitions..."
                                class="admin-input w-[240px] h-14 rounded-2xl hidden lg:block">

                            <button type="submit"
                                class="w-14 h-14 rounded-2xl glass flex items-center justify-center hover:bg-white hover:shadow-lg transition-all duration-300 group">

                                <svg class="w-5 h-5 text-gray-500 group-hover:text-[#c9a96e] transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                    </path>

                                </svg>

                            </button>

                        </form>
                        </button>

                        {{-- VIEW PUBLIC SITE --}}
                        <a href="{{ route('home') }}"
                            class="relative w-14 h-14 rounded-2xl glass flex items-center justify-center hover:bg-white hover:shadow-lg transition-all duration-300 group"
                            title="View Public Site">

                            <span class="text-xl text-gray-500 group-hover:text-[#c9a96e] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-buildings" viewBox="0 0 16 16">
                                    <path
                                        d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z" />
                                    <path
                                        d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z" />
                                </svg>
                            </span>

                        </a>

                        {{-- LOGOUT BUTTON DI TOPBAR --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-14 h-14 rounded-2xl glass flex items-center justify-center hover:bg-red-50 hover:shadow-lg transition-all duration-300 group"
                                title="Logout">
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-red-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>

                    </div>
                </div>
            </header>

            {{-- CONTENT --}}
            <div class="p-10">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Package RealRashid SweetAlert untuk Flash Messages --}}
    @include('sweetalert::alert')

    {{-- GLOBAL DELETE CONFIRMATION SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Success!',
                    text: "{!! session('success') !!}",
                    icon: 'success',
                    confirmButtonColor: '#2a2826',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif
</body>

</html>