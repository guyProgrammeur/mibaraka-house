@extends('client.layouts.app')

@section('title', 'Résultat de recherche - ' . e($query))

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
            <span class="text-neutral-900 font-medium">Recherche</span>
        </nav>
        
        <!-- ================= EN-TÊTE RECHERCHE ================= -->
        <div class="border-b border-neutral-100 pb-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div>
                    <div class="inline-block text-[10px] uppercase tracking-[0.2em] text-gold mb-3">
                        Résultats de recherche
                    </div>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif font-light tracking-wide text-neutral-900">
                        "{{ e($query) }}"
                    </h1>
                    <p class="text-neutral-500 text-sm mt-3">
                        {{ $products->total() }} produit(s) trouvé(s)
                    </p>
                </div>
                
                <!-- Formulaire de recherche rapide -->
                <form action="{{ route('client.search') }}" method="GET" class="flex w-full md:w-96">
                    <input type="text" name="q" value="{{ e($query) }}" 
                           placeholder="Nouvelle recherche..."
                           maxlength="100"
                           class="flex-1 border border-neutral-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-gold transition">
                    <button type="submit" class="bg-neutral-900 hover:bg-gold hover:text-black text-white px-6 py-2.5 transition-colors duration-300 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="text-[11px] uppercase tracking-wider">Rechercher</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- ================= SIDEBAR FILTRES ================= -->
            <aside class="w-full lg:w-64 shrink-0">
                <div class="border border-neutral-100 p-5 bg-neutral-50/40 sticky top-24">
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-5 pb-2 border-b border-neutral-200 flex items-center justify-between">
                        <span>Filtrer</span>
                        <span class="w-1.5 h-1.5 bg-gold rounded-full"></span>
                    </h3>
                    
                    <div class="space-y-5">
                        <!-- Tri -->
                        <div>
                            <label class="block text-[11px] uppercase tracking-wider text-neutral-500 mb-2 font-medium">Trier par</label>
                            <select id="sort_select" onchange="applySort()" 
                                    class="w-full text-xs p-2.5 border border-neutral-200 bg-white focus:outline-none focus:border-black transition cursor-pointer">
                                <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Nouveautés</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom A → Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nom Z → A</option>
                                <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Mieux notés</option>
                            </select>
                        </div>
                        
                        <!-- Réinitialiser -->
                        <div class="pt-4 border-t border-neutral-100">
                            <a href="{{ route('client.catalog') }}" class="flex items-center gap-2 text-[10px] uppercase tracking-wider text-neutral-400 hover:text-gold transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Réinitialiser les filtres
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ================= RÉSULTATS ================= -->
            <div class="flex-1">
                
                <!-- ================= ANNONCES MIDDLE ================= -->
                @if(isset($announcementsByPosition['middle']) && $announcementsByPosition['middle']->isNotEmpty())
                    <div class="mb-6">
                        @foreach($announcementsByPosition['middle'] as $announcement)
                            @include('client.components.announcement', ['announcement' => $announcement, 'position' => 'middle'])
                        @endforeach
                    </div>
                @endif
                
                @if($products->count() > 0)
                    <!-- Compteur résultats -->
                    <div class="text-xs text-neutral-500 mb-4 pb-2 border-b border-neutral-100">
                        Affichage de <span class="text-black font-medium">{{ $products->firstItem() ?? 0 }}</span> à 
                        <span class="text-black font-medium">{{ $products->lastItem() ?? 0 }}</span> sur 
                        <span class="text-black font-medium">{{ $products->total() }}</span> résultats
                    </div>
                    
                    <!-- Grille produits -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                        @foreach($products as $product)
                            @include('client.components.product-card', ['product' => $product, 'currency' => $currency])
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-10 pt-4 border-t border-neutral-100">
                            {{ $products->links() }}
                        </div>
                    @endif
                    
                @else
                    <!-- Aucun résultat -->
                    <div class="text-center py-16 border border-neutral-100 bg-neutral-50/40">
                        <div class="text-5xl text-neutral-300 mb-4">⚜️</div>
                        <h3 class="text-xl font-serif font-light text-neutral-900 mb-2">Aucun résultat trouvé</h3>
                        <p class="text-neutral-500 text-sm max-w-md mx-auto mb-6">
                            Aucun produit ne correspond à votre recherche "<strong class="text-gold">{{ e($query) }}</strong>"
                        </p>
                        
                        <div class="max-w-sm mx-auto text-left bg-white p-6 border border-neutral-100 mb-8">
                            <h4 class="text-[11px] uppercase tracking-[0.2em] font-semibold text-neutral-900 mb-3">Suggestions :</h4>
                            <ul class="space-y-2 text-xs text-neutral-600">
                                <li class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-gold mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Vérifiez l'orthographe des mots clés</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-gold mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Utilisez des termes plus génériques</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-gold mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Essayez une autre recherche</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-gold mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Parcourez nos catégories</span>
                                </li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('client.catalog') }}" class="inline-flex items-center gap-2 border border-neutral-900 hover:bg-neutral-900 hover:text-white text-neutral-900 text-[11px] uppercase tracking-[0.2em] px-6 py-2.5 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Voir tous les produits
                        </a>
                    </div>
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
    function applySort() {
        const sort = document.getElementById('sort_select').value;
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sort);
        window.location.href = url.toString();
    }
    
    // Animation sur la recherche
    const searchForm = document.querySelector('form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="loader"></span>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        });
    }
</script>
@endpush

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
</style>
@endpush
@endsection