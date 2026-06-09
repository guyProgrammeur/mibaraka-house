@extends('admin.layouts.app')

@section('title', 'Avis produits')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">Avis produits</h1>
            <p class="text-sm text-neutral-500 mt-1">Gérez les avis et évaluations des clients</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reviews.export') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Total</span>
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Approuvés</span>
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['approved']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">En attente</span>
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['pending']) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Note moyenne</span>
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['avg_rating']))
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @else
                            <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endif
                    @endfor
                </div>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ number_format($stats['avg_rating'], 1) }}/5</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg border border-neutral-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Client ou commentaire..."
                       class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Produit</label>
                <select name="product_id" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Tous les produits</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Statut</label>
                <select name="status" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Tous</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvés</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Note</label>
                <select name="rating" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Toutes</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 étoiles</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 étoiles</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 étoiles</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 étoiles</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 étoile</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gold hover:text-black transition-colors">
                    Filtrer
                </button>
                @if(request()->has('search') || request()->has('product_id') || request()->has('status') || request()->has('rating'))
                    <a href="{{ route('admin.reviews.index') }}" class="ml-2 text-neutral-500 hover:text-neutral-900 text-sm">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Actions groupées -->
    @if($reviews->count() > 0)
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="text-xs text-neutral-500">Actions groupées :</span>
        <button onclick="bulkAction('approve')" 
                class="text-xs px-3 py-1.5 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition-colors">
            Approuver sélection
        </button>
        <button onclick="bulkAction('reject')" 
                class="text-xs px-3 py-1.5 border border-amber-300 text-amber-700 rounded-lg hover:bg-amber-50 transition-colors">
            Rejeter sélection
        </button>
        <button onclick="bulkAction('delete')" 
                class="text-xs px-3 py-1.5 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
            Supprimer sélection
        </button>
    </div>
    @endif

    <!-- Liste des avis -->
    <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-neutral-300 text-gold focus:ring-gold">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Note</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Avis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="review-checkbox rounded border-neutral-300 text-gold focus:ring-gold" value="{{ $review->id }}">
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <span class="text-sm font-medium text-neutral-900">{{ $review->product->name ?? 'Produit supprimé' }}</span>
                                @if($review->product)
                                    <div class="text-xs text-neutral-400 mt-0.5">ID: {{ $review->product->id }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <span class="text-sm text-neutral-900">{{ $review->customer_name ?? $review->user->name ?? 'Anonyme' }}</span>
                                @if($review->user_id)
                                    <div class="text-xs text-neutral-400 mt-0.5">Client enregistré</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-neutral-600 max-w-xs truncate">{{ $review->comment ?: 'Aucun commentaire' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $review->is_approved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $review->is_approved ? 'Approuvé' : 'En attente' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-neutral-500">
                            {{ $review->created_at->format('d/m/Y') }}
                            <div class="text-xs text-neutral-400">{{ $review->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.reviews.show', $review) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-gold transition-colors" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-green-600 transition-colors" title="Approuver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                
                                @if($review->is_approved)
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-amber-600 transition-colors" title="Rejeter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet avis ?')">
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
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-neutral-500">Aucun avis trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reviews->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Sélectionner/désélectionner tous les avis
    const selectAllCheckbox = document.getElementById('select-all');
    const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            reviewCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }
    
    // Actions groupées
    function bulkAction(action) {
        const selectedIds = Array.from(reviewCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un avis.');
            return;
        }
        
        let confirmMessage = '';
        let url = '';
        
        switch(action) {
            case 'approve':
                confirmMessage = `Approuver ${selectedIds.length} avis ?`;
                url = '{{ route("admin.reviews.bulk-approve") }}';
                break;
            case 'reject':
                confirmMessage = `Rejeter ${selectedIds.length} avis ?`;
                url = '{{ route("admin.reviews.bulk-reject") }}';
                break;
            case 'delete':
                confirmMessage = `Supprimer ${selectedIds.length} avis ? Cette action est irréversible.`;
                url = '{{ route("admin.reviews.bulk-delete") }}';
                break;
        }
        
        if (confirm(confirmMessage)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = JSON.stringify(selectedIds);
            form.appendChild(idsInput);
            
            document.body.appendChild(form);
            form.submit();
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