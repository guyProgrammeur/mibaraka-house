@extends('client.layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="bg-white text-neutral-900 min-h-screen font-sans antialiased">
    
    <!-- ================= ANNONCES TOP ================= -->
    @if(isset($announcementsByPosition['top']) && $announcementsByPosition['top']->isNotEmpty())
        <div class="space-y-3 max-w-7xl mx-auto px-4 pt-4">
            @foreach($announcementsByPosition['top'] as $announcement)
                @include('client.components.announcement', ['announcement' => $announcement, 'position' => 'top'])
            @endforeach
        </div>
    @endif

    <!-- ================= HERO EN-TÊTE PRESTIGE ================= -->
    <header class="py-16 md:py-20 border-b border-neutral-100 bg-neutral-50/30">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-serif font-light uppercase tracking-[0.2em] text-neutral-900 mb-4">
                {{ $company->name ?? 'Mibaraka House' }}
            </h1>
            <div class="w-12 h-px bg-gold mx-auto mb-5"></div>
            <p class="text-neutral-500 max-w-2xl mx-auto text-sm md:text-base font-light leading-relaxed">
                {{ $company->slogan ?? 'Une collection intemporelle alliant épuration contemporaine et finitions d\'exception.' }}
            </p>
            
            <!-- Statistiques -->
            <div class="mt-8 flex flex-wrap justify-center items-center gap-4 md:gap-6 text-[11px] uppercase tracking-wider text-neutral-400">
                <span class="flex items-center gap-2">
                    <svg class="w-3 h-3 text-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $productCount }} Produits exclusifs
                </span>
                <span class="text-gold">✦</span>
                <span class="flex items-center gap-2">
                    <svg class="w-3 h-3 text-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Note {{ number_format($averageRating, 1) }}/5
                </span>
            </div>
        </div>
    </header>

    <!-- ================= NAVIGATION CATÉGORIES ================= -->
    @if(isset($categories) && $categories->isNotEmpty())
        <nav class="sticky top-16 bg-white/80 backdrop-blur-sm z-30 border-b border-neutral-100">
            <div class="max-w-7xl mx-auto px-4 py-4 overflow-x-auto hide-scrollbar">
                <div class="flex flex-nowrap justify-start md:justify-center gap-2 md:gap-3">
                    <a href="{{ route('client.catalog') }}" 
                       class="flex-shrink-0 border border-neutral-200 hover:border-black hover:bg-black hover:text-white px-4 py-2 text-[11px] uppercase tracking-wider font-medium transition-all duration-300 rounded-full">
                        Tous
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('client.category', $cat->slug) }}" 
                           class="flex-shrink-0 border border-neutral-200 hover:border-black hover:bg-black hover:text-white px-4 py-2 text-[11px] uppercase tracking-wider font-medium transition-all duration-300 rounded-full">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>
    @endif

    <!-- ================= SÉLECTION PHARE ================= -->
    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 py-12 md:py-16">
            <div class="border-b border-neutral-200 mb-8 pb-4 flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 border-l-2 border-gold pl-4">
                    La Sélection Phare
                </h2>
                <a href="{{ route('client.catalog') }}?featured=1" 
                   class="text-[10px] uppercase tracking-wider text-neutral-400 hover:text-gold transition-colors flex items-center gap-1">
                    Voir toute la collection
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-4">
                @foreach($featuredProducts as $product)
                    <div class="group relative bg-white border border-neutral-100 hover:border-neutral-900 hover:shadow-lg transition-all duration-300 p-3 flex flex-col rounded-lg">
                        <div class="relative w-full aspect-square bg-neutral-50 overflow-hidden mb-3 rounded-lg">
                            <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy"
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                            
                            @if($product->is_featured)
                            <div class="absolute top-2 left-2">
                                <span class="bg-black text-gold text-[9px] tracking-wider uppercase px-2 py-0.5 font-semibold rounded-sm">
                                    Star
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <p class="text-[10px] text-neutral-400 uppercase tracking-wider mb-1">
                                {{ $product->category->name ?? 'Collection' }}
                            </p>
                            <h3 class="text-xs font-normal text-neutral-900 line-clamp-2 group-hover:text-gold transition-colors mb-2">
                                <a href="{{ route('client.product', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                        </div>

                        <div class="mt-3 pt-2 border-t border-neutral-50 flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-900 font-serif">
                                @if($currency->code === 'CDF')
                                    {{ $currency->symbol }} {{ number_format($product->price * ($currency->rate ?? 2850), 0, ',', ' ') }}
                                @else
                                    {{ $currency->symbol }} {{ number_format($product->price, 2, ',', ' ') }}
                                @endif
                            </span>
                            @if($product->avg_rating > 0)
                                <div class="flex items-center gap-1">
                                    <div class="flex text-[10px] text-gold">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($product->avg_rating)) ★ @else ☆ @endif
                                        @endfor
                                    </div>
                                    <span class="text-[10px] text-neutral-400">({{ $product->reviews_count }})</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ================= ANNONCES MIDDLE ================= -->
    @if(isset($announcementsByPosition['middle']) && $announcementsByPosition['middle']->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 my-8">
            @foreach($announcementsByPosition['middle'] as $announcement)
                @include('client.components.announcement', ['announcement' => $announcement, 'position' => 'middle'])
            @endforeach
        </div>
    @endif

    <!-- ================= SECTION NOUVEAUTÉS & BEST-SELLERS ================= -->
    <section class="max-w-7xl mx-auto px-4 py-12 md:py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12">
        
        <!-- Colonne Nouveautés -->
        @if(isset($newProducts) && $newProducts->isNotEmpty())
            <div>
                <div class="border-b border-neutral-200 mb-6 pb-3">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 border-l-2 border-black pl-4">
                        Nouveautés
                    </h2>
                    <p class="text-[10px] text-neutral-400 mt-1 pl-4">Les dernières arrivées</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">
                    @foreach($newProducts as $product)
                        <div class="group bg-white border border-neutral-100 hover:border-neutral-900 hover:shadow-md transition-all duration-300 p-2.5 flex flex-col rounded-lg">
                            <div class="relative w-full aspect-square bg-neutral-50 overflow-hidden mb-2 rounded-lg">
                                <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" 
                                     alt="{{ $product->name }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-1 right-1">
                                    <span class="bg-gold/90 text-black text-[8px] tracking-wider uppercase px-1.5 py-0.5 font-bold rounded-sm">
                                        New
                                    </span>
                                </div>
                            </div>
                            <h3 class="text-xs font-normal text-neutral-900 line-clamp-2 group-hover:text-gold transition-colors mb-1">
                                <a href="{{ route('client.product', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="mt-2 pt-1.5 flex items-center justify-between border-t border-neutral-50">
                                <span class="text-sm font-medium text-neutral-900">
                                    @if($currency->code === 'CDF')
                                        {{ $currency->symbol }} {{ number_format($product->price * ($currency->rate ?? 2850), 0, ',', ' ') }}
                                    @else
                                        {{ $currency->symbol }} {{ number_format($product->price, 2, ',', ' ') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('client.catalog') }}?sort=newest" 
                       class="text-[10px] uppercase tracking-wider text-neutral-400 hover:text-gold transition-colors inline-flex items-center gap-1">
                        Découvrir toutes les nouveautés
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Colonne Meilleures Ventes -->
        @if(isset($bestSellers) && $bestSellers->isNotEmpty())
            <div>
                <div class="border-b border-neutral-200 mb-6 pb-3">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 border-l-2 border-gold pl-4">
                        Best-Sellers
                    </h2>
                    <p class="text-[10px] text-neutral-400 mt-1 pl-4">Les favoris de nos clients</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">
                    @foreach($bestSellers as $product)
                        <div class="group bg-white border border-neutral-100 hover:border-neutral-900 hover:shadow-md transition-all duration-300 p-2.5 flex flex-col rounded-lg">
                            <div class="relative w-full aspect-square bg-neutral-50 overflow-hidden mb-2 rounded-lg">
                                <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" 
                                     alt="{{ $product->name }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-1 left-1">
                                    <span class="bg-gold text-black text-[8px] tracking-wider uppercase px-1.5 py-0.5 font-bold rounded-sm">
                                        Best
                                    </span>
                                </div>
                            </div>
                            <h3 class="text-xs font-normal text-neutral-900 line-clamp-2 group-hover:text-gold transition-colors mb-1">
                                <a href="{{ route('client.product', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="mt-2 pt-1.5 flex items-center justify-between border-t border-neutral-50">
                                <span class="text-sm font-medium text-neutral-900">
                                    @if($currency->code === 'CDF')
                                        {{ $currency->symbol }} {{ number_format($product->price * ($currency->rate ?? 2850), 0, ',', ' ') }}
                                    @else
                                        {{ $currency->symbol }} {{ number_format($product->price, 2, ',', ' ') }}
                                    @endif
                                </span>
                                @if($product->avg_rating > 0)
                                    <div class="flex items-center gap-0.5">
                                        <span class="text-[10px] text-gold">★</span>
                                        <span class="text-[10px] text-neutral-500">{{ number_format($product->avg_rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('client.catalog') }}?sort=popularity" 
                       class="text-[10px] uppercase tracking-wider text-neutral-400 hover:text-gold transition-colors inline-flex items-center gap-1">
                        Voir tous les best-sellers
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- ================= LIVRAISON & SERVICES ================= -->
    <div class="bg-neutral-50 py-10 border-t border-neutral-100">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <svg class="w-6 h-6 mx-auto text-gold mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="text-[11px] uppercase tracking-wider font-medium">Livraison Premium</p>
                <p class="text-[10px] text-neutral-400 mt-1">Sous 24-48h à Kinshasa</p>
            </div>
            <div>
                <svg class="w-6 h-6 mx-auto text-gold mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p class="text-[11px] uppercase tracking-wider font-medium">Paiement Sécurisé</p>
                <p class="text-[10px] text-neutral-400 mt-1">Visa, Mastercard, Mobile Money</p>
            </div>
            <div>
                <svg class="w-6 h-6 mx-auto text-gold mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <p class="text-[11px] uppercase tracking-wider font-medium">Retour Facile</p>
                <p class="text-[10px] text-neutral-400 mt-1">14 jours pour changer d'avis</p>
            </div>
            <div>
                <svg class="w-6 h-6 mx-auto text-gold mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636L9.172 14.828a4 4 0 01-5.657 0L3.5 13.5m15.864-7.864a4 4 0 010 5.657l-9.192 9.192A4 4 0 013.5 20.5l-2-2"></path>
                </svg>
                <p class="text-[11px] uppercase tracking-wider font-medium">Savoir-faire Local</p>
                <p class="text-[10px] text-neutral-400 mt-1">100% produits authentiques</p>
            </div>
        </div>
    </div>

    <!-- ================= ANNONCES BOTTOM ================= -->
    @if(isset($announcementsByPosition['bottom']) && $announcementsByPosition['bottom']->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 my-8">
            @foreach($announcementsByPosition['bottom'] as $announcement)
                @include('client.components.announcement', ['announcement' => $announcement, 'position' => 'bottom'])
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    /* Masquer la scrollbar pour les catégories */
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    /* Border radius arrondi */
    .rounded-lg {
        border-radius: 0.75rem;
    }
    
    /* Transition smooth */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>
@endpush