@extends('client.layouts.app')

@section('title', $category->name . ' - ' . $company->name)

@section('content')
<div class="bg-white text-neutral-900 min-h-screen font-sans antialiased">
    
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

    <div class="max-w-7xl mx-auto px-4 py-10 md:py-12">
        
        <!-- ================= FIL D'ARIANE & ENTÊTE DE CATÉGORIE ================= -->
        <div class="text-center mb-12">
            <nav class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-3">
                <a href="{{ route('client.catalog') }}" class="hover:text-black transition-colors">Collection</a>
                <span class="mx-2 text-gold">✦</span>
                <span class="text-neutral-900 font-medium">{{ $category->name }}</span>
            </nav>
            
            <h1 class="text-3xl md:text-5xl font-serif font-light uppercase tracking-[0.2em] text-neutral-900">
                {{ $category->name }}
            </h1>
            <div class="w-12 h-px bg-gold mx-auto mt-4 mb-5"></div>
            
            @if($category->description)
                <p class="text-neutral-500 text-sm md:text-base max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
            
            <!-- Compteur de produits -->
            <div class="mt-4 text-[11px] uppercase tracking-wider text-neutral-400">
                {{ $products->total() }} {{ Str::plural('pièce', $products->total()) }}
            </div>
        </div>

        <!-- ================= SOUS-CATÉGORIES ================= -->
        @if(isset($subcategories) && $subcategories->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-2 mb-10">
                <a href="{{ route('client.category', $category->slug) }}" 
                   class="bg-neutral-900 text-white border border-neutral-900 px-4 py-1.5 text-[11px] uppercase tracking-wider transition-all duration-300 rounded-full">
                    Toutes
                </a>
                @foreach($subcategories as $sub)
                    <a href="{{ route('client.category', $sub->slug) }}" 
                       class="bg-neutral-50 hover:bg-neutral-900 border border-neutral-200 text-neutral-700 hover:text-white px-4 py-1.5 text-[11px] uppercase tracking-wider transition-all duration-300 rounded-full">
                        {{ $sub->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- ================= ZONE PRINCIPALE : FILTRES + PRODUITS ================= -->
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- BARRE LATÉRALE DE FILTRES -->
            <aside class="w-full lg:w-64 shrink-0">
                <div class="border border-neutral-100 p-5 bg-neutral-50/40 sticky top-24">
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-5 pb-2 border-b border-neutral-200 flex items-center justify-between">
                        <span>Filtrer</span>
                        <span class="w-1.5 h-1.5 bg-gold rounded-full"></span>
                    </h3>
                    
                    <form action="{{ url()->current() }}" method="GET" id="filter-form" class="space-y-5">
                        @if(request()->filled('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <!-- Filtre Prix -->
                        <div>
                            <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-2 font-medium">Gamme de prix</label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-neutral-400">{{ $currency->symbol }}</span>
                                    <input type="number" name="min_price" 
                                           value="{{ request('min_price', $priceRange['min']) }}" 
                                           min="{{ $priceRange['min'] }}" 
                                           max="{{ $priceRange['max'] }}"
                                           step="100"
                                           class="w-full text-xs pl-7 pr-2 py-2 border border-neutral-200 bg-white focus:outline-none focus:border-neutral-900 transition-all rounded-md" 
                                           placeholder="Min">
                                </div>
                                <span class="text-neutral-400 text-xs">—</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-neutral-400">{{ $currency->symbol }}</span>
                                    <input type="number" name="max_price" 
                                           value="{{ request('max_price', $priceRange['max']) }}" 
                                           min="{{ $priceRange['min'] }}" 
                                           max="{{ $priceRange['max'] }}"
                                           step="100"
                                           class="w-full text-xs pl-7 pr-2 py-2 border border-neutral-200 bg-white focus:outline-none focus:border-neutral-900 transition-all rounded-md" 
                                           placeholder="Max">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-neutral-900 hover:bg-gold hover:text-black text-white text-[10px] uppercase tracking-[0.2em] font-medium py-2.5 transition-all duration-300 rounded-md">
                            Appliquer
                        </button>

                        @if(request()->filled('min_price') || request()->filled('max_price') || request()->filled('rating'))
                            <a href="{{ route('client.category', $category->slug) }}" 
                               class="block text-center text-[10px] uppercase tracking-wider text-neutral-400 hover:text-black transition-colors pt-1">
                                Réinitialiser
                            </a>
                        @endif
                    </form>
                </div>
            </aside>

            <!-- CONTENU DE LA GRILLE DE PRODUITS -->
            <div class="flex-1">
                
                <!-- BARRE DE TRI -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-3 border-b border-neutral-100">
                    <div class="text-xs text-neutral-500 font-light tracking-wide">
                        <span class="text-black font-medium">{{ $products->firstItem() ?? 0 }}</span> à 
                        <span class="text-black font-medium">{{ $products->lastItem() ?? 0 }}</span> sur 
                        <span class="text-black font-medium">{{ $products->total() }}</span> produits
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <label for="sort-select" class="text-[10px] uppercase tracking-wider text-neutral-400">Trier :</label>
                        <select id="sort-select" class="text-xs p-2 border border-neutral-200 bg-white focus:outline-none focus:border-black font-light text-neutral-800 cursor-pointer rounded-md" 
                                onchange="window.location.href = this.value">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Nouveautés</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom A → Z</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nom Z → A</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating_desc']) }}" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Mieux notés</option>
                        </select>
                    </div>
                </div>

                <!-- ================= ANNONCES MIDDLE (après le tri) ================= -->
                @if(isset($announcementsByPosition['middle']) && $announcementsByPosition['middle']->isNotEmpty())
                    <div class="mb-8">
                        @foreach($announcementsByPosition['middle'] as $announcement)
                            <div class="bg-gradient-to-r from-gold/10 to-gold/5 border-l-4 border-gold p-4 rounded-lg mb-3">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        @if($announcement->badge)
                                            <span class="inline-block bg-gold/20 text-gold-dark text-[10px] px-2 py-0.5 rounded mb-1">{{ $announcement->badge }}</span>
                                        @endif
                                        <p class="text-sm text-neutral-700">{{ $announcement->content ?? $announcement->message }}</p>
                                    </div>
                                    @if($announcement->button_text && $announcement->button_link)
                                        <a href="{{ $announcement->button_link }}" class="text-gold text-[10px] uppercase tracking-wider hover:underline">
                                            {{ $announcement->button_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- GRILLE DE PRODUITS -->
                @if($products->isEmpty())
                    <div class="text-center py-20 border border-neutral-100 bg-neutral-50/40">
                        <div class="text-4xl text-neutral-300 block mb-3">⚜️</div>
                        <p class="text-xs text-neutral-400 uppercase tracking-wider font-light">Aucune pièce ne correspond à vos critères.</p>
                        <a href="{{ route('client.category', $category->slug) }}" class="inline-block mt-5 text-[10px] uppercase tracking-wider text-gold hover:text-black transition-colors">
                            Voir toute la collection
                        </a>
                    </div>
                @else
                    <div id="product-grid-container" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 md:gap-4">
                        @foreach($products as $product)
                            <div class="group relative bg-white border border-neutral-100 hover:border-neutral-900 hover:shadow-lg transition-all duration-300 p-3 flex flex-col product-item rounded-lg">
                                <!-- Image -->
                                <div class="relative w-full aspect-square bg-neutral-50 overflow-hidden mb-3 rounded-lg">
                                    <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" 
                                         alt="{{ $product->name }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                    
                                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                                        @if($product->is_featured)
                                            <span class="bg-black text-gold text-[8px] tracking-wider uppercase px-2 py-0.5 font-bold rounded-sm">
                                                Iconique
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Infos produit -->
                                <div class="flex-1">
                                    <p class="text-[9px] text-neutral-400 uppercase tracking-wider mb-1">
                                        {{ $category->name }}
                                    </p>
                                    <h3 class="text-xs font-normal text-neutral-900 line-clamp-2 group-hover:text-gold transition-colors mb-2">
                                        <a href="{{ route('client.product', $product->slug) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>

                                <!-- Prix et note -->
                                <div class="mt-auto pt-2 border-t border-neutral-50 flex items-center justify-between">
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
                                            <span class="text-[9px] text-neutral-400">({{ $product->reviews_count }})</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Bouton rapide ajouter -->
                                <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image_url }}', 1, false)"
                                        class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 bg-black text-white p-1.5 rounded-full hover:bg-gold hover:text-black">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- PAGINATION -->
                    @if($products->hasPages())
                        <div class="mt-10 pt-4 border-t border-neutral-100">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif

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

@push('scripts')
<script>
    const minInput = document.querySelector('input[name="min_price"]');
    const maxInput = document.querySelector('input[name="max_price"]');
    
    // Gestion de l'affichage grille/liste
    function setView(view) {
        const container = document.getElementById('product-grid-container');
        if (view === 'list') {
            container.classList.remove('grid-cols-2', 'sm:grid-cols-2', 'md:grid-cols-3', 'lg:grid-cols-3');
            container.classList.add('flex', 'flex-col', 'gap-3');
            document.querySelectorAll('.product-item').forEach(item => {
                item.classList.add('flex-row', 'gap-4');
                const imgDiv = item.querySelector('.aspect-square');
                if (imgDiv) {
                    imgDiv.classList.add('w-24', 'h-24', 'mb-0');
                }
            });
        } else {
            container.classList.remove('flex', 'flex-col', 'gap-3');
            container.classList.add('grid', 'grid-cols-2', 'sm:grid-cols-2', 'md:grid-cols-3', 'lg:grid-cols-3', 'gap-3', 'md:gap-4');
            document.querySelectorAll('.product-item').forEach(item => {
                item.classList.remove('flex-row', 'gap-4');
                const imgDiv = item.querySelector('.aspect-square');
                if (imgDiv) {
                    imgDiv.classList.remove('w-24', 'h-24', 'mb-0');
                }
            });
        }
    }
</script>
@endpush

@push('styles')
<style>
    /* Pagination élégante */
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
        border-radius: 0.5rem;
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
    
    /* Line clamp */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Animation hover */
    .product-item {
        transition: all 0.3s ease;
    }
</style>
@endpush
@endsection