@extends('admin.layouts.app')

@section('title', 'Modifier le produit - ' . $product->name)
@section('header', 'Modifier le produit')

@section('breadcrumb')
    <div class="text-sm text-neutral-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-gold">Produits</a>
        <span class="mx-2">/</span>
        <span class="text-gold">Modifier</span>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Informations générales -->
            <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">Informations générales</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Nom du produit *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                            <p class="text-xs text-neutral-400 mt-1">Slug actuel : <span class="text-gold">{{ $product->slug }}</span></p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Catégorie *</label>
                            <select name="category_id" required class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->full_path }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Prix (USD) *</label>
                            <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" required 
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Unité</label>
                            <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" placeholder="kg, litre, pièce, pack..."
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Poids (kg)</label>
                            <input type="number" name="weight" step="0.01" value="{{ old('weight', $product->weight) }}" placeholder="0.00"
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">SKU / Référence</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" 
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Description</label>
                        <textarea name="description" rows="5" class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">{{ old('description', $product->description) }}</textarea>
                        <p class="text-xs text-neutral-400 mt-1">Description détaillée du produit</p>
                    </div>
                </div>
            </div>
            
            <!-- Images -->
            <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">Images du produit</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-2">Image principale</label>
                            @if($product->image_path)
                            <div class="mb-3">
                                <div class="relative inline-block">
                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="w-32 h-32 object-cover rounded-sm border border-neutral-200">
                                    <button type="button" onclick="document.getElementById('remove_image').value = '1'; this.parentElement.style.display='none'" 
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                            </div>
                            @endif
                            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" 
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                            <p class="text-xs text-neutral-400 mt-1">JPG, PNG, GIF. Max 2MB. Recommandé: 800x800px</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-2">Image secondaire</label>
                            @if($product->image_secondary)
                            <div class="mb-3">
                                <div class="relative inline-block">
                                    <img src="{{ asset('storage/' . $product->image_secondary) }}" class="w-32 h-32 object-cover rounded-sm border border-neutral-200">
                                    <button type="button" onclick="document.getElementById('remove_image_secondary').value = '1'; this.parentElement.style.display='none'" 
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image_secondary" id="remove_image_secondary" value="0">
                            </div>
                            @endif
                            <input type="file" name="image_secondary" accept="image/jpeg,image/png,image/jpg,image/gif" 
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gestion des stocks -->
            <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">Gestion des stocks</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="track_stock" value="1" {{ old('track_stock', $product->track_stock) ? 'checked' : '' }} class="rounded border-neutral-300 text-gold focus:ring-gold">
                                <span class="text-sm font-medium text-neutral-700">Suivre le stock</span>
                            </label>
                            <p class="text-xs text-neutral-400 mt-1">Activez pour gérer les quantités</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Quantité en stock</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-600 mb-1">Seuil d'alerte</label>
                            <input type="number" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold) }}" min="0"
                                   class="w-full border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                            <p class="text-xs text-neutral-400 mt-1">Notification quand stock ≤ ce seuil</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Visibilité -->
            <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">Visibilité</h3>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-neutral-300 text-gold focus:ring-gold">
                            <span class="text-sm font-medium text-neutral-700">Produit phare (mis en avant)</span>
                        </label>
                        
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-neutral-300 text-gold focus:ring-gold">
                            <span class="text-sm font-medium text-neutral-700">Produit actif (visible en boutique)</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Informations additionnelles -->
            <div class="bg-neutral-50 border border-neutral-200 rounded-sm p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-neutral-500">Créé le :</span>
                        <span class="ml-2 text-neutral-700 font-medium">{{ $product->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-500">Dernière modification :</span>
                        <span class="ml-2 text-neutral-700 font-medium">{{ $product->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-500">Nombre de vues :</span>
                        <span class="ml-2 text-gold font-semibold">{{ number_format($product->views) }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-500">Nombre de commandes :</span>
                        <span class="ml-2 text-gold font-semibold">{{ number_format($product->orderItems->sum('quantity')) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-between gap-4">
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2 border border-neutral-300 rounded-sm text-sm font-medium text-neutral-600 hover:bg-neutral-50 transition">
                    Annuler
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('admin.products.duplicate', $product) }}" class="px-5 py-2 border border-gold rounded-sm text-sm font-medium text-gold hover:bg-gold hover:text-black transition" onclick="return confirm('Dupliquer ce produit ?')">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Dupliquer
                    </a>
                    <button type="submit" class="px-5 py-2 bg-gold hover:bg-gold-dark text-black rounded-sm text-sm font-medium transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Preview image principale
    document.querySelector('input[name="image"]')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.querySelector('input[name="image"]').closest('.grid').querySelector('.relative');
                if (preview) preview.remove();
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endpush
@endsection