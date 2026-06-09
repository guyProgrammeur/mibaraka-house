@extends('admin.layouts.app')

@section('title', 'Catégories')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">Catégories</h1>
            <p class="text-sm text-neutral-500 mt-1">Gérez vos catégories et sous-catégories</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.export') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter
            </a>
            <a href="{{ route('admin.categories.create') }}" 
               class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle catégorie
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Total</span>
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Principales</span>
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['main']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Sous-catégories</span>
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['sub']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Actives</span>
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg border border-neutral-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nom..."
                       class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Type</label>
                <select name="type" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Toutes</option>
                    <option value="main" {{ request('type') == 'main' ? 'selected' : '' }}>Principales</option>
                    <option value="sub" {{ request('type') == 'sub' ? 'selected' : '' }}>Sous-catégories</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Statut</label>
                <select name="status" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gold hover:text-black transition-colors">
                    Filtrer
                </button>
                @if(request()->has('search') || request()->has('type') || request()->has('status'))
                    <a href="{{ route('admin.categories.index') }}" class="ml-2 text-neutral-500 hover:text-neutral-900 text-sm">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des catégories -->
    <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Produits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} text-gold text-lg"></i>
                                @else
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                                    </svg>
                                @endif
                                <div>
                                    <span class="font-medium text-neutral-900">
                                        @if($category->parent)
                                            <span class="text-neutral-400 text-xs">↳</span>
                                        @endif
                                        {{ $category->name }}
                                    </span>
                                    @if($category->parent)
                                        <div class="text-xs text-neutral-400 mt-0.5">
                                            Parent: {{ $category->parent->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $category->isMainCategory() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $category->depth_level }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs text-neutral-500">{{ $category->slug }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-neutral-600">{{ $category->allProducts()->count() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-neutral-500">{{ $category->position }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" 
                                       onchange="toggleActive({{ $category->id }}, this.checked)"
                                       {{ $category->is_active ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-neutral-300 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.show', $category) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-gold transition-colors" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-blue-600 transition-colors" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button" 
                                        onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                        class="p-1.5 text-neutral-500 hover:text-red-600 transition-colors" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                <form id="delete-form-{{ $category->id }}" 
                                      action="{{ route('admin.categories.destroy', $category) }}" 
                                      method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                                </svg>
                                <p class="text-neutral-500">Aucune catégorie trouvée</p>
                                <a href="{{ route('admin.categories.create') }}" class="mt-4 text-gold hover:underline text-sm">
                                    Créer votre première catégorie
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleActive(id, isActive) {
        fetch(`/admin/categories/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log(data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
    
    function confirmDelete(id, name) {
        if (confirm(`Supprimer la catégorie "${name}" ?\n\nCette action est irréversible.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
@endpush

@push('styles')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.25rem;
        flex-wrap: wrap;
    }
    
    .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        color: #4b5563;
        font-size: 0.875rem;
        transition: all 0.2s ease;
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
    
    @media (max-width: 768px) {
        .responsive-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .responsive-table td,
        .responsive-table th {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush
@endsection