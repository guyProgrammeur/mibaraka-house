@extends('client.layouts.app')

@section('title', $product->name . ' - ' . $company->name)

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
            <a href="{{ route('client.catalog') }}" class="hover:text-black transition-colors">Collection</a>
            <span class="mx-2 text-gold">✦</span>
            @if($product->category)
                <a href="{{ route('client.category', $product->category->slug) }}" class="hover:text-black transition-colors">
                    {{ $product->category->name }}
                </a>
                <span class="mx-2 text-gold">✦</span>
            @endif
            <span class="text-neutral-900">{{ $product->name }}</span>
        </nav>

        <!-- ================= PRODUIT PRINCIPAL ================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 mb-16">
            
            <!-- COLONNE GAUCHE : GALERIE IMAGES -->
            <div class="space-y-3">
                <div class="relative w-full aspect-square bg-neutral-50 border border-neutral-100 overflow-hidden group">
                    <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" 
                         alt="{{ $product->name }}" 
                         id="main-product-image"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    
                    <div class="absolute top-3 left-3 flex flex-col gap-2">
                        @if($product->is_featured)
                            <span class="bg-black text-gold text-[9px] tracking-wider uppercase px-2 py-0.5 font-bold rounded-sm">
                                Exclusivité
                            </span>
                        @endif
                    </div>
                </div>
                
                @if($product->gallery_images && count($product->gallery_images) > 0)
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        <div class="w-16 h-16 border-2 border-gold cursor-pointer overflow-hidden" onclick="changeImage('{{ $product->image_url }}')">
                            <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                        </div>
                        @foreach($product->gallery_images as $galleryImage)
                            <div class="w-16 h-16 border border-neutral-200 cursor-pointer hover:border-gold transition-colors overflow-hidden" onclick="changeImage('{{ asset('storage/' . $galleryImage) }}')">
                                <img src="{{ asset('storage/' . $galleryImage) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- COLONNE DROITE : INFORMATIONS PRODUIT -->
            <div class="flex flex-col">
                <div class="mb-6">
                    <span class="text-[11px] uppercase tracking-[0.2em] text-gold font-medium block mb-2">
                        {{ $product->category->name ?? 'Collection Prestige' }}
                    </span>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-serif font-light tracking-wide text-neutral-900 mb-3">
                        {{ $product->name }}
                    </h1>
                    
                    @if($product->sku)
                        <span class="text-[10px] text-neutral-400 block mb-4 uppercase tracking-wider">Réf: {{ $product->sku }}</span>
                    @endif

                    @if($reviewsStats['total'] > 0)
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center gap-1">
                                <div class="flex text-sm text-gold">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($reviewsStats['average']))
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm font-medium text-neutral-900">{{ number_format($reviewsStats['average'], 1) }}</span>
                            </div>
                            <span class="text-neutral-400 text-xs">({{ $reviewsStats['total'] }} avis clients)</span>
                            <a href="#reviews" class="text-[10px] uppercase tracking-wider text-neutral-400 hover:text-gold transition-colors">
                                Lire les avis
                            </a>
                        </div>
                    @endif

                    <div class="mb-6">
                        @if($product->old_price && $product->old_price > $product->price)
                            <span class="text-lg text-neutral-400 line-through mr-3">
                                @if($currency->code === 'CDF')
                                    {{ $currency->symbol }} {{ number_format($product->old_price * ($currency->rate ?? 2850), 0, ',', ' ') }}
                                @else
                                    {{ $currency->symbol }} {{ number_format($product->old_price, 2, ',', ' ') }}
                                @endif
                            </span>
                        @endif
                        <span class="text-3xl md:text-4xl font-serif font-semibold text-neutral-900">
                            @if($currency->code === 'CDF')
                                {{ $currency->symbol }} {{ number_format($product->price * ($currency->rate ?? 2850), 0, ',', ' ') }}
                            @else
                                {{ $currency->symbol }} {{ number_format($product->price, 2, ',', ' ') }}
                            @endif
                        </span>
                    </div>

                    @if($product->short_description)
                        <div class="text-neutral-600 text-sm leading-relaxed font-light mb-6 border-t border-neutral-100 pt-4">
                            <p>{{ $product->short_description }}</p>
                        </div>
                    @endif
                    
                    @if($product->description)
                        <div class="mb-6 border-t border-neutral-100 pt-4">
                            <button onclick="toggleDescription()" class="flex items-center gap-2 text-[10px] uppercase tracking-wider text-neutral-500 hover:text-gold transition-colors">
                                <span id="desc-toggle-text">Lire la description complète</span>
                                <svg id="desc-toggle-icon" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="full-description" class="hidden mt-3 text-neutral-600 text-sm leading-relaxed font-light">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Stock & Quantité -->
                <div class="border-t border-neutral-100 pt-6 mb-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-neutral-500">Quantité :</span>
                            <div class="flex items-center border border-neutral-200">
                                <button onclick="decrementQuantity()" class="px-3 py-1.5 hover:bg-neutral-100 transition-colors">−</button>
                                <input type="number" id="quantity" value="1" min="1" max="{{ $product->track_stock ? $product->stock_quantity : 99 }}" 
                                       class="w-12 text-center text-sm border-0 focus:outline-none">
                                <button onclick="incrementQuantity()" class="px-3 py-1.5 hover:bg-neutral-100 transition-colors">+</button>
                            </div>
                        </div>
                        
                        @if($product->track_stock)
                            <div class="flex items-center gap-2">
                                @if($product->stock_quantity > 0)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <span class="text-[11px] text-neutral-500">En stock ({{ $product->stock_quantity }})</span>
                                @else
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    <span class="text-[11px] text-red-500">Rupture de stock</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image_url }}', document.getElementById('quantity').value, true)" 
        class="w-full md:w-auto bg-neutral-900 hover:bg-gold hover:text-black text-white font-medium text-xs uppercase tracking-[0.2em] px-8 py-3.5 transition-all duration-300 flex items-center justify-center gap-2"
        {{ ($product->track_stock && $product->stock_quantity <= 0) ? 'disabled' : '' }}>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
    </svg>
    Ajouter au panier
