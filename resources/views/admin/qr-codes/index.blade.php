@extends('admin.layouts.app')

@section('title', 'QR Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">QR Codes</h1>
            <p class="text-sm text-neutral-500 mt-1">Gérez vos QR codes pour produits, catégories et catalogue</p>
        </div>
        <a href="{{ route('admin.qr-codes.create') }}" 
           class="inline-flex items-center justify-center bg-neutral-900 text-white px-4 h-11 rounded-xl text-sm font-medium hover:bg-gold hover:text-neutral-950 transition-colors shadow-sm gap-2 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouveau QR Code
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Total -->
        <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
            <div class="flex items-center justify-between text-neutral-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Total</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3 3h10c1.5 0 3-1 3-3V7c0-2-1.5-3-3-3H7c-1.5 0-3 1-3 3z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-2">{{ number_format($stats['total']) }}</p>
        </div>
        
        <!-- Actifs -->
        <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
            <div class="flex items-center justify-between text-green-500">
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Actifs</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-600 mt-2">{{ number_format($stats['active']) }}</p>
        </div>
        
        <!-- Catalogue -->
        <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
            <div class="flex items-center justify-between text-purple-500">
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Catalogue</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-2">{{ number_format($stats['catalog']) }}</p>
        </div>
        
        <!-- Catégories -->
        <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
            <div class="flex items-center justify-between text-blue-500">
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Catégories</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-2">{{ number_format($stats['category']) }}</p>
        </div>
        
        <!-- Produits -->
        <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
            <div class="flex items-center justify-between text-emerald-500">
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Produits</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-2">{{ number_format($stats['product']) }}</p>
        </div>
        
        <!-- Scans -->
        <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
            <div class="flex items-center justify-between text-gold">
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Scans</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gold mt-2">{{ number_format($stats['total_scans']) }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl border border-neutral-200 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.qr-codes.index') }}" class="flex flex-col md:flex-row items-end gap-3">
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-neutral-600 mb-1.5">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nom ou code..."
                       class="w-full h-11 border border-neutral-300 rounded-xl px-4 text-sm focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 bg-neutral-50/50">
            </div>
            
            <div class="w-full md:w-44">
                <label class="block text-xs font-semibold text-neutral-600 mb-1.5">Type</label>
                <select name="type" class="w-full h-11 border border-neutral-300 rounded-xl px-3 text-sm bg-white focus:outline-none focus:border-neutral-900">
                    <option value="">Tous les types</option>
                    <option value="catalog" {{ request('type') == 'catalog' ? 'selected' : '' }}>Catalogue</option>
                    <option value="category" {{ request('type') == 'category' ? 'selected' : '' }}>Catégorie</option>
                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Produit</option>
                </select>
            </div>
            
            <div class="w-full md:w-44">
                <label class="block text-xs font-semibold text-neutral-600 mb-1.5">Statut</label>
                <select name="status" class="w-full h-11 border border-neutral-300 rounded-xl px-3 text-sm bg-white focus:outline-none focus:border-neutral-900">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            
            <div class="w-full md:w-auto flex items-center gap-2">
                <button type="submit" class="w-full md:w-auto h-11 bg-neutral-900 text-white px-5 rounded-xl text-sm font-medium hover:bg-neutral-800 transition-colors shadow-sm">
                    Filtrer
                </button>
                @if(request()->filled('search') || request()->filled('type') || request()->filled('status'))
                    <a href="{{ route('admin.qr-codes.index') }}" class="h-11 px-3 border border-neutral-300 rounded-xl flex items-center justify-center text-neutral-500 hover:text-neutral-900 hover:bg-neutral-50 transition-colors" title="Réinitialiser">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des QR Codes -->
    <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-50 border-b border-neutral-200 text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                        <th class="px-6 py-4 w-20">Aperçu</th>
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Destination</th>
                        <th class="px-6 py-4">Scans</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Créé le</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-sm text-neutral-700">
                    @forelse($qrCodes as $qrCode)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 bg-neutral-50 border border-neutral-200 rounded-lg flex items-center justify-center p-1.5">
                                <svg class="w-full h-full text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="2" y="2" width="6" height="6" stroke-width="2" />
                                    <rect x="16" y="2" width="6" height="6" stroke-width="2" />
                                    <rect x="2" y="16" width="6" height="6" stroke-width="2" />
                                    <rect x="16" y="16" width="3" height="3" stroke-width="2" fill="currentColor"/>
                                    <rect x="21" y="21" width="1" height="1" stroke-width="1" fill="currentColor"/>
                                </svg>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-neutral-900">{{ $qrCode->name }}</span>
                            <div class="text-xs text-neutral-400 mt-0.5">Code: {{ $qrCode->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                {{ $qrCode->type === 'catalog' ? 'bg-purple-50 text-purple-700 border border-purple-100' : '' }}
                                {{ $qrCode->type === 'category' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                                {{ $qrCode->type === 'product' ? 'bg-green-50 text-green-700 border border-green-100' : '' }}">
                                {{ ucfirst($qrCode->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-600">
                            @if($qrCode->type === 'catalog')
                                <span class="text-neutral-500 italic">Catalogue général</span>
                            @elseif($qrCode->type === 'category' && $qrCode->category)
                                <span class="font-medium">{{ $qrCode->category->name }}</span>
                            @elseif($qrCode->type === 'product' && $qrCode->product)
                                <span class="font-medium">{{ $qrCode->product->name }}</span>
                            @else
                                <span class="text-neutral-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="font-semibold text-neutral-900">{{ number_format($qrCode->scan_count) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" class="sr-only peer" 
                                       onchange="toggleActive({{ $qrCode->id }}, this.checked)"
                                       {{ $qrCode->is_active ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-neutral-200 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all border border-neutral-300 peer-checked:border-gold"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-xs text-neutral-500">
                            {{ $qrCode->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.qr-codes.show', $qrCode) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.qr-codes.edit', $qrCode) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-blue-600 hover:bg-neutral-100 rounded-lg transition-colors" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.qr-codes.destroy', $qrCode) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce QR code ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-red-600 hover:bg-neutral-100 rounded-lg transition-colors" title="Supprimer">
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
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="w-12 h-12 rounded-xl bg-neutral-50 border border-neutral-200 flex items-center justify-center text-neutral-400 mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="6" height="6" stroke-width="1.5"/>
                                        <rect x="15" y="3" width="6" height="6" stroke-width="1.5"/>
                                        <rect x="3" y="15" width="6" height="6" stroke-width="1.5"/>
                                        <rect x="15" y="15" width="6" height="6" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <p class="text-neutral-900 font-medium text-sm">Aucun QR code trouvé</p>
                                <p class="text-neutral-400 text-xs mt-1 mb-4">Il n'y a aucun résultat correspondant à vos critères actuels.</p>
                                <a href="{{ route('admin.qr-codes.create') }}" class="inline-flex h-9 items-center bg-neutral-950 text-white px-4 rounded-lg text-xs font-medium hover:bg-gold hover:text-neutral-950 transition-colors">
                                    Créer un QR code
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($qrCodes->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50/50">
            {{ $qrCodes->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleActive(id, isActive) {
        fetch(`/admin/qr-codes/${id}/toggle-active`, {
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
                // Optionnel: afficher une notification
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
        border-radius: 0.5rem;
        color: #4b5563;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    .pagination .page-item.active .page-link {
        background: #111111;
        border-color: #111111;
        color: #ffffff;
    }
    
    .pagination .page-item .page-link:hover {
        background: #f3f4f6;
        border-color: #cb9b2b;
    }
</style>
@endpush
@endsection