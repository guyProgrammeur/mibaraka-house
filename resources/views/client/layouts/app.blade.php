<!DOCTYPE html>
<html lang="fr" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $company->description ?? 'Découvrez la collection exclusive Mibaraka House - Luxe, élégance et authenticité' }}">
    <title>{{ $company->name ?? 'Mibaraka House' }} | Haute Parfumerie & Cosmétique</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

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
                            light: '#F3E5AB',
                            dark: '#AA7C11'
                        }
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Inter', 'sans-serif'; }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f5f5f5; }
        ::-webkit-scrollbar-thumb { background: #111111; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #D4AF37; }
        
        html { scroll-behavior: smooth; }
        
        .loader {
            width: 20px;
            height: 20px;
            border: 2px solid white;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            animation: rotation 1s linear infinite;
        }
        
        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] { -moz-appearance: textfield; }
        
        [data-tooltip] { position: relative; cursor: pointer; }
        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #111;
            color: white;
            padding: 4px 8px;
            font-size: 10px;
            white-space: nowrap;
            border-radius: 4px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s;
            pointer-events: none;
            z-index: 50;
        }
        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            margin-bottom: 8px;
        }
        
        [x-cloak] { display: none !important; }
        
        .container-custom {
            width: 100%;
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (min-width: 640px) {
            .container-custom { padding-left: 1.5rem; padding-right: 1.5rem; }
        }
        @media (min-width: 1024px) {
            .container-custom { padding-left: 2rem; padding-right: 2rem; }
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Toast notification */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* Badge animation */
        #cart-badge-count {
            transition: transform 0.2s ease;
        }
        .scale-125 {
            transform: scale(1.25);
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col h-full text-neutral-900 font-sans antialiased selection:bg-neutral-900 selection:text-white" 
      x-data="{ 
          mobileMenuOpen: false,
          scrollTop: false,
          searchOpen: false
      }" 
      x-init="window.addEventListener('scroll', () => { scrollTop = window.scrollY > 300 })">

    <!-- ================= TOP BAR ================= -->
    <div class="bg-neutral-950 text-white border-b border-neutral-900 text-[11px] tracking-[0.2em] uppercase">
        <div class="container-custom h-9 flex items-center justify-between">
            <div class="hidden sm:block text-neutral-400 font-light">
                {{ $company->slogan ?? 'Mibaraka House — Univers d\'Excellence' }}
            </div>
            
            <div class="flex items-center gap-4 ml-auto">
                <span class="text-neutral-500 font-light text-[10px]">Devise :</span>
                <div class="relative inline-block text-left group">
                    <button class="text-white hover:text-gold transition-colors font-medium focus:outline-none flex items-center gap-1 text-[10px]">
                        {{ session('currency', 'CDF') }}
                        <span class="text-[8px] text-neutral-400">▼</span>
                    </button>
                    <div class="absolute right-0 top-full mt-2 w-24 bg-white border border-neutral-100 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('client.currency', 'USD') }}" class="block px-3 py-1.5 text-xs text-neutral-800 hover:bg-neutral-50 hover:text-gold {{ session('currency') == 'USD' ? 'font-semibold text-gold' : '' }}">USD ($)</a>
                            <a href="{{ route('client.currency', 'CDF') }}" class="block px-3 py-1.5 text-xs text-neutral-800 hover:bg-neutral-50 hover:text-gold {{ session('currency') == 'CDF' ? 'font-semibold text-gold' : '' }}">CDF (FC)</a>
                        </div>
                    </div>
                </div>
                
                <a href="https://wa.me/{{ $company->whatsapp ?? '243000000000' }}" target="_blank" 
                   class="hidden md:flex items-center gap-1 text-neutral-400 hover:text-gold transition-colors" data-tooltip="Contactez-nous sur WhatsApp">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.032 2.002c-5.514 0-9.998 4.48-9.998 9.99 0 1.76.457 3.486 1.33 5.01l-1.43 4.87 4.98-1.4c1.47.8 3.12 1.23 4.81 1.23 5.51 0 9.99-4.48 9.99-9.99 0-5.51-4.48-9.99-9.99-9.99z"/>
                    </svg>
                    <span class="text-[9px]">WhatsApp</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ================= MAIN NAVIGATION ================= -->
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-neutral-100 transition-all duration-300">
        <div class="container-custom py-3">
            <div class="flex items-center justify-between gap-4">
                <div class="shrink-0">
                    <a href="{{ route('client.catalog') }}" class="text-xl md:text-2xl font-serif tracking-[0.2em] font-bold uppercase text-neutral-900 hover:text-gold transition-colors">
                        {{ $company->short_name ?? 'Mibaraka' }}<span class="text-gold font-normal">.H</span>
                    </a>
                </div>

                <div class="hidden md:block flex-1 max-w-xl mx-6">
                    <form action="{{ route('client.search') }}" method="GET" class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit..." 
                               class="w-full bg-neutral-50 text-sm border border-neutral-200 px-5 py-2.5 pl-12 focus:outline-none focus:border-neutral-900 focus:bg-white transition-all placeholder-neutral-400 rounded-full">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-neutral-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-neutral-900 text-white text-xs px-4 py-1.5 rounded-full hover:bg-gold hover:text-black transition-colors">
                            OK
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-4 text-neutral-900">
                    <button @click="searchOpen = !searchOpen" class="md:hidden hover:text-gold transition-colors p-1" title="Rechercher">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden hover:text-gold transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <a href="{{ route('client.cart.index') }}" class="relative group p-1 flex items-center gap-2 hover:text-gold transition-colors">
                        <div class="relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1,0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0,1-1.12-1.243l1.264-12A1.125 1.125 0 0,1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1,1-.75 0 .375.375 0 0,1 .75 0Zm7.5 0a.375.375 0 1,1-.75 0 .375.375 0 0,1 .75 0Z" />
                            </svg>
                            <span class="absolute -top-1.5 -right-2 bg-neutral-900 text-gold text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-neutral-700 font-sans transition-colors" 
                                  id="cart-badge-count">0</span>
                        </div>
                        <span class="text-[11px] uppercase tracking-[0.2em] hidden lg:inline font-medium">Panier</span>
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex items-center justify-center gap-8 text-xs uppercase tracking-[0.2em] mt-4 pt-2 border-t border-neutral-100">
                <a href="{{ route('client.catalog') }}" class="hover:text-gold transition-colors font-medium py-2">Accueil</a>
                <a href="{{ route('client.catalog') }}?sort=newest" class="hover:text-gold transition-colors py-2">Nouveautés</a>
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $cat)
                        <a href="{{ route('client.category', $cat->slug) }}" class="hover:text-gold transition-colors py-2">{{ $cat->name }}</a>
                    @endforeach
                @endif
            </div>
        </div>
        
        <div x-show="searchOpen" x-cloak x-transition.duration.300ms class="md:hidden px-4 pb-4 border-t border-neutral-100 bg-white">
            <form action="{{ route('client.search') }}" method="GET" class="relative mt-3">
                <input type="text" name="q" value="{{ request('q') }}" 
                       placeholder="Rechercher un produit..."
                       class="w-full bg-neutral-50 text-sm border border-neutral-200 rounded-full px-5 py-3 pl-12 focus:outline-none focus:border-gold transition">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-neutral-900 text-white text-xs px-4 py-1.5 rounded-full hover:bg-gold hover:text-black transition-colors">
                    OK
                </button>
            </form>
        </div>
        
        <div x-show="mobileMenuOpen" x-cloak x-transition.duration.300ms class="lg:hidden bg-white border-t border-neutral-100 max-h-[70vh] overflow-y-auto shadow-lg">
            <div class="px-4 py-4 space-y-1">
                <a href="{{ route('client.catalog') }}" class="block py-3 text-sm uppercase tracking-wider hover:text-gold transition border-b border-neutral-50">Accueil</a>
                <a href="{{ route('client.catalog') }}?sort=newest" class="block py-3 text-sm uppercase tracking-wider hover:text-gold transition border-b border-neutral-50">Nouveautés</a>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <a href="{{ route('client.category', $cat->slug) }}" class="block py-3 text-sm uppercase tracking-wider hover:text-gold transition border-b border-neutral-50">{{ $cat->name }}</a>
                    @endforeach
                @endif
                <a href="https://wa.me/{{ $company->whatsapp ?? '243000000000' }}" target="_blank" class="block py-3 text-sm uppercase tracking-wider hover:text-gold transition flex items-center gap-2">
                    <i class="fab fa-whatsapp text-gold"></i> WhatsApp
                </a>
            </div>
        </div>
    </nav>

    <!-- ================= TOAST NOTIFICATION ================= -->
    <div id="toast-notification" class="toast-notification hidden">
        <div class="bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="toast-message">Produit ajouté au panier</span>
        </div>
    </div>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-neutral-950 text-neutral-400 pt-14 pb-8 border-t border-neutral-900 text-xs font-light">
        <div class="container-custom grid grid-cols-1 md:grid-cols-4 gap-10 border-b border-neutral-900 pb-10 mb-8">
            <div>
                <h4 class="text-white font-serif tracking-[0.2em] uppercase text-sm mb-4 font-medium">La Maison</h4>
                <p class="leading-relaxed text-neutral-400 font-light text-xs">
                    {{ $company->description ?? 'Distributeur exclusif de produits authentiques, alliant rituels de soins ancestraux et essences d\'exceptions modernes.' }}
                </p>
            </div>

            <div>
                <h4 class="text-white font-serif tracking-[0.2em] uppercase text-sm mb-4 font-medium">Collections</h4>
                <ul class="space-y-2 uppercase tracking-wider text-[11px]">
                    <li><a href="{{ route('client.catalog') }}" class="hover:text-gold transition-colors">Tous les produits</a></li>
                    <li><a href="{{ route('client.catalog') }}?sort=newest" class="hover:text-gold transition-colors">Nouveautés</a></li>
                    <li><a href="{{ route('client.catalog') }}?sort=price_asc" class="hover:text-gold transition-colors">Meilleurs prix</a></li>
                </ul>
            </div>

            @if(isset($categories) && $categories->count() > 0)
            <div>
                <h4 class="text-white font-serif tracking-[0.2em] uppercase text-sm mb-4 font-medium">Catégories</h4>
                <ul class="space-y-2 uppercase tracking-wider text-[11px]">
                    @foreach($categories->take(5) as $cat)
                        <li><a href="{{ route('client.category', $cat->slug) }}" class="hover:text-gold transition-colors">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <h4 class="text-white font-serif tracking-[0.2em] uppercase text-sm mb-4 font-medium">Service Client</h4>
                <p class="text-neutral-400 mb-2 text-xs">Besoin d'un conseil personnalisé ?</p>
                <div class="text-gold font-medium tracking-wide text-sm">
                    {{ $company->formatted_phone ?? '+243 000 000 000' }}
                </div>
                <p class="text-neutral-500 text-[11px] mt-1">{{ $company->email ?? 'contact@mibaraka.com' }}</p>
                <div class="mt-4 flex gap-4">
                    <a href="https://wa.me/{{ $company->whatsapp ?? '243000000000' }}" target="_blank" class="text-neutral-500 hover:text-gold transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.032 2.002c-5.514 0-9.998 4.48-9.998 9.99 0 1.76.457 3.486 1.33 5.01l-1.43 4.87 4.98-1.4c1.47.8 3.12 1.23 4.81 1.23 5.51 0 9.99-4.48 9.99-9.99 0-5.51-4.48-9.99-9.99-9.99z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/{{ $company->instagram ?? '' }}" target="_blank" class="text-neutral-500 hover:text-gold transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="container-custom flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] uppercase tracking-[0.2em] text-neutral-500">
            <div>&copy; {{ date('Y') }} {{ $company->name ?? 'Mibaraka House' }}. Tous droits réservés.</div>
            <div class="flex gap-6 font-light">
                <span>Sélection Premium</span>
                <span class="text-gold">✦</span>
                <span>Kinshasa, RDC</span>
                <span class="text-gold">✦</span>
                <span>Livraison Express</span>
            </div>
        </div>
    </footer>

    <button x-show="scrollTop" x-cloak @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="fixed bottom-6 right-6 w-10 h-10 bg-neutral-900 text-white rounded-full shadow-lg hover:bg-gold hover:text-black transition-all duration-300 flex items-center justify-center z-40"
            data-tooltip="Retour en haut">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Configuration devise
        const currencyCode = '{{ session('currency', 'CDF') }}';
        const currencySymbol = currencyCode === 'CDF' ? 'FC' : '$';
        const currencyRate = {{ $currency->rate ?? 2250 }};
        
        // Formater un prix
        function formatPrice(price) {
            if (currencyCode === 'CDF') {
                return currencySymbol + ' ' + (price * currencyRate).toLocaleString('fr-CD');
            }
            return currencySymbol + ' ' + price.toFixed(2).replace('.', ',');
        }
        
        // Mettre à jour le badge
        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
            const badge = document.getElementById('cart-badge-count');
            if (badge) {
                badge.innerText = totalItems;
            }
            return totalItems;
        }
        
        // Afficher une notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                const messageEl = document.getElementById('toast-message');
                const toastDiv = toast.querySelector('div');
                
                messageEl.innerText = message;
                toastDiv.classList.remove('bg-green-500', 'bg-red-500', 'bg-yellow-500', 'bg-blue-500');
                
                if (type === 'success') toastDiv.classList.add('bg-green-500');
                else if (type === 'error') toastDiv.classList.add('bg-red-500');
                else if (type === 'warning') toastDiv.classList.add('bg-yellow-500');
                else toastDiv.classList.add('bg-blue-500');
                
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }
        }
        
        // Fonction globale pour ajouter au panier
        window.addToCart = function(productId, productName, productPrice, productImage, quantity = 1, redirectToCart = false) {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            
            // Vérifier si le produit existe déjà
            const existingIndex = cart.findIndex(item => item.id == productId);
            
            let newQuantity = quantity;
            let message = '';
            
            if (existingIndex !== -1) {
                // Produit existe déjà
                newQuantity = cart[existingIndex].quantity + quantity;
                // Limiter à 99
                if (newQuantity > 99) {
                    newQuantity = 99;
                    showToast(`${productName} : quantité maximale atteinte (99)`, 'warning');
                } else {
                    cart[existingIndex].quantity = newQuantity;
                    message = `${productName} : +${quantity} (total: ${newQuantity})`;
                    showToast(message, 'info');
                }
            } else {
                // Nouveau produit
                const finalQuantity = Math.min(quantity, 99);
                cart.push({
                    id: parseInt(productId),
                    name: productName,
                    price: parseFloat(productPrice),
                    image: productImage,
                    quantity: finalQuantity,
                    slug: productName.toLowerCase().replace(/[^a-z0-9]+/g, '-')
                });
                message = `${productName} ajouté au panier !`;
                showToast(message, 'success');
            }
            
            // Sauvegarder
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Mettre à jour le badge
            const totalItems = updateCartBadge();
            
            // Animation sur le badge
            const badge = document.getElementById('cart-badge-count');
            if (badge) {
                badge.classList.add('scale-125');
                setTimeout(() => badge.classList.remove('scale-125'), 200);
            }
            
            // Feedback sur le bouton
            const btn = event?.target?.closest('button');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="loader"></span>';
                btn.disabled = true;
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 500);
            }
            
            // Rediriger vers le panier si demandé
            if (redirectToCart) {
                setTimeout(() => {
                    window.location.href = '/cart';
                }, 800);
            }
            
            return { success: true, totalItems, newQuantity };
        };
        
        // Fonction pour vérifier si un produit est déjà dans le panier
        window.isInCart = function(productId) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            return cart.find(item => item.id == productId);
        };
        
        // Fonction pour obtenir la quantité
        window.getCartQuantity = function(productId) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const item = cart.find(item => item.id == productId);
            return item ? item.quantity : 0;
        };
        
        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            updateCartBadge();
        });
    </script>
    @stack('scripts')
</body>
</html>