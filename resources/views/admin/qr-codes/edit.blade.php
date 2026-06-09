@extends('admin.layouts.app')

@section('title', 'Modifier - ' . $qrCode->name)

@section('content')
<div class="max-w-4xl mx-auto pb-24 md:pb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 px-4 md:px-0">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.qr-codes.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <a href="{{ route('admin.qr-codes.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">QR Codes</a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium text-sm md:text-base">Modifier</span>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-neutral-900">{{ $qrCode->name }}</h1>
            <p class="text-xs md:text-sm text-neutral-500 mt-1">Modifiez les informations du QR code</p>
        </div>
        
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('admin.qr-codes.show', $qrCode) }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
                Annuler
            </a>
            <button type="submit" form="edit-qr-form" 
                    class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors shadow-sm">
                Enregistrer
            </button>
        </div>
    </div>

    <form id="edit-qr-form" action="{{ route('admin.qr-codes.update', $qrCode) }}" method="POST" enctype="multipart/form-data" class="space-y-4 md:space-y-6 px-4 md:px-0">
        @csrf
        @method('PUT')

        <!-- Informations principales -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Informations principales</h2>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-0.5">Les informations de base du QR code</p>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $qrCode->name) }}" required
                           class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('name') border-red-500 @enderror"
                           placeholder="Ex: QR Code Promo Noël">
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Nom interne pour identifier ce QR code</p>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug (lecture seule) -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Code unique
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ $qrCode->code }}" readonly
                               class="flex-1 border border-neutral-200 bg-neutral-50 rounded-lg px-3 py-2.5 text-sm text-neutral-500 font-mono cursor-not-allowed">
                        <button type="button" onclick="copyToClipboard('{{ $qrCode->code }}')" 
                                class="p-2.5 text-neutral-500 hover:text-gold transition-colors" title="Copier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Type de destination -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">
                        Type de destination <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <label class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 border border-neutral-200 rounded-xl p-3.5 cursor-pointer hover:bg-neutral-50 transition-all type-option has-[:checked]:border-gold has-[:checked]:bg-gold/5" data-type="catalog">
                            <input type="radio" name="type" value="catalog" class="sr-only type-radio" {{ old('type', $qrCode->type) == 'catalog' ? 'checked' : '' }}>
                            <div class="p-2 bg-purple-50 rounded-lg sm:bg-transparent sm:p-0 shrink-0">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-purple-600 sm:mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold sm:font-medium text-neutral-900 text-left sm:text-center">Catalogue complet</p>
                                <p class="text-xs text-neutral-500 text-left sm:text-center">Tous les produits</p>
                            </div>
                        </label>
                        
                        <label class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 border border-neutral-200 rounded-xl p-3.5 cursor-pointer hover:bg-neutral-50 transition-all type-option has-[:checked]:border-gold has-[:checked]:bg-gold/5" data-type="category">
                            <input type="radio" name="type" value="category" class="sr-only type-radio" {{ old('type', $qrCode->type) == 'category' ? 'checked' : '' }}>
                            <div class="p-2 bg-yellow-50 rounded-lg sm:bg-transparent sm:p-0 shrink-0">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600 sm:mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold sm:font-medium text-neutral-900 text-left sm:text-center">Catégorie</p>
                                <p class="text-xs text-neutral-500 text-left sm:text-center">Une catégorie spécifique</p>
                            </div>
                        </label>
                        
                        <label class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 border border-neutral-200 rounded-xl p-3.5 cursor-pointer hover:bg-neutral-50 transition-all type-option has-[:checked]:border-gold has-[:checked]:bg-gold/5" data-type="product">
                            <input type="radio" name="type" value="product" class="sr-only type-radio" {{ old('type', $qrCode->type) == 'product' ? 'checked' : '' }}>
                            <div class="p-2 bg-blue-50 rounded-lg sm:bg-transparent sm:p-0 shrink-0">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600 sm:mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold sm:font-medium text-neutral-900 text-left sm:text-center">Produit unique</p>
                                <p class="text-xs text-neutral-500 text-left sm:text-center">Un seul produit</p>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catégorie (conditionnel) -->
                <div id="category_field" class="{{ old('type', $qrCode->type) == 'category' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Catégorie <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors bg-white @error('category_id') border-red-500 @enderror">
                        <option value="">-- Sélectionnez une catégorie --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $qrCode->category_id) == $category->id ? 'selected' : '' }}>
                                @if($category->parent)
                                    ↳ {{ $category->parent->name }} › {{ $category->name }}
                                @else
                                    {{ $category->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Produit (conditionnel) -->
                <div id="product_field" class="{{ old('type', $qrCode->type) == 'product' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Produit <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors bg-white @error('product_id') border-red-500 @enderror">
                        <option value="">-- Sélectionnez un produit --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $qrCode->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - {{ number_format($product->price, 0, ',', ' ') }} FC
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('description') border-red-500 @enderror"
                              placeholder="Description optionnelle du QR code...">{{ old('description', $qrCode->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Apparence du QR code -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Apparence du QR code</h2>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-0.5">Personnalisez l'apparence du QR code</p>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Taille -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Taille</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all h-11 has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="size" value="small" {{ old('size', $qrCode->size) == 'small' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-sm font-medium peer-checked:text-neutral-900 text-neutral-600">Petit (200px)</span>
                        </label>
                        <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all h-11 has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="size" value="medium" {{ old('size', $qrCode->size) == 'medium' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-sm font-medium peer-checked:text-neutral-900 text-neutral-600">Moyen (300px)</span>
                        </label>
                        <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all h-11 has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="size" value="large" {{ old('size', $qrCode->size) == 'large' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-sm font-medium peer-checked:text-neutral-900 text-neutral-600">Grand (400px)</span>
                        </label>
                    </div>
                    @error('size')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Couleur -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Couleur</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all h-11 has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="color" value="black" {{ old('color', $qrCode->color) == 'black' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-sm font-medium peer-checked:text-neutral-900 text-neutral-600">Noir</span>
                        </label>
                        <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all h-11 has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="color" value="white" {{ old('color', $qrCode->color) == 'white' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-sm font-medium peer-checked:text-neutral-900 text-neutral-600">Blanc (sur fond foncé)</span>
                        </label>
                        <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all h-11 has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="color" value="custom" {{ old('color', $qrCode->color) == 'custom' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-sm font-medium peer-checked:text-neutral-900 text-neutral-600">Personnalisée</span>
                        </label>
                    </div>
                    
                    <!-- Couleur personnalisée -->
                    <div id="custom_color_field" class="{{ old('color', $qrCode->color) == 'custom' ? '' : 'hidden' }} mt-3">
                        <label class="block text-xs font-medium text-neutral-600 mb-1">Sélectionnez une couleur</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="custom_color" value="{{ old('custom_color', $qrCode->custom_color ?? '#D4AF37') }}" 
                                   class="w-12 h-11 border border-neutral-300 rounded-lg cursor-pointer bg-transparent shrink-0">
                            <input type="text" name="custom_color_hex" value="{{ old('custom_color', $qrCode->custom_color ?? '#D4AF37') }}" 
                                   class="flex-1 border border-neutral-300 rounded-lg px-3 h-11 focus:outline-none focus:border-gold transition-colors font-mono text-sm"
                                   placeholder="#D4AF37">
                        </div>
                        @error('custom_color')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Logo -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Logo (optionnel)</label>
                    @if($qrCode->logo_path)
                        <div class="mb-3 p-3 bg-neutral-50 rounded-lg flex items-center gap-3">
                            <img src="{{ Storage::url($qrCode->logo_path) }}" alt="Logo" class="w-12 h-12 object-cover rounded">
                            <div class="flex-1">
                                <p class="text-sm text-neutral-600">Logo actuel</p>
                                <label class="inline-flex items-center gap-2 text-xs text-red-500 cursor-pointer mt-1">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-red-300">
                                    <span>Supprimer ce logo</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg"
                           class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-gold transition-colors file:mr-3 file:py-1 file:px-3 file:border-0 file:bg-neutral-100 file:text-neutral-700 hover:file:bg-neutral-200 file:rounded-md file:text-xs file:font-medium file:cursor-pointer">
                    <p class="text-[11px] text-neutral-400 mt-1 leading-normal">Format PNG, JPG. Max 512KB. Carré recommandé. Le logo apparaîtra au centre du QR code.</p>
                    @error('logo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Personnalisation du Poster -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Personnalisation du poster</h2>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-0.5">Personnalisez l'apparence de l'affiche</p>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Template -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Template</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <label class="flex flex-col items-center p-2 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="poster_template" value="classic" {{ old('poster_template', $qrCode->poster_template ?? 'classic') == 'classic' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-xs font-medium peer-checked:text-gold">Classique</span>
                        </label>
                        <label class="flex flex-col items-center p-2 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="poster_template" value="luxury" {{ old('poster_template', $qrCode->poster_template) == 'luxury' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-xs font-medium peer-checked:text-gold">Luxe</span>
                        </label>
                        <label class="flex flex-col items-center p-2 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="poster_template" value="elegant" {{ old('poster_template', $qrCode->poster_template) == 'elegant' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-xs font-medium peer-checked:text-gold">Élégant</span>
                        </label>
                        <label class="flex flex-col items-center p-2 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="poster_template" value="modern" {{ old('poster_template', $qrCode->poster_template) == 'modern' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-xs font-medium peer-checked:text-gold">Moderne</span>
                        </label>
                        <label class="flex flex-col items-center p-2 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="poster_template" value="minimal" {{ old('poster_template', $qrCode->poster_template) == 'minimal' ? 'checked' : '' }} class="sr-only peer">
                            <span class="text-xs font-medium peer-checked:text-gold">Minimal</span>
                        </label>
                    </div>
                </div>

                <!-- Couleurs du poster -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Couleur principale</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="poster_primary_color" value="{{ old('poster_primary_color', $qrCode->poster_primary_color ?? '#D4AF37') }}" 
                                   class="w-12 h-11 border border-neutral-300 rounded-lg cursor-pointer">
                            <input type="text" name="poster_primary_color_hex" value="{{ old('poster_primary_color', $qrCode->poster_primary_color ?? '#D4AF37') }}" 
                                   class="flex-1 border border-neutral-300 rounded-lg px-3 h-11 focus:outline-none focus:border-gold transition-colors font-mono text-sm"
                                   placeholder="#D4AF37">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Couleur de fond</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="poster_background_color" value="{{ old('poster_background_color', $qrCode->poster_background_color ?? '#FFFFFF') }}" 
                                   class="w-12 h-11 border border-neutral-300 rounded-lg cursor-pointer">
                            <input type="text" name="poster_background_color_hex" value="{{ old('poster_background_color', $qrCode->poster_background_color ?? '#FFFFFF') }}" 
                                   class="flex-1 border border-neutral-300 rounded-lg px-3 h-11 focus:outline-none focus:border-gold transition-colors font-mono text-sm"
                                   placeholder="#FFFFFF">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Couleur du texte</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="poster_text_color" value="{{ old('poster_text_color', $qrCode->poster_text_color ?? '#1a1a1a') }}" 
                                   class="w-12 h-11 border border-neutral-300 rounded-lg cursor-pointer">
                            <input type="text" name="poster_text_color_hex" value="{{ old('poster_text_color', $qrCode->poster_text_color ?? '#1a1a1a') }}" 
                                   class="flex-1 border border-neutral-300 rounded-lg px-3 h-11 focus:outline-none focus:border-gold transition-colors font-mono text-sm"
                                   placeholder="#1a1a1a">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Message personnalisé</label>
                        <input type="text" name="custom_message" value="{{ old('custom_message', $qrCode->custom_message) }}" 
                               class="w-full border border-neutral-300 rounded-lg px-3 h-11 focus:outline-none focus:border-gold transition-colors"
                               placeholder="Ex: Scannez pour découvrir nos offres">
                    </div>
                </div>

                <!-- Options d'affichage -->
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_brand_name" value="1" class="rounded border-neutral-300 text-gold focus:ring-gold" {{ old('show_brand_name', $qrCode->show_brand_name ?? true) ? 'checked' : '' }}>
                        <span class="text-sm text-neutral-700">Afficher le nom de la marque</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_tagline" value="1" class="rounded border-neutral-300 text-gold focus:ring-gold" {{ old('show_tagline', $qrCode->show_tagline ?? true) ? 'checked' : '' }}>
                        <span class="text-sm text-neutral-700">Afficher le slogan</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Statut -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Statut</h2>
            </div>
            <div class="p-4 md:p-6">
                <label class="relative inline-flex items-center cursor-pointer min-h-6">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $qrCode->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-neutral-300 rounded-full peer peer-checked:bg-neutral-900 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    <span class="ml-3 text-sm font-medium text-neutral-700">QR code actif</span>
                </label>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-2 leading-normal">Les QR codes inactifs ne redirigeront pas les utilisateurs vers la destination configurée.</p>
            </div>
        </div>
    </form>

    <!-- Boutons flottants pour mobile -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-neutral-200 px-4 py-3 flex items-center gap-3 z-50">
        <a href="{{ route('admin.qr-codes.show', $qrCode) }}" 
           class="flex-1 border border-neutral-300 text-neutral-700 h-11 flex items-center justify-center rounded-xl text-sm font-medium active:bg-neutral-100 transition-colors">
            Annuler
        </a>
        <button type="submit" form="edit-qr-form" 
                class="flex-1 bg-neutral-900 text-white h-11 flex items-center justify-center rounded-xl text-sm font-medium active:bg-neutral-800 transition-colors shadow-sm">
            Enregistrer
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeOptions = document.querySelectorAll('.type-option');
        const categoryField = document.getElementById('category_field');
        const productField = document.getElementById('product_field');
        const colorRadios = document.querySelectorAll('input[name="color"]');
        const customColorField = document.getElementById('custom_color_field');
        
        // Synchronisation des champs de couleur hex
        function setupColorSync(primaryPicker, primaryHex, secondaryPicker, secondaryHex, textPicker, textHex) {
            if (primaryPicker && primaryHex) {
                primaryPicker.addEventListener('input', () => primaryHex.value = primaryPicker.value);
                primaryHex.addEventListener('input', () => {
                    if (primaryHex.value.match(/^#[a-fA-F0-9]{6}$/)) primaryPicker.value = primaryHex.value;
                });
            }
            if (secondaryPicker && secondaryHex) {
                secondaryPicker.addEventListener('input', () => secondaryHex.value = secondaryPicker.value);
                secondaryHex.addEventListener('input', () => {
                    if (secondaryHex.value.match(/^#[a-fA-F0-9]{6}$/)) secondaryPicker.value = secondaryHex.value;
                });
            }
            if (textPicker && textHex) {
                textPicker.addEventListener('input', () => textHex.value = textPicker.value);
                textHex.addEventListener('input', () => {
                    if (textHex.value.match(/^#[a-fA-F0-9]{6}$/)) textPicker.value = textHex.value;
                });
            }
        }
        
        // Initialiser les synchronisations de couleurs
        setupColorSync(
            document.querySelector('input[name="poster_primary_color"]'),
            document.querySelector('input[name="poster_primary_color_hex"]'),
            document.querySelector('input[name="poster_background_color"]'),
            document.querySelector('input[name="poster_background_color_hex"]'),
            document.querySelector('input[name="poster_text_color"]'),
            document.querySelector('input[name="poster_text_color_hex"]')
        );
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                const btn = event.target.closest('button');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 2000);
            });
        }
        
        function updateTypeFields() {
            const selectedRadio = document.querySelector('input[name="type"]:checked');
            const type = selectedRadio ? selectedRadio.value : 'catalog';
            
            if (categoryField) categoryField.classList.add('hidden');
            if (productField) productField.classList.add('hidden');
            
            if (type === 'category' && categoryField) {
                categoryField.classList.remove('hidden');
            } else if (type === 'product' && productField) {
                productField.classList.remove('hidden');
            }
        }
        
        function updateColorField() {
            const selectedColor = document.querySelector('input[name="color"]:checked');
            if (customColorField) {
                if (selectedColor && selectedColor.value === 'custom') {
                    customColorField.classList.remove('hidden');
                } else {
                    customColorField.classList.add('hidden');
                }
            }
        }
        
        typeOptions.forEach(opt => {
            opt.addEventListener('click', function() {
                const radio = this.querySelector('.type-radio');
                if (radio) {
                    radio.checked = true;
                    updateTypeFields();
                }
            });
        });
        
        colorRadios.forEach(radio => {
            radio.addEventListener('change', updateColorField);
        });
        
        updateTypeFields();
        updateColorField();
        
        window.copyToClipboard = copyToClipboard;
    });
</script>
@endpush
@endsection