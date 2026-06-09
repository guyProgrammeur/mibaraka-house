@extends('admin.layouts.app')

@section('title', 'Modifier l\'annonce')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.announcements.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <a href="{{ route('admin.announcements.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">Annonces</a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium">Modifier</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">Modifier l'annonce</h1>
            <p class="text-sm text-neutral-500 mt-1">Modifiez les informations de l'annonce</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.announcements.index') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
                Annuler
            </a>
            <button type="submit" form="edit-announcement-form" 
                    class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors shadow-sm">
                Enregistrer
            </button>
        </div>
    </div>

    <!-- Formulaire -->
    <form id="edit-announcement-form" action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Informations générales -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Informations générales</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Les informations de base de l'annonce</p>
            </div>
            <div class="p-6 space-y-5">
                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                           class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('title') border-red-500 @enderror"
                           placeholder="Ex: Livraison Gratuite">
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Message</label>
                    <textarea name="message" rows="3" 
                              class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('message') border-red-500 @enderror"
                              placeholder="Contenu de l'annonce...">{{ old('message', $announcement->message) }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Badge -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Badge</label>
                    <input type="text" name="badge" value="{{ old('badge', $announcement->badge) }}"
                           class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('badge') border-red-500 @enderror"
                           placeholder="Ex: NOUVEAU, PROMO, LIMITÉ...">
                    <p class="text-xs text-neutral-400 mt-1">Petit texte d'accroche (optionnel)</p>
                    @error('badge')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Type et affichage -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Type et affichage</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Personnalisez l'apparence et le comportement</p>
            </div>
            <div class="p-6 space-y-5">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">
                        Type d'annonce <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        @foreach($types as $typeKey => $typeLabel)
                            <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                                <input type="radio" name="type" value="{{ $typeKey }}" 
                                       {{ old('type', $announcement->type) == $typeKey ? 'checked' : '' }} 
                                       class="sr-only peer">
                                <span class="text-xs font-medium peer-checked:text-gold">{{ $typeLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">
                        Position <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($positions as $posKey => $posLabel)
                            <label class="flex items-center justify-center p-3 border border-neutral-300 rounded-lg cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                                <input type="radio" name="position" value="{{ $posKey }}" 
                                       {{ old('position', $announcement->position) == $posKey ? 'checked' : '' }} 
                                       class="sr-only peer">
                                <span class="text-xs font-medium peer-checked:text-gold">{{ $posLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('position')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icône -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Icône (Font Awesome)</label>
                    <div class="flex items-center gap-3">
                        <div id="icon-preview" class="w-10 h-10 bg-neutral-100 rounded-lg flex items-center justify-center text-neutral-600 text-xl">
                            <i class="{{ $announcement->icon ?? 'fas fa-bullhorn' }}"></i>
                        </div>
                        <select name="icon" class="flex-1 border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors @error('icon') border-red-500 @enderror">
                            @foreach($iconOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('icon', $announcement->icon) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('icon')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ordre -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="order" value="{{ old('order', $announcement->order ?? 0) }}"
                           class="w-32 border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors @error('order') border-red-500 @enderror"
                           min="0">
                    <p class="text-xs text-neutral-400 mt-1">Plus le chiffre est petit, plus l'annonce apparaît en haut</p>
                    @error('order')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Bouton CTA -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Bouton d'appel à l'action</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Optionnel - Ajoutez un bouton à votre annonce</p>
            </div>
            <div class="p-6 space-y-5">
                <!-- Texte du bouton -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Texte du bouton</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $announcement->button_text) }}"
                           class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors @error('button_text') border-red-500 @enderror"
                           placeholder="Ex: En savoir plus">
                    @error('button_text')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lien du bouton -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Lien du bouton</label>
                    <select name="button_link" class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors @error('button_link') border-red-500 @enderror">
                        @foreach($linkOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('button_link', $announcement->button_link) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-neutral-400 mt-1">Ou entrez une URL personnalisée ci-dessous</p>
                    @error('button_link')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL personnalisée -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">URL personnalisée</label>
                    <input type="url" name="custom_url" value="{{ old('custom_url', $announcement->button_link ?? '') }}"
                           class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors"
                           placeholder="https://exemple.com/page">
                    <p class="text-xs text-neutral-400 mt-1">Saisissez une URL complète pour remplacer la sélection ci-dessus</p>
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Image</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Optionnel - Ajoutez une image à votre annonce</p>
            </div>
            <div class="p-6 space-y-5">
                @if($announcement->image)
                    <div class="mb-3 p-4 bg-neutral-50 rounded-lg flex items-center gap-4">
                        <img src="{{ Storage::url($announcement->image) }}" alt="Image actuelle" class="w-20 h-20 object-cover rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm text-neutral-600">Image actuelle</p>
                            <label class="inline-flex items-center gap-2 text-xs text-red-500 cursor-pointer mt-1">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-red-300">
                                <span>Supprimer cette image</span>
                            </label>
                        </div>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Nouvelle image</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors file:mr-3 file:py-1 file:px-3 file:border-0 file:bg-neutral-100 file:text-neutral-700 hover:file:bg-neutral-200 file:rounded-md file:text-xs file:font-medium file:cursor-pointer">
                    <p class="text-xs text-neutral-400 mt-1">Format JPG, PNG, GIF, WEBP. Max 2MB.</p>
                    @error('image')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Dates de validité -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Période de validité</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Optionnel - Limitez l'affichage dans le temps</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Date de début</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $announcement->start_date ? $announcement->start_date->format('Y-m-d') : '') }}"
                               class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors">
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Date de fin</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $announcement->end_date ? $announcement->end_date->format('Y-m-d') : '') }}"
                               class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-gold transition-colors">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-neutral-400 mt-2">Laissez vide pour une validité permanente</p>
            </div>
        </div>

        <!-- Statut -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Statut</h2>
            </div>
            <div class="p-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-neutral-300 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    <span class="ml-3 text-sm font-medium text-neutral-700">Annonce active</span>
                </label>
                <p class="text-xs text-neutral-500 mt-2">Les annonces inactives ne seront pas affichées sur le site.</p>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Prévisualisation de l'icône
    const iconSelect = document.querySelector('select[name="icon"]');
    const iconPreview = document.getElementById('icon-preview');
    
    if (iconSelect && iconPreview) {
        function updateIconPreview() {
            const selectedIcon = iconSelect.value;
            if (selectedIcon) {
                iconPreview.innerHTML = `<i class="${selectedIcon}"></i>`;
            }
        }
        
        iconSelect.addEventListener('change', updateIconPreview);
        updateIconPreview();
    }
    
    // Gestion de l'URL personnalisée
    const customUrlInput = document.querySelector('input[name="custom_url"]');
    const buttonLinkSelect = document.querySelector('select[name="button_link"]');
    
    if (customUrlInput && buttonLinkSelect) {
        customUrlInput.addEventListener('input', function() {
            if (this.value) {
                buttonLinkSelect.disabled = true;
                buttonLinkSelect.value = '';
            } else {
                buttonLinkSelect.disabled = false;
            }
        });
        
        buttonLinkSelect.addEventListener('change', function() {
            if (this.value) {
                customUrlInput.disabled = true;
                customUrlInput.value = '';
            } else {
                customUrlInput.disabled = false;
            }
        });
        
        // Initialisation
        if (customUrlInput.value) {
            buttonLinkSelect.disabled = true;
        } else if (buttonLinkSelect.value) {
            customUrlInput.disabled = true;
        }
    }
</script>
@endpush
@endsection