@extends('client.layouts.app')

@section('title', 'Mon panier')

@section('content')
<div class="bg-white text-neutral-900 min-h-screen">
    
    @if(isset($announcementsByPosition['top']) && $announcementsByPosition['top']->isNotEmpty())
        <div class="bg-black text-white text-center py-3 text-xs tracking-[0.2em] uppercase border-b border-gold">
            <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-center gap-4 md:gap-8">
                @foreach($announcementsByPosition['top'] as $announcement)
                    <span class="font-light">{{ $announcement->content ?? $announcement->message }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 py-8 md:py-12">
        
        <nav class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-8">
            <a href="{{ route('client.catalog') }}" class="hover:text-black transition-colors">Accueil</a>
            <span class="mx-2 text-gold">✦</span>
            <span class="text-neutral-900 font-medium">Panier</span>
        </nav>

        <div class="border-b border-neutral-100 pb-6 mb-8">
            <h1 class="text-3xl md:text-4xl font-serif font-light tracking-wide text-neutral-900">Mon panier</h1>
            <p class="text-neutral-500 text-sm mt-2" id="cart-count-display">0 article(s) dans votre panier</p>
        </div>

        <!-- Conteneur dynamique du panier -->
        <div id="cart-container">
            <div class="text-center py-16 border border-neutral-100 bg-neutral-50/40" id="empty-cart-message">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-neutral-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-serif font-light text-neutral-900 mb-2">Votre panier est vide</h2>
                <p class="text-neutral-500 text-sm mb-6">Découvrez notre collection et trouvez l'inspiration.</p>
                <a href="{{ route('client.catalog') }}" class="inline-flex items-center gap-2 border border-neutral-900 hover:bg-neutral-900 hover:text-white text-neutral-900 text-[11px] uppercase tracking-[0.2em] px-6 py-3 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    Commencer mes achats
                </a>
            </div>
            
            <div id="cart-items-list" class="grid grid-cols-1 lg:grid-cols-3 gap-8 hidden">
                <div class="lg:col-span-2 space-y-4" id="cart-items"></div>
                <div class="lg:col-span-1">
                    <div class="border border-neutral-100 bg-neutral-50/30 sticky top-24">
                        <div class="p-5 border-b border-neutral-100">
                            <h2 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900">Récapitulatif</h2>
                        </div>
                        <div class="p-5 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500">Sous-total</span>
                                <span class="text-neutral-900 font-medium" id="cart-subtotal">0 FC</span>
                            </div>
                            <!-- <div class="flex justify-between text-sm">
                                <span class="text-neutral-500">Livraison</span>
                                <span class="text-neutral-900" id="cart-delivery">Offerte</span>
                            </div> -->
                            <div class="border-t border-neutral-200 pt-3 mt-2">
                                <div class="flex justify-between font-semibold text-base">
                                    <span class="text-neutral-900">Total</span>
                                    <span class="text-gold text-lg" id="cart-total">0 FC</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-5 border-t border-neutral-100 bg-white">
                            <a href="{{ route('client.checkout.index') }}" class="w-full block text-center bg-neutral-900 hover:bg-gold hover:text-black text-white py-3 transition-all duration-300 uppercase tracking-[0.2em] text-[11px] font-semibold">
                                Procéder à la commande
                            </a>
                            <p class="text-[10px] text-neutral-400 text-center mt-3">-====================================================-</p>
                        </div>
                        <button id="clear-cart" class="w-full mt-3 text-center text-[10px] text-neutral-400 hover:text-red-500 transition-colors">
                            Vider le panier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($announcementsByPosition['bottom']) && $announcementsByPosition['bottom']->isNotEmpty())
        <div class="bg-neutral-50 py-8 text-center border-t border-neutral-100 mt-12">
            <div class="max-w-7xl mx-auto px-4">
                @foreach($announcementsByPosition['bottom'] as $announcement)
                    <p class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 font-light">
                        {{ $announcement->content ?? $announcement->message }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    // Configuration
    const currencyCode = '{{ $currency->code ?? 'CDF' }}';
    const currencySymbol = '{{ $currency->symbol ?? 'FC' }}';
    const currencyRate = {{ $currency->rate ?? 2250 }};
    
    // Formater un prix
    function formatPrice(price) {
        if (currencyCode === 'CDF') {
            return currencySymbol + ' ' + (price * currencyRate).toLocaleString('fr-CD');
        }
        return currencySymbol + ' ' + price.toFixed(2).replace('.', ',');
    }
    
    // Récupérer le panier
    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }
    
    // Sauvegarder le panier
    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartBadge();
        renderCart();
    }
    
    // Mettre à jour le badge
    function updateCartBadge() {
        const cart = getCart();
        const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
        const badge = document.getElementById('cart-badge-count');
        if (badge) badge.innerText = totalItems;
    }
    
    // Supprimer un article
    function removeFromCart(productId) {
        let cart = getCart();
        cart = cart.filter(item => item.id != productId);
        saveCart(cart);
        showToast('Produit retiré du panier', 'success');
    }
    
    // Mettre à jour la quantité
    function updateQuantity(productId, newQuantity) {
        let cart = getCart();
        const index = cart.findIndex(item => item.id == productId);
        
        if (index !== -1) {
            if (newQuantity <= 0) {
                cart.splice(index, 1);
                showToast('Produit retiré du panier', 'success');
            } else {
                cart[index].quantity = newQuantity;
            }
            saveCart(cart);
        }
    }
    
    // Vider le panier
    function clearCart() {
        if (confirm('Vider tout le panier ?')) {
            localStorage.setItem('cart', '[]');
            renderCart();
            updateCartBadge();
            showToast('Panier vidé avec succès', 'success');
        }
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
    
    // Échapper le HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Afficher le panier
    function renderCart() {
        const cart = getCart();
        const emptyDiv = document.getElementById('empty-cart-message');
        const cartList = document.getElementById('cart-items-list');
        const cartItemsContainer = document.getElementById('cart-items');
        const countDisplay = document.getElementById('cart-count-display');
        
        if (!cartItemsContainer) return;
        
        // Dédupliquer le panier
        const uniqueCart = [];
        const ids = new Set();
        for (const item of cart) {
            if (!ids.has(item.id)) {
                ids.add(item.id);
                uniqueCart.push({...item});
            } else {
                const existing = uniqueCart.find(i => i.id == item.id);
                if (existing) {
                    existing.quantity += item.quantity;
                    if (existing.quantity > 99) existing.quantity = 99;
                }
            }
        }
        
        // Sauvegarder la version propre
        if (uniqueCart.length !== cart.length) {
            localStorage.setItem('cart', JSON.stringify(uniqueCart));
        }
        
        const finalCart = uniqueCart;
        
        if (finalCart.length === 0) {
            if (emptyDiv) emptyDiv.classList.remove('hidden');
            if (cartList) cartList.classList.add('hidden');
            if (countDisplay) countDisplay.innerText = '0 article(s) dans votre panier';
            return;
        }
        
        if (emptyDiv) emptyDiv.classList.add('hidden');
        if (cartList) cartList.classList.remove('hidden');
        
        let subtotal = 0;
        let html = '';
        
        for (const item of finalCart) {
            let quantity = item.quantity;
            if (quantity > 99) quantity = 99;
            if (quantity < 1) continue;
            
            const itemTotal = item.price * quantity;
            subtotal += itemTotal;
            
            html += `
                <div class="flex gap-4 p-4 border border-neutral-100 bg-white hover:shadow-sm transition-all duration-300" data-product-id="${item.id}">
                    <div class="w-24 h-24 bg-neutral-50 flex-shrink-0 overflow-hidden">
                        <img src="${item.image || '/images/placeholder.jpg'}" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.jpg'">
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap justify-between gap-2 mb-2">
                            <h3 class="font-medium text-neutral-900 text-sm">
                                <a href="/produit/${item.slug || item.id}" class="hover:text-gold transition-colors">${escapeHtml(item.name)}</a>
                            </h3>
                            <p class="font-semibold text-neutral-900 text-sm">${formatPrice(item.price)}</p>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center border border-neutral-200">
                                <button class="cart-decrease w-8 h-8 flex items-center justify-center hover:bg-neutral-100 transition-colors text-neutral-600" data-id="${item.id}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <span class="cart-quantity w-10 text-center text-sm text-neutral-900" data-id="${item.id}">${quantity}</span>
                                <button class="cart-increase w-8 h-8 flex items-center justify-center hover:bg-neutral-100 transition-colors text-neutral-600" data-id="${item.id}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            <button class="cart-remove text-neutral-400 hover:text-red-500 transition-colors text-[10px] uppercase tracking-wider flex items-center gap-1" data-id="${item.id}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (cartItemsContainer) cartItemsContainer.innerHTML = html;
        
        // Frais de livraison (gratuit > 50$)
        let deliveryFee = 0;
        let deliveryHtml = 'Offerte';
        if (subtotal < 50 && subtotal > 0) {
            deliveryFee = 3;
            deliveryHtml = formatPrice(3);
        }
        const total = subtotal + deliveryFee;
        
        const subtotalEl = document.getElementById('cart-subtotal');
        const deliveryEl = document.getElementById('cart-delivery');
        const totalEl = document.getElementById('cart-total');
        
        if (subtotalEl) subtotalEl.innerHTML = formatPrice(subtotal);
        if (deliveryEl) deliveryEl.innerHTML = deliveryHtml;
        if (totalEl) totalEl.innerHTML = formatPrice(total);
        
        if (countDisplay) {
            const totalItems = finalCart.reduce((sum, item) => sum + (item.quantity || 0), 0);
            countDisplay.innerText = `${totalItems} article(s) dans votre panier`;
        }
        
        // Attacher les événements
        attachCartEvents();
    }
    
    // Attacher les événements du panier
    function attachCartEvents() {
        document.querySelectorAll('.cart-increase').forEach(btn => {
            btn.removeEventListener('click', handleIncrease);
            btn.addEventListener('click', handleIncrease);
        });
        
        document.querySelectorAll('.cart-decrease').forEach(btn => {
            btn.removeEventListener('click', handleDecrease);
            btn.addEventListener('click', handleDecrease);
        });
        
        document.querySelectorAll('.cart-remove').forEach(btn => {
            btn.removeEventListener('click', handleRemove);
            btn.addEventListener('click', handleRemove);
        });
        
        const clearBtn = document.getElementById('clear-cart');
        if (clearBtn) {
            clearBtn.removeEventListener('click', handleClear);
            clearBtn.addEventListener('click', handleClear);
        }
    }
    
    function handleIncrease(e) {
        const id = parseInt(e.currentTarget.dataset.id);
        const quantitySpan = document.querySelector(`.cart-quantity[data-id="${id}"]`);
        if (quantitySpan) {
            let qty = parseInt(quantitySpan.innerText);
            if (qty < 99) {
                updateQuantity(id, qty + 1);
            } else {
                showToast('Quantité maximale : 99', 'warning');
            }
        }
    }
    
    function handleDecrease(e) {
        const id = parseInt(e.currentTarget.dataset.id);
        const quantitySpan = document.querySelector(`.cart-quantity[data-id="${id}"]`);
        if (quantitySpan) {
            let qty = parseInt(quantitySpan.innerText);
            if (qty > 1) {
                updateQuantity(id, qty - 1);
            }
        }
    }
    
    function handleRemove(e) {
        const id = parseInt(e.currentTarget.dataset.id);
        if (confirm('Supprimer cet article du panier ?')) {
            removeFromCart(id);
        }
    }
    
    function handleClear() {
        if (confirm('Vider tout le panier ?')) {
            clearCart();
        }
    }
    
    // Initialiser
    document.addEventListener('DOMContentLoaded', function() {
        renderCart();
    });
</script>

@push('styles')
<style>
    .cart-item {
        transition: all 0.2s ease;
    }
    .cart-item:hover {
        border-color: #D4AF37;
    }
    .hidden {
        display: none;
    }
</style>
@endpush
@endsection