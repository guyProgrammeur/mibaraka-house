@extends('admin.layouts.app')

@section('title', 'Gestion des produits')
@section('header', 'Produits')

@section('breadcrumb')
    <div class="text-sm text-neutral-500">
        <span class="text-gold">Produits</span> / Liste
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Actions rapides -->
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex gap-3">
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-black px-4 py-2 text-xs uppercase tracking-wider font-semibold transition-all rounded-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nouveau produit
            </a>
            <a href="{{ route('admin.products.export', request()->query()) }}" class="inline-flex items-center gap-2 bg-neutral-800 hover:bg-neutral-900 text-white px-4 py-2 text-xs uppercase tracking-wider font-semibold transition-all rounded-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Exporter CSV
            </a>
        </div>
        
        <!-- Alertes stock bas -->
        <div x-data="{ lowStockOpen: false }" class="relative">
            <button @click="lowStockOpen = !lowStockOpen" class="flex items-center gap-2 text-amber-600 hover:text-amber-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="text-xs font-medium">Stock bas</span>
            </button>
            <div x-show="lowStockOpen" @click.away="lowStockOpen = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-sm shadow-xl z-50 border border-neutral-200">
                <div class="p-3 border-b border-neutral-100">
                    <h4 class="text-xs font-semibold uppercase tracking-wider">Produits en stock bas</h4>
                </div>
                <div class="max-h-64 overflow-y-auto" id="lowStockList">
                    <div class="p-4 text-center text-neutral-500 text-xs">Chargement...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="bg-white border border-neutral-200 rounded-sm">
        <div class="p-5 border-b border-neutral-100">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Filtres avancés</h3>
        </div>
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." class="border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                
                <select name="category_id" class="border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->full_path }}
                    </option>
                    @endforeach
                </select>
                
                <select name="status" class="border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                    <option value="">Tous statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Mis en avant</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Stock bas</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Rupture</option>
                </select>
                
                <div class="flex gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Prix min" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Prix max" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                </div>
                
                <button type="submit" class="bg-neutral-800 hover:bg-black text-white px-4 py-2 text-xs uppercase tracking-wider font-medium transition-all rounded-sm flex items-center justify-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Filtrer
                </button>
                
                @if(request()->anyFilled(['search', 'category_id', 'status', 'min_price', 'max_price']))
                <a href="{{ route('admin.products.index') }}" class="border border-neutral-200 hover:border-black rounded-sm px-4 py-2 text-xs uppercase tracking-wider text-center transition-all flex items-center justify-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Réinitialiser
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tableau des produits -->
    <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Nom / Référence</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Prix (USD)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-neutral-50 transition">
                        <td class="px-4 py-3">
                            @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" class="w-10 h-10 object-cover rounded-sm">
                            @else
                            <div class="w-10 h-10 bg-neutral-100 rounded-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-neutral-900">{{ $product->name }}</div>
                            <div class="text-xs text-neutral-400 mt-0.5">Slug: {{ $product->slug }}</div>
                            @if($product->is_featured)
                            <span class="inline-block mt-1 text-[10px] font-semibold uppercase tracking-wider text-gold">★ En vedette</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-neutral-600">
                            {{ $product->category?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-neutral-900">
                            $ {{ number_format($product->price, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            @if(!$product->track_stock)
                                <span class="text-xs text-neutral-400">Non suivi</span>
                            @elseif($product->stock_quantity <= 0)
                                <span class="inline-flex items-center gap-1 text-xs text-red-600">
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    Rupture
                                </span>
                            @elseif($product->stock_quantity <= $product->stock_alert_threshold)
                                <span class="inline-flex items-center gap-1 text-xs text-amber-600">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                    Stock bas ({{ $product->stock_quantity }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-600">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                    {{ $product->stock_quantity }} unités
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }}">
                                {{ $product->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="p-1.5 text-neutral-500 hover:text-gold transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="{{ route('admin.products.duplicate', $product) }}" class="p-1.5 text-neutral-500 hover:text-gold transition" title="Dupliquer" onclick="return confirm('Dupliquer ce produit ?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </a>
                                <a href="{{ route('admin.products.toggle-active', $product) }}" class="p-1.5 text-neutral-500 hover:text-amber-600 transition" title="{{ $product->is_active ? 'Désactiver' : 'Activer' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.products.toggle-featured', $product) }}" class="p-1.5 text-neutral-500 hover:text-gold transition" title="{{ $product->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}">
                                    <svg class="w-4 h-4" fill="{{ $product->is_featured ? '#D4AF37' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit définitivement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-red-600 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-neutral-500">
                            <svg class="w-12 h-12 mx-auto text-neutral-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>Aucun produit trouvé</p>
                            <a href="{{ route('admin.products.create') }}" class="inline-block mt-3 text-gold hover:underline text-sm">Créer votre premier produit</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-neutral-200 bg-neutral-50">
            {{ $products->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chargement des produits en stock bas
    fetch('{{ route("admin.products.low-stock") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('lowStockList');
            if (data.count > 0) {
                container.innerHTML = data.products.map(product => `
                    <a href="${product.url}" class="block p-3 border-b border-neutral-100 hover:bg-neutral-50 transition">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-neutral-800">${product.name}</span>
                            <span class="text-xs text-amber-600 font-semibold">Stock: ${product.stock}</span>
                        </div>
                        <div class="w-full bg-neutral-200 rounded-full h-1 mt-2">
                            <div class="bg-amber-500 h-1 rounded-full" style="width: ${Math.min(100, (product.stock / product.threshold) * 100)}%"></div>
                        </div>
                    </a>
                `).join('');
            } else {
                container.innerHTML = '<div class="p-4 text-center text-neutral-500 text-xs">Aucun produit en stock bas</div>';
            }
        })
        .catch(() => {
            document.getElementById('lowStockList').innerHTML = '<div class="p-4 text-center text-red-500 text-xs">Erreur de chargement</div>';
        });
</script>
@endpush
@endsection