<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@300;400;600&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --cream-bg: #f7f4ef;
            --cream-light: #faf8f5;
            --warm-white: #ffffff;
            --charcoal: #2a2826;
            --charcoal-light: #3d3a37;
            --gold: #c9a96e;
            --gold-dark: #a88a5a;
            --border-subtle: #e8e4dc;
            --shadow-soft: 0 4px 24px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        * {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .museum-title {
            font-family: 'Crimson Pro', Georgia, serif;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        /* Smooth transitions */
        * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--cream-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-dark);
        }

        /* Sidebar animations */
        .nav-item {
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--gold);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-item:hover::before,
        .nav-item.active::before {
            transform: translateX(0);
        }

        .nav-item .icon {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-item:hover .icon {
            transform: translateX(4px);
        }

        /* Profile card hover effect */
        .profile-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        /* Floating animation for logo */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-4px);
            }
        }

        .logo-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Badge pulse */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .badge-pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Backdrop blur support */
        @supports (backdrop-filter: blur(20px)) {
            .blur-bg {
                backdrop-filter: blur(20px) saturate(180%);
            }
        }
    </style>
</head>

<body style="background-color: var(--cream-bg); color: var(--charcoal);">

    <div class="flex min-h-screen">

        {{-- Enhanced Sidebar --}}
        <aside class="w-[300px] border-r blur-bg" style="border-color: var(--border-subtle); 
                      background: linear-gradient(180deg, 
                                  rgba(255,255,255,0.95) 0%, 
                                  rgba(255,255,255,0.92) 100%); 
                      box-shadow: var(--shadow-soft);">

            <div class="flex flex-col h-full">

                {{-- Logo Section --}}
                <div class="px-8 py-10 border-b" style="border-color: var(--border-subtle);">
                    <div class="logo-float inline-block">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="4" y="4" width="32" height="32" rx="6" fill="var(--charcoal)" opacity="0.05" />
                            <path d="M12 20L20 12L28 20L20 28L12 20Z" fill="var(--gold)" />
                            <circle cx="20" cy="20" r="3" fill="var(--charcoal)" />
                        </svg>
                    </div>

                    <div class="mt-6">
                        <p class="uppercase tracking-[0.25em] text-[10px] font-semibold mb-2"
                            style="color: var(--gold-dark);">
                            ALPHASEUM
                        </p>
                        <h1 class="museum-title text-5xl font-light gradient-text">
                            Admin
                        </h1>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-6 py-8 space-y-2">

                    <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center gap-4 px-5 py-4 rounded-xl group
                              {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="background: {{ request()->routeIs('admin.dashboard')
    ? 'linear-gradient(135deg, var(--charcoal) 0%, var(--charcoal-light) 100%)'
    : 'transparent' }};
                              color: {{ request()->routeIs('admin.dashboard')
    ? 'var(--warm-white)'
    : 'var(--charcoal)' }};">

                        <svg class="icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                        </svg>

                        <span class="font-medium">Dashboard</span>

                        @if(request()->routeIs('admin.dashboard'))
                            <span class="ml-auto badge-pulse w-2 h-2 rounded-full" style="background: var(--gold);"></span>
                        @endif
                    </a>

                    <a href="{{ route('admin.artworks.index') }}"
                        class="nav-item flex items-center gap-4 px-5 py-4 rounded-xl group"
                        style="color: var(--charcoal);">

                        <svg class="icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M3 9h18M9 21V9" />
                        </svg>

                        <span class="font-medium">Artworks</span>

                        <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full"
                            style="background: var(--gold); color: var(--warm-white);">
                            
                            {{ \App\Models\Artwork::count() }}
                           

                        </span>
                    </a>

                    <a href="#" class="nav-item flex items-center gap-4 px-5 py-4 rounded-xl group"
                        style="color: var(--charcoal);">

                        <svg class="icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4" />
                            <path d="M3 5v14a2 2 0 0 0 2 2h16v-5" />
                            <path d="M18 12a2 2 0 0 0 0 4h4v-4Z" />
                        </svg>

                        <span class="font-medium">Exhibitions</span>
                    </a>

                    <a href="#" class="nav-item flex items-center gap-4 px-5 py-4 rounded-xl group"
                        style="color: var(--charcoal);">

                        <svg class="icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path
                                d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>

                        <span class="font-medium">Tickets</span>
                    </a>

                    {{-- Divider --}}
                    <div class="py-4">
                        <div class="h-px" style="background: var(--border-subtle);"></div>
                    </div>

                    <a href="#" class="nav-item flex items-center gap-4 px-5 py-4 rounded-xl group"
                        style="color: var(--charcoal);">

                        <svg class="icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="3" />
                            <path
                                d="M12 1v6m0 6v6m9-9h-6m-6 0H3m15.364 6.364l-4.243-4.243m-6.364 0L3.636 18.364m12.728 0l-4.243-4.243m-6.364 0L3.636 5.636" />
                        </svg>

                        <span class="font-medium">Settings</span>
                    </a>

                </nav>

                {{-- User Profile Card --}}
                <div class="px-6 pb-8">
                    <div class="profile-card p-4 rounded-2xl" style="background: linear-gradient(135deg, 
                                            rgba(201, 169, 110, 0.1) 0%, 
                                            rgba(168, 138, 90, 0.05) 100%);
                                border: 1px solid var(--border-subtle);">

                        <div class="flex items-center gap-3">
                            {{-- Avatar --}}
                            <div class="relative">
                                <div class="w-12 h-12 rounded-xl overflow-hidden"
                                    style="background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);">
                                    <div
                                        class="w-full h-full flex items-center justify-center text-white font-semibold text-lg">
                                        A
                                    </div>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2"
                                    style="background: #10b981; border-color: var(--warm-white);"></div>
                            </div>

                            {{-- User Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate" style="color: var(--charcoal);">
                                    Admin User
                                </p>
                                <p class="text-xs truncate" style="color: var(--charcoal); opacity: 0.6;">
                                    admin@alphaseum.com
                                </p>
                            </div>

                            {{-- Logout Button --}}
                            <button class="p-2 rounded-lg hover:bg-white/50" style="color: var(--charcoal);">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="flex-1 overflow-auto">

            {{-- Top Bar --}}
            <header class="sticky top-0 z-10 px-10 py-6 border-b blur-bg" style="background: rgba(247, 244, 239, 0.9); 
                           border-color: var(--border-subtle);">

                <div class="flex items-center justify-between">

                    {{-- Breadcrumb / Title --}}
                    <div>
                        <div class="flex items-center gap-2 text-sm mb-1" style="color: var(--charcoal); opacity: 0.6;">
                            <span>Admin</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                            <span>@yield('breadcrumb', 'Dashboard')</span>
                        </div>
                        <h2 class="museum-title text-3xl font-light" style="color: var(--charcoal);">
                            @yield('page-title', 'Dashboard')
                        </h2>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="flex items-center gap-3">

                        {{-- Search --}}
                        <button class="p-3 rounded-xl hover:bg-white/80"
                            style="background: rgba(255,255,255,0.5); color: var(--charcoal);">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </button>

                        {{-- Notifications --}}
                        <button class="relative p-3 rounded-xl hover:bg-white/80"
                            style="background: rgba(255,255,255,0.5); color: var(--charcoal);">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>
                            <span class="absolute top-2 right-2 w-2 h-2 rounded-full badge-pulse"
                                style="background: #ef4444;"></span>
                        </button>

                        {{-- Add New --}}
                        <button class="px-5 py-3 rounded-xl font-medium flex items-center gap-2 hover:shadow-lg" style="background: linear-gradient(135deg, var(--charcoal) 0%, var(--charcoal-light) 100%); 
                                       color: var(--warm-white);">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            <span>Add New</span>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <div class="p-10">
                @yield('content')
            </div>

        </main>

    </div>

</body>

</html>