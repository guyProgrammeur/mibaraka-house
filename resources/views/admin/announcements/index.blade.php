@extends('admin.layouts.app')

@section('title', 'Annonces')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">Annonces</h1>
            <p class="text-sm text-neutral-500 mt-1">Gérez vos annonces promotionnelles et informations</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" 
           class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors flex items-center gap-2 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvelle annonce
        </a>
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
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Actives</span>
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Inactives</span>
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['inactive']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Positions</span>
                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gold mt-1">{{ count($stats['positions']) }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg border border-neutral-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.announcements.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Titre ou message..."
                       class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Type</label>
                <select name="type" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Tous les types</option>
                    @foreach($stats['types'] as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Position</label>
                <select name="position" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Toutes les positions</option>
                    @foreach($stats['positions'] as $key => $label)
                        <option value="{{ $key }}" {{ request('position') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
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
                @if(request()->has('search') || request()->has('type') || request()->has('position') || request()->has('status'))
                    <a href="{{ route('admin.announcements.index') }}" class="ml-2 text-neutral-500 hover:text-neutral-900 text-sm">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des annonces -->
    <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Annonce</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Validité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    @if($announcement->icon)
                                        <i class="{{ $announcement->icon }} text-gold text-sm"></i>
                                    @endif
                                    <span class="font-medium text-neutral-900">{{ $announcement->title }}</span>
                                    @if($announcement->badge)
                                        <span class="text-[10px] bg-neutral-100 text-neutral-600 px-1.5 py-0.5 rounded">
                                            {{ $announcement->badge }}
                                        </span>
                                    @endif
                                </div>
                                @if($announcement->message)
                                    <p class="text-xs text-neutral-500 mt-1 line-clamp-1">{{ $announcement->message }}</p>
                                @endif
                                @if($announcement->button_text)
                                    <span class="text-[10px] text-gold mt-1 inline-block">🔗 {{ $announcement->button_text }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $announcement->type === 'text' ? 'bg-gray-100 text-gray-700' : '' }}
                                {{ $announcement->type === 'button' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $announcement->type === 'image' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $announcement->type === 'image_text' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $announcement->type === 'banner' ? 'bg-pink-100 text-pink-700' : '' }}">
                                {{ $stats['types'][$announcement->type] ?? $announcement->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-neutral-600">
                                {{ $stats['positions'][$announcement->position] ?? $announcement->position }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">
                            {{ $announcement->order }}
                        </td>
                        <td class="px-6 py-4">
                            @if($announcement->start_date || $announcement->end_date)
                                <div class="text-xs">
                                    @if($announcement->start_date)
                                        <div>Du: {{ $announcement->start_date->format('d/m/Y') }}</div>
                                    @endif
                                    @if($announcement->end_date)
                                        <div>Au: {{ $announcement->end_date->format('d/m/Y') }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-neutral-400">Permanent</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" 
                                       onchange="toggleActive({{ $announcement->id }}, this.checked)"
                                       {{ $announcement->is_active ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-neutral-300 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.announcements.show', $announcement) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-gold transition-colors" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-blue-600 transition-colors" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette annonce ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-red-600 transition-colors" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.418.586l-4.331-8.167a2.415 2.415 0 00-1.248-.996L1 9.584l3.003-.518a2.416 2.416 0 001.248-.996l4.331-8.167a1.76 1.76 0 013.418.586zM14 15a4 4 0 000-8v8z"></path>
                                </svg>
                                <p class="text-neutral-500">Aucune annonce trouvée</p>
                                <a href="{{ route('admin.announcements.create') }}" class="mt-4 text-gold hover:underline text-sm">
                                    Créer votre première annonce
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($announcements->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleActive(id, isActive) {
        fetch(`/admin/announcements/${id}/toggle`, {
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
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection