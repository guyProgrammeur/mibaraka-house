@extends('admin.layouts.app')

@section('title', 'Nouveau produit')
@section('header', 'Créer un produit')

@section('content')
<div class="bg-white rounded-lg shadow max-w-3xl mx-auto">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                    <select name="category_id" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->full_path }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix (USD) *</label>
                    <input type="number" name="price" step="0.01" value="{{ old('price') }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité (kg, L, pièce...)</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image principale</label>
                    <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF. Max 2MB</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image secondaire</label>
                    <input type="file" name="image_secondary" accept="image/*" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div class="border-t pt-6">
                <h3 class="font-medium text-gray-900 mb-4">Gestion des stocks</h3>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="track_stock" value="1" {{ old('track_stock') ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm">Suivre le stock</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Quantité</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Seuil d'alerte</label>
                        <input type="number" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', 5) }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
            
            <div class="border-t pt-6">
                <h3 class="font-medium text-gray-900 mb-4">Visibilité</h3>
                <div class="flex gap-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">Produit phare (mis en avant)</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">Actif (visible en boutique)</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Annuler</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Créer le produit
            </button>
        </div>
    </form>
</div>
@endsection