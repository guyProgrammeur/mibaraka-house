<!DOCTYPE html>
<html lang="fr" class="h-full bg-neutral-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Espace Administratif | {{ $company->name ?? 'Mibaraka House' }}</title>
    
    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        gold: {
                            DEFAULT: '#D4AF37',
                            dark: '#AA7C11',
                            light: '#F3E5AB'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Styles de scrollbar professionnels */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #111111; }
        ::-webkit-scrollbar-thumb { background: #D4AF37; }
        [x-cloak] { display: none !important; }
        
        /* Mobile menu overlay */
        .mobile-menu-open {
            overflow: hidden;
        }
        
        /* Responsive tables */
        @media (max-width: 768px) {
            .responsive-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-neutral-900 bg-neutral-50 flex flex-col md:flex-row" x-data="{ sidebarOpen: false }" :class="{ 'mobile-menu-open': sidebarOpen }">

    <!-- ================= BOUTON MENU MOBILE ================= -->
    <button @click="sidebarOpen = !sidebarOpen" 
            class="fixed top-4 left-4 z-50 md:hidden bg-neutral-900 text-white p-2 rounded-lg shadow-lg transition-all duration-300"
            :class="{ 'left-64': sidebarOpen }">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" x-show="!sidebarOpen"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-show="sidebarOpen"></path>
        </svg>
    </button>

    <!-- Overlay pour mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" 
         x-cloak
         class="fixed inset-0 bg-black/50 z-30 md:hidden transition-opacity duration-300"></div>

    <!-- ================= BARRE LATÉRALE DE NAVIGATION (SIDEBAR) ================= -->
    <aside class="fixed md:relative inset-y-0 left-0 w-64 bg-neutral-950 text-neutral-400 flex flex-col justify-between shrink-0 border-r border-neutral-900 z-40 transition-transform duration-300 transform"
           :class="{ '-translate-x-full md:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }">
        
        <div class="overflow-y-auto flex-1">
            <!-- Header Sidebar (Brand Logo) -->
            <div class="h-16 flex items-center px-6 bg-black border-b border-neutral-900">
                <a href="{{ route('admin.dashboard') }}" class="text-md font-serif font-semibold tracking-widest text-white uppercase">
                    Mibaraka<span class="text-gold">.H</span> <span class="text-[10px] font-sans font-light tracking-normal text-neutral-500 lowercase">admin</span>
                </a>
            </div>

            <!-- Liens de Gestion Organisés -->
            <nav class="p-4 space-y-7">
                <!-- Section : Vue d'ensemble -->
                <div>
                    <span class="px-3 text-[10px] uppercase tracking-widest font-semibold text-neutral-600 block mb-2">Activité</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.dashboard') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                                <span class="hidden md:inline">Tableau de bord</span>
                                <span class="md:hidden">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.orders.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Commandes
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section : Catalogue d'Exception -->
                <div>
                    <span class="px-3 text-[10px] uppercase tracking-widest font-semibold text-neutral-600 block mb-2">Boutique</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.categories.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Catégories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.products.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Produits
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.reviews.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                Avis produits
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.customers.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Fichier Clients
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section : Marketing & Système -->
                <div>
                    <span class="px-3 text-[10px] uppercase tracking-widest font-semibold text-neutral-600 block mb-2">Outils & Comm</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.announcements.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.418.586l-4.331-8.167a2.415 2.415 0 00-1.248-.996L1 9.584l3.003-.518a2.416 2.416 0 001.248-.996l4.331-8.167a1.76 1.76 0 013.418.586zM14 15a4 4 0 000-8v8z"></path></svg>
                                Rubriques Annonces
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.currencies.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.currencies.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Devises
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.qr-codes.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.qr-codes.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                Codes QR
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.company.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-wider transition-colors rounded-sm {{ request()->routeIs('admin.company.*') ? 'bg-neutral-900 text-white border-l-2 border-gold font-medium' : 'hover:text-white hover:bg-neutral-900/50' }}">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0V11m0 5h12"></path></svg>
                                Identité
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Section Basse : Sessions & Lien Public -->
        <div class="p-4 border-t border-neutral-900 bg-neutral-950">
            <a href="{{ route('client.catalog') }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-[11px] uppercase tracking-widest text-gold bg-neutral-900 hover:bg-gold hover:text-black transition-all mb-3 rounded-sm font-medium">
                <span class="hidden md:inline">Voir la Boutique</span>
                <span class="md:hidden">Boutique</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 00-2 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
            <div class="text-[11px] text-neutral-600 px-3 text-center md:text-left">
                Connecté en tant qu'Admin
            </div>
        </div>
    </aside>

    <!-- ================= CONTENEUR CENTRAL ET ZONE DE TRAVAIL ================= -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto md:ml-0">
        
        <!-- BARRE DE COMMANDE SUPÉRIEURE (TOP NAVBAR) -->
        <header class="bg-white border-b border-neutral-200 shrink-0 px-4 md:px-6 py-3 md:py-0 md:h-16 flex flex-wrap items-center justify-between gap-3 sticky top-0 z-20">
            <!-- Emplacement / Titrage Dynamique Interne -->
            <div class="flex items-center gap-2 md:gap-4">
                <div class="w-8 md:hidden"></div> <!-- Espace pour le bouton menu mobile -->
                <span class="text-[10px] md:text-xs text-neutral-400 font-light uppercase tracking-wider hidden sm:inline">Mibaraka Management Studio</span>
            </div>

            <!-- Actions Systèmes Globales Directes & Profil -->
            <div class="flex items-center gap-2 md:gap-4 flex-wrap justify-end">
                
                <!-- Action Système : Purger le Cache -->
                <form action="{{ route('admin.cache.clear') }}" method="POST" title="Vider les caches applicatifs">
                    @csrf
                    <button type="submit" class="border border-neutral-200 hover:border-black text-neutral-600 hover:text-black px-2 md:px-2.5 py-1.5 text-[9px] md:text-[10px] uppercase tracking-widest transition-colors flex items-center gap-1 rounded-sm">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span class="hidden sm:inline">Vider Cache</span>
                        <span class="sm:hidden">Cache</span>
                    </button>
                </form>

                <!-- Action Système : Commuter la Maintenance -->
                <form action="{{ route('admin.maintenance.toggle') }}" method="POST">
                    @csrf
                    @if(app()->isDownForMaintenance())
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 md:px-2.5 py-1.5 text-[9px] md:text-[10px] uppercase tracking-widest font-medium transition-colors rounded-sm">
                            <span class="hidden sm:inline">Ouvrir le Site</span>
                            <span class="sm:hidden">Ouvrir</span>
                        </button>
                    @else
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-2 md:px-2.5 py-1.5 text-[9px] md:text-[10px] uppercase tracking-widest font-medium transition-colors rounded-sm" title="Activer l'écran de maintenance pour les clients">
                            <span class="hidden sm:inline">Maintenance</span>
                            <span class="sm:hidden">Maint.</span>
                        </button>
                    @endif
                </form>

                <div class="h-4 w-[1px] bg-neutral-200 mx-1 hidden sm:block"></div>

                <!-- Déconnexion Sécurisée -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-neutral-500 hover:text-red-600 text-[10px] md:text-xs uppercase tracking-wider font-medium transition-colors">
                        <span class="hidden sm:inline">Déconnexion</span>
                        <span class="sm:hidden">Exit</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- ZONE DE NOTIFICATIONS ACCESSIBLES (ALERT FLUSHES) -->
        @if(session('success') || session('error') || $errors->any())
            <div class="px-4 md:px-8 pt-4 md:pt-6">
                @if(session('success'))
                    <div class="bg-white border-l-4 border-emerald-500 shadow-sm p-3 md:p-4 text-xs text-neutral-800 flex items-center justify-between rounded-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold text-sm">✓</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-white border-l-4 border-red-500 shadow-sm p-3 md:p-4 text-xs text-neutral-800 flex items-center justify-between rounded-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-red-500 font-bold text-sm">✕</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-white border-l-4 border-red-400 shadow-sm p-3 md:p-4 text-xs text-neutral-800 rounded-sm">
                        <p class="font-semibold mb-1 text-red-600">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="list-disc list-inside space-y-0.5 text-neutral-600">
                            @foreach ($errors->all() as $error)
                                <li class="text-xs break-words">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <!-- CONTENU TECHNIQUE ET INJECTION DE VUE CRUE -->
        <main class="flex-grow p-3 md:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')
</body>
</html>