@extends('client.layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
<div class="bg-white text-neutral-900 min-h-screen">
    
    <!-- ================= ANNONCES TOP ================= -->
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
        
        <!-- ================= FIL D'ARIANE ================= -->
        <nav class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-8">
            <a href="{{ route('client.catalog') }}" class="hover:text-black transition-colors">Accueil</a>
            <span class="mx-2 text-gold">✦</span>
            <a href="{{ route('client.cart.index') }}" class="hover:text-black transition-colors">Panier</a>
            <span class="mx-2 text-gold">✦</span>
            <span class="text-neutral-900 font-medium">Commander</span>
        </nav>

        <div class="border-b border-neutral-100 pb-6 mb-8">
            <h1 class="text-3xl md:text-4xl font-serif font-light tracking-wide text-neutral-900">Finaliser la commande</h1>
            <p class="text-neutral-500 text-sm mt-2">Veuillez compléter vos informations pour finaliser votre achat</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- ================= FORMULAIRE ================= -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Informations de livraison -->
                <div class="border border-neutral-100 p-6 bg-neutral-50/40">
                    <h2 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-5 pb-2 border-b border-neutral-200 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-neutral-900 text-white text-[11px] flex items-center justify-center">1</span>
                        Informations de livraison
                    </h2>
                    
                    <form action="{{ route('client.checkout.store') }}" method="POST" id="checkoutForm" class="space-y-5">
                        @csrf
                        <input type="hidden" name="cart_data" id="cart_data">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                       class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all @error('customer_name') border-red-500 @enderror"
                                       placeholder="Jean Dupont">
                                @error('customer_name')
                                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                       class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all @error('customer_phone') border-red-500 @enderror"
                                       placeholder="0812345678">
                                @error('customer_phone')
                                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Email (optionnel)</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                       class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all"
                                       placeholder="jean@exemple.com">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Adresse de livraison <span class="text-red-500">*</span></label>
                                <input type="text" name="delivery_address" value="{{ old('delivery_address') }}" required
                                       class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all @error('delivery_address') border-red-500 @enderror"
                                       placeholder="Numéro et nom de rue">
                                @error('delivery_address')
                                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Ville</label>
                                <input type="text" name="delivery_city" value="{{ old('delivery_city') }}"
                                       class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all"
                                       placeholder="Kinshasa">
                            </div>
                            
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Quartier</label>
                                <input type="text" name="delivery_neighborhood" value="{{ old('delivery_neighborhood') }}"
                                       class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all"
                                       placeholder="Gombe, Lingwala...">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-1.5">Notes (optionnel)</label>
                                <textarea name="delivery_notes" rows="2" 
                                          class="w-full border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition-all"
                                          placeholder="Sonnette, étage, point de repère...">{{ old('delivery_notes') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Méthode de paiement -->
                <div class="border border-neutral-100 p-6 bg-neutral-50/40">
                    <h2 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-5 pb-2 border-b border-neutral-200 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-neutral-900 text-white text-[11px] flex items-center justify-center">2</span>
                        Méthode de paiement
                    </h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-start gap-4 p-4 border border-neutral-200 cursor-pointer hover:bg-white transition-all group has-[:checked]:border-gold has-[:checked]:bg-white/50">
                            <input type="radio" name="payment_method" form="checkoutForm" value="cash" checked class="mt-0.5 text-gold focus:ring-gold">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium text-neutral-900">Espèces</span>
                                </div>
                                <p class="text-xs text-neutral-500 mt-1">Paiement en espèces</p>
                            </div>
                        </label>
                        
                        <label class="flex items-start gap-4 p-4 border border-neutral-200 cursor-pointer hover:bg-white transition-all group has-[:checked]:border-gold has-[:checked]:bg-white/50">
                            <input type="radio" name="payment_method" form="checkoutForm" value="mobile_money" class="mt-0.5 text-gold focus:ring-gold">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <span class="font-medium text-neutral-900">Mobile Money</span>
                                </div>
                                <p class="text-xs text-neutral-500 mt-1">Paiement via Airtel Money ou Orange Money</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ================= RÉCAPITULATIF ================= -->
            <div class="lg:col-span-1">
                <div class="border border-neutral-100 bg-white sticky top-24">
                    <div class="p-5 border-b border-neutral-100 bg-neutral-50/50">
                        <h2 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Récapitulatif
                        </h2>
                    </div>
                    
                    <div id="cart-items-checkout" class="p-5 space-y-4 max-h-80 overflow-y-auto">
                        <div class="text-center py-4 text-neutral-400 text-sm">Chargement du panier...</div>
                    </div>
                    
                    <div class="p-5 border-t border-neutral-100 bg-neutral-50/30">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500">Sous-total</span>
                                <span class="text-neutral-900 font-medium" id="checkout-subtotal">0 FC</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500">Livraison</span>
                                <span class="text-neutral-900" id="checkout-delivery">Offerte</span>
                            </div>
                            <div class="border-t border-neutral-200 pt-2 mt-2">
                                <div class="flex justify-between font-semibold text-base">
                                    <span class="text-neutral-900">Total</span>
                                    <span class="text-gold text-lg" id="checkout-total">0 FC</span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" form="checkoutForm" class="w-full mt-6 bg-neutral-900 hover:bg-gold hover:text-black text-white py-3 transition-all duration-300 uppercase tracking-[0.2em] text-[11px] font-semibold">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirmer la commande
                        </button>
                        
                        <p class="text-[10px] text-neutral-400 text-center mt-4">
                            En confirmant, vous serez redirigé vers WhatsApp pour envoyer votre commande.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= ANNONCES BOTTOM ================= -->
    @if(isset($announcementsByPosition['bottom']) && $announcementsByPosition['bottom']->isNotEmpty())
        <div class="bg-neutral-50 py-8 text-center border-t border-neutral-100 mt-8">
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
    // Configuration devise
    const currencyCode = '{{ $currency->code ?? 'CDF' }}';
    const currencySymbol = '{{ $currency->symbol ?? 'FC' }}';
    const currencyRate = {{ $currency->rate ?? 2850 }};
    
    function formatPrice(price) {
        if (currencyCode === 'CDF') {
            return currencySymbol + ' ' + (price * currencyRate).toLocaleString('fr-CD');
        }
        return currencySymbol + ' ' + price.toFixed(2).replace('.', ',');
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function loadCartToCheckout() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const container = document.getElementById('cart-items-checkout');
        const cartDataInput = document.getElementById('cart_data');
        
        if (!container) return;
        
        // Remplir le champ caché avec les données du panier
        if (cartDataInput) {
            cartDataInput.value = JSON.stringify(cart);
        }
        
        if (cart.length === 0) {
            container.innerHTML = '<div class="text-center py-4 text-neutral-400 text-sm">Votre panier est vide</div>';
            document.getElementById('checkout-subtotal').innerHTML = formatPrice(0);
            document.getElementById('checkout-total').innerHTML = formatPrice(0);
            return;
        }
        
        let subtotal = 0;
        let html = '';
        
        for (const item of cart) {
            const quantity = Math.min(item.quantity || 1, 99);
            const itemTotal = item.price * quantity;
            subtotal += itemTotal;
            
            html += `
                <div class="flex gap-3 pb-3 border-b border-neutral-100 last:border-0">
                    <img src="${item.image || '/images/placeholder.jpg'}" class="w-14 h-14 object-cover bg-neutral-100 rounded" onerror="this.src='/images/placeholder.jpg'">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-neutral-900 line-clamp-1">${escapeHtml(item.name)}</p>
                        <p class="text-xs text-neutral-500 mt-1">${quantity} x ${formatPrice(item.price)}</p>
                    </div>
                </div>
            `;
        }
        
        container.innerHTML = html;
        
        let deliveryFee = 0;
        let deliveryHtml = 'Offerte';
        if (subtotal < 50 && subtotal > 0) {
            deliveryFee = 3;
            deliveryHtml = formatPrice(3);
        }
        const total = subtotal + deliveryFee;
        
        document.getElementById('checkout-subtotal').innerHTML = formatPrice(subtotal);
        document.getElementById('checkout-delivery').innerHTML = deliveryHtml;
        document.getElementById('checkout-total').innerHTML = formatPrice(total);
    }
    
    // Auto-formatage du téléphone
    const phoneInput = document.querySelector('input[name="customer_phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            e.target.value = value;
        });
    }
    
    // Animation au submit
    const form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.innerHTML = '<span class="loader"></span> Redirection...';
                btn.disabled = true;
            }
        });
    }
    
    // Charger le panier au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        loadCartToCheckout();
    });
</script>

@push('styles')
<style>
    .loader {
        width: 16px;
        height: 16px;
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
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection