@extends('admin.layouts.app')

@section('title', 'Nouvelle catégorie')

@section('content')
<div class="max-w-4xl mx-auto pb-24 md:pb-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 px-4 md:px-0">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.categories.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium text-sm md:text-base">Nouvelle catégorie</span>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-neutral-900">Créer une catégorie</h1>
            <p class="text-xs md:text-sm text-neutral-500 mt-1">Ajoutez une nouvelle catégorie ou sous-catégorie</p>
        </div>
        
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
                Annuler
            </a>
            <button type="submit" form="create-category-form" 
                    class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors shadow-sm">
                Créer la catégorie
            </button>
        </div>
    </div>

    <!-- Formulaire -->
    <form id="create-category-form" action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 md:space-y-6 px-4 md:px-0">
        @csrf

        <!-- Informations principales -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Informations générales</h2>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-0.5">Les informations de base de la catégorie</p>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('name') border-red-500 @enderror"
                           placeholder="Ex: Électronique, Mode, Maison...">
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Le slug sera généré automatiquement à partir du nom</p>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent (Catégorie principale) -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Catégorie parente
                    </label>
                    <select name="parent_id" class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors bg-white @error('parent_id') border-red-500 @enderror">
                        <option value="">-- Aucune (catégorie principale) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Laissez vide pour créer une catégorie principale</p>
                    @error('parent_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('description') border-red-500 @enderror"
                              placeholder="Description optionnelle de la catégorie...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Apparence & paramètres -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Apparence & paramètres</h2>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-0.5">Personnalisez l'affichage et le comportement</p>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Icône (Font Awesome) -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Icône (Font Awesome)
                    </label>
                    <div class="flex items-center gap-3">
                        <div id="icon-preview" class="w-10 h-10 bg-neutral-100 rounded-lg flex items-center justify-center text-neutral-600 text-xl">
                            <i class="fas fa-tag"></i>
                        </div>
                        <input type="text" name="icon" value="{{ old('icon', 'fas fa-tag') }}" 
                               class="flex-1 border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors font-mono @error('icon') border-red-500 @enderror"
                               placeholder="fas fa-tag">
                    </div>
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">
                        Ex: <code class="text-gold">fas fa-tag</code>, <code class="text-gold">fas fa-mobile-alt</code>, <code class="text-gold">fas fa-tshirt</code>
                        <br>Consultez <a href="https://fontawesome.com/icons" target="_blank" class="text-gold hover:underline">FontAwesome Icons</a>
                    </p>
                    @error('icon')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Position
                    </label>
                    <input type="number" name="position" value="{{ old('position', 0) }}" 
                           class="w-32 border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('position') border-red-500 @enderror"
                           placeholder="0">
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Ordre d'affichage (plus petit = plus haut)</p>
                    @error('position')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Statut -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Visibilité</h2>
            </div>
            <div class="p-4 md:p-6">
                <label class="relative inline-flex items-center cursor-pointer min-h-6">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-neutral-300 rounded-full peer peer-checked:bg-neutral-900 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    <span class="ml-3 text-sm font-medium text-neutral-700">Catégorie active</span>
                </label>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-2 leading-normal">Les catégories inactives ne seront pas visibles dans la boutique en ligne.</p>
            </div>
        </div>
    </form>

    <!-- Boutons flottants pour mobile -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-neutral-200 px-4 py-3 flex items-center gap-3 z-50">
        <a href="{{ route('admin.categories.index') }}" 
           class="flex-1 border border-neutral-300 text-neutral-700 h-11 flex items-center justify-center rounded-xl text-sm font-medium active:bg-neutral-100 transition-colors">
            Annuler
        </a>
        <button type="submit" form="create-category-form" 
                class="flex-1 bg-neutral-900 text-white h-11 flex items-center justify-center rounded-xl text-sm font-medium active:bg-neutral-800 transition-colors shadow-sm">
            Créer la catégorie
        </button>
    </div>
</div>

@push('scripts')
<script>
    // Prévisualisation de l'icône en temps réel
    const iconInput = document.querySelector('input[name="icon"]');
    const iconPreview = document.getElementById('icon-preview');
    
    if (iconInput && iconPreview) {
        function updateIconPreview() {
            const iconClass = iconInput.value.trim();
            if (iconClass) {
                // Créer un élément i temporaire pour tester
                const tempIcon = document.createElement('i');
                tempIcon.className = iconClass;
                if (tempIcon.classList.length > 0) {
                    iconPreview.innerHTML = `<i class="${iconClass}"></i>`;
                } else {
                    iconPreview.innerHTML = `<i class="fas fa-tag"></i>`;
                }
            } else {
                iconPreview.innerHTML = `<i class="fas fa-tag"></i>`;
            }
        }
        
        iconInput.addEventListener('input', updateIconPreview);
        updateIconPreview();
    }
</script>
@endpush

@push('styles')
<style>
    /* Animation pour l'aperçu de l'icône */
    #icon-preview {
        transition: all 0.2s ease;
    }
    
    #icon-preview i {
        font-size: 1.25rem;
    }
    
    /* Style pour le code dans la description */
    code {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
    }
</style>
@endpush
@endsection