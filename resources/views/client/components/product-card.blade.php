@props(['product', 'currency'])

<div class="group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col product-card">
    <div class="relative overflow-hidden aspect-square">
        <a href="{{ route('client.product', $product->slug) }}" class="block h-full">
            @if($product->featured_image)
                <img src="{{ $product->featured_image_url }}" alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
            @endif
        </a>
        
        @if($product->old_price && $product->old_price > $product->price)
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
            </span>
        @endif
        
        <!-- Bouton ajouter au panier visible sur mobile sans hover -->
        <div class="absolute bottom-2 left-2 right-2 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300">
            <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->featured_image_url }}', 1)"
                    class="w-full bg-gold text-black py-2 rounded-lg font-semibold text-sm hover:bg-gold-dark transition shadow-md">
                <i class="fas fa-shopping-cart mr-1"></i> Ajouter
            </button>
        </div>
    </div>
    
    <div class="p-3 flex-1 flex flex-col">
        <a href="{{ route('client.category', $product->category->slug) }}" class="text-xs text-gold uppercase tracking-wider hover:underline">
            {{ $product->category->name }}
        </a>
        <a href="{{ route('client.product', $product->slug) }}" class="block flex-1">
            <h3 class="font-semibold text-gray-800 mt-1 mb-2 hover:text-gold transition line-clamp-2 text-sm md:text-base">
                {{ $product->name }}
            </h3>
        </a>
        
        <div class="flex items-center justify-between mt-2 flex-wrap gap-2">
            <div>
                @if($product->old_price && $product->old_price > $product->price)
                    <span class="text-gray-400 line-through text-xs">
                        {{ $currency === 'CDF' ? number_format($product->old_price * 2850) . ' FC' : '$ ' . number_format($product->old_price, 2) }}
                    </span>
                @endif
                <span class="text-gold font-bold text-base md:text-lg block">
                    {{ $currency === 'CDF' ? number_format($product->price * 2850) . ' FC' : '$ ' . number_format($product->price, 2) }}
                </span>
            </div>
            
            @if($product->avg_rating > 0)
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($product->avg_rating))
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                        @else
                            <i class="far fa-star text-yellow-400 text-xs"></i>
                        @endif
                    @endfor
                    <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                </div>
            @endif
        </div>
        
        <!-- Bouton voir détails pour mobile -->
        <a href="{{ route('client.product', $product->slug) }}" 
           class="mt-3 text-center text-xs text-gray-500 hover:text-gold transition md:hidden">
            Voir détails <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>@props(['product', 'currency'])

<div class="group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col product-card">
    <div class="relative overflow-hidden aspect-square bg-gray-100">
        <a href="{{ route('client.product', $product->slug) }}" class="block h-full">
            @if($product->featured_image)
                <img src="{{ $product->featured_image_url }}" 
                     alt="{{ $product->name }}" 
                     loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
            @endif
        </a>
        
        <!-- Badge promotion -->
        @if($product->old_price && $product->old_price > $product->price)
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded font-semibold z-10">
                -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
            </span>
        @endif
        
        <!-- Badge nouveauté -->
        @if($product->created_at && $product->created_at->diffInDays(now()) <= 7)
            <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded font-semibold z-10">
                <i class="fas fa-star text-xs mr-1"></i>Nouveau
            </span>
        @endif
        
        <!-- Bouton ajouter au panier - toujours visible sur mobile, hover sur desktop -->
        <div class="absolute bottom-2 left-2 right-2 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 z-10">
            <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->featured_image_url }}', 1)"
                    class="w-full bg-gold text-black py-2 rounded-lg font-semibold text-sm hover:bg-gold-dark transition shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-shopping-cart text-sm"></i>
                <span class="hidden sm:inline">Ajouter</span>
                <span class="sm:hidden"><i class="fas fa-plus"></i></span>
            </button>
        </div>
    </div>
    
    <div class="p-3 flex-1 flex flex-col">
        <!-- Catégorie -->
        @if($product->category)
            <a href="{{ route('client.category', $product->category->slug) }}" 
               class="text-xs text-gold uppercase tracking-wider hover:underline mb-1">
                {{ $product->category->name }}
            </a>
        @endif
        
        <!-- Nom produit -->
        <a href="{{ route('client.product', $product->slug) }}" class="block flex-1">
            <h3 class="font-semibold text-gray-800 hover:text-gold transition line-clamp-2 text-sm md:text-base">
                {{ $product->name }}
            </h3>
        </a>
        
        <!-- Prix et étoiles -->
        <div class="mt-2">
            <!-- Prix -->
            <div class="flex items-baseline flex-wrap gap-2 mb-2">
                @if($product->old_price && $product->old_price > $product->price)
                    <span class="text-gray-400 line-through text-xs">
                        {{ $currency->code === 'CDF' ? number_format($product->old_price * ($currency->rate ?? 2850), 0) . ' ' . $currency->symbol : $currency->symbol . ' ' . number_format($product->old_price, 2) }}
                    </span>
                @endif
                <span class="text-gold font-bold text-base md:text-lg">
                    {{ $currency->code === 'CDF' ? number_format($product->price * ($currency->rate ?? 2850), 0) . ' ' . $currency->symbol : $currency->symbol . ' ' . number_format($product->price, 2) }}
                </span>
            </div>
            
            <!-- Étoiles -->
            @if($product->avg_rating > 0)
                <div class="flex items-center">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->avg_rating))
                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                            @else
                                <i class="far fa-star text-yellow-400 text-xs"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                </div>
            @else
                <div class="flex items-center">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="far fa-star text-gray-300 text-xs"></i>
                        @endfor
                    </div>
                    <span class="text-xs text-gray-400 ml-1">0 avis</span>
                </div>
            @endif
        </div>
        
        <!-- Stock info (optionnel) -->
        @if($product->track_stock && $product->stock_quantity <= $product->stock_alert_threshold && $product->stock_quantity > 0)
            <div class="mt-2">
                <span class="text-xs text-orange-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Plus que {{ $product->stock_quantity }} en stock
                </span>
            </div>
        @endif
        
        <!-- Lien voir détails pour mobile (optionnel) -->
        <a href="{{ route('client.product', $product->slug) }}" 
           class="mt-3 text-center text-xs text-gray-500 hover:text-gold transition md:hidden py-2 border-t border-gray-100">
            Voir détails <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

@push('styles')
<style>
    /* Optimisations pour mobile */
    @media (max-width: 640px) {
        .product-card {
            font-size: 12px;
            padding: 0.35rem;
        }

        .product-card .line-clamp-2 {
            -webkit-line-clamp: 2;
            font-size: 12px;
        }

        .product-card .p-3 { padding: 0.5rem !important; }
        .product-card .aspect-square { min-height: 0; }
    }
    
    /* Évitement du flash sur les images */
    .product-card img {
        background-color: #f3f4f6;
    }
</style>
@endpush