</button>
                </div>

                <!-- Livraison & Services -->
                <div class="border-t border-neutral-100 pt-6 grid grid-cols-2 gap-4 text-[10px] uppercase tracking-wider">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Livraison 24-48h</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Paiement sécurisé</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span>Retour 14 jours</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Authentifié</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SECTION AVIS CLIENTS ================= -->
        <div id="reviews" class="border-t border-neutral-200 pt-12 mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div>
                    <h3 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-6">Avis clients</h3>
                    <div class="bg-neutral-50 p-6 border border-neutral-100 text-center">
                        <div class="text-4xl font-serif font-bold text-neutral-900">{{ number_format($reviewsStats['average'], 1) }}</div>
                        <div class="flex justify-center text-gold text-sm my-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($reviewsStats['average']))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <p class="text-[11px] text-neutral-400 uppercase tracking-wider">
                            Basé sur {{ $reviewsStats['total'] }} avis
                        </p>
                    </div>
                    
                    @if($reviewsStats['total'] > 0)
                        <div class="mt-6 space-y-2">
                            @foreach($reviewsStats['distribution'] as $rating => $data)
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="w-8 text-neutral-600">{{ $rating }} ★</span>
                                    <div class="flex-1 h-1.5 bg-neutral-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gold rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                    </div>
                                    <span class="w-10 text-neutral-400">{{ $data['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <button onclick="document.getElementById('review-form').scrollIntoView({behavior: 'smooth'})" 
                            class="w-full mt-6 border border-neutral-900 hover:bg-neutral-900 hover:text-white text-neutral-900 text-[10px] uppercase tracking-[0.2em] py-2.5 transition-all duration-300">
                        Donner mon avis
                    </button>
                </div>

                <div class="lg:col-span-2">
                    <h3 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-6">Témoignages</h3>
                    
                    @forelse($reviews as $review)
                        <div class="border-b border-neutral-100 pb-4 mb-4 last:border-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <span class="font-medium text-sm text-neutral-900">{{ $review->customer_name }}</span>
                                <div class="flex text-gold text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating) ★ @else ☆ @endif
                                    @endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-neutral-600 text-sm font-light leading-relaxed">{{ $review->comment }}</p>
                            @endif
                            <p class="text-[10px] text-neutral-400 mt-2">{{ $review->created_at->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8 bg-neutral-50">
                            <p class="text-xs text-neutral-400 font-light italic">Aucun avis pour le moment.</p>
                            <p class="text-[10px] text-neutral-400 mt-2">Soyez le premier à donner votre avis</p>
                        </div>
                    @endforelse
                    
                    @if($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                    
                    <div id="review-form" class="mt-8 pt-6 border-t border-neutral-200">
                        <h4 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-4">Partagez votre expérience</h4>
                        <form action="{{ route('client.review.store', $product->slug) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-[11px] text-neutral-600 mb-2">Votre note</label>
                                <div class="flex gap-2 text-xl text-gold cursor-pointer" id="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span data-rating="{{ $i }}" class="hover:scale-110 transition-transform">☆</span>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-value" required>
                            </div>
                            
                            @guest
                            <div class="mb-4">
                                <label class="block text-[11px] text-neutral-600 mb-2">Votre nom</label>
                                <input type="text" name="customer_name" required 
                                       class="w-full border border-neutral-200 p-2 text-sm focus:outline-none focus:border-gold transition-colors">
                            </div>
                            @endguest
                            
                            <div class="mb-4">
                                <label class="block text-[11px] text-neutral-600 mb-2">Votre avis</label>
                                <textarea name="comment" rows="4" 
                                          class="w-full border border-neutral-200 p-2 text-sm focus:outline-none focus:border-gold transition-colors"
                                          placeholder="Partagez votre expérience..."></textarea>
                            </div>
                            
                            <button type="submit" class="border border-neutral-900 hover:bg-neutral-900 hover:text-white text-neutral-900 text-[10px] uppercase tracking-[0.2em] px-6 py-2.5 transition-all duration-300">
                                Publier mon avis
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PRODUITS SIMILAIRES ================= -->
        @if($relatedProducts->isNotEmpty())
            <section class="border-t border-neutral-200 pt-12 mt-12">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 border-l-2 border-gold pl-3">
                        Dans la même collection
                    </h3>
                    <a href="{{ route('client.category', $product->category->slug) }}" 
                       class="text-[10px] uppercase tracking-wider text-neutral-400 hover:text-gold transition-colors">
                        Voir toute la collection
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach($relatedProducts as $related)
                        @include('client.components.product-card', ['product' => $related, 'currency' => $currency])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- ================= PRODUITS RÉCEMMENT CONSULTÉS ================= -->
        @if($recentProducts->isNotEmpty())
            <section class="border-t border-neutral-200 pt-12 mt-8">
                <h3 class="text-sm uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-6 border-l-2 border-gold pl-3">
                    Récemment consultés
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-4">
                    @foreach($recentProducts as $recent)
                        @include('client.components.product-card', ['product' => $recent, 'currency' => $currency])
                    @endforeach
                </div>
            </section>
        @endif
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

@push('scripts')
<script>
    function decrementQuantity() {
        const input = document.getElementById('quantity');
        if (input.value > 1) input.value = parseInt(input.value) - 1;
    }
    
    function incrementQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max) || 99;
        if (input.value < max) input.value = parseInt(input.value) + 1;
    }
    
    function toggleDescription() {
        const desc = document.getElementById('full-description');
        const text = document.getElementById('desc-toggle-text');
        const icon = document.getElementById('desc-toggle-icon');
        
        if (desc.classList.contains('hidden')) {
            desc.classList.remove('hidden');
            text.innerText = 'Réduire la description';
            icon.style.transform = 'rotate(180deg)';
        } else {
            desc.classList.add('hidden');
            text.innerText = 'Lire la description complète';
            icon.style.transform = 'rotate(0deg)';
        }
    }
    
    const stars = document.querySelectorAll('#rating-stars span');
    const ratingInput = document.getElementById('rating-value');
    
    if (stars.length > 0 && ratingInput) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.innerHTML = '★';
                    } else {
                        s.innerHTML = '☆';
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.opacity = '0.7';
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                stars.forEach(s => s.style.opacity = '1');
            });
        });
    }
    
    function changeImage(src) {
        const mainImage = document.getElementById('main-product-image');
        if (mainImage) {
            mainImage.src = src;
        }
    }
</script>
@endpush

@push('styles')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.2rem;
        height: 2.2rem;
        padding: 0 0.5rem;
        border: 1px solid #e5e5e5;
        color: #4b5563;
        font-size: 0.75rem;
        transition: all 0.2s ease;
        background: white;
    }
    .pagination .page-item.active .page-link {
        background: #D4AF37;
        border-color: #D4AF37;
        color: #111111;
    }
    .pagination .page-item .page-link:hover {
        background: #f3f4f6;
        border-color: #D4AF37;
    }
    
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
@endpush
@endsection