@extends('admin.layouts.app')

@section('title', 'Devises')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">Devises</h1>
            <p class="text-sm text-neutral-500 mt-1">Gérez les devises et les taux de change</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.currencies.preview') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Aperçu conversions
            </a>
            <a href="{{ route('admin.currencies.export') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter CSV
            </a>
            <form action="{{ route('admin.currencies.sync-rates') }}" method="POST" class="inline">
    @csrf
    <button type="submit" 
            class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-2"
            onclick="return confirm('Synchroniser les taux de change avec l\'API externe ?')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Sync taux
    </button>
</form>
            <a href="{{ route('admin.currencies.create') }}" 
               class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle devise
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Total devises</span>
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-1">{{ number_format($totalCurrencies) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Devises actives</span>
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($activeCurrencies) }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Par défaut</span>
                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gold mt-1">{{ $defaultCurrency->code ?? 'USD' }}</p>
        </div>
        
        <div class="bg-white rounded-lg border border-neutral-200 p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-neutral-500 uppercase tracking-wider">Taux de référence</span>
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-neutral-900 mt-1">1 USD = ?</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg border border-neutral-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.currencies.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Code, nom ou symbole..."
                       class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Statut</label>
                <select name="status" class="border border-neutral-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-neutral-900">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gold hover:text-black transition-colors">
                    Filtrer
                </button>
                @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('admin.currencies.index') }}" class="ml-2 text-neutral-500 hover:text-neutral-900 text-sm">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des devises -->
    <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Symbole</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Taux (1 USD)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Par défaut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($currencies as $currency)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-neutral-900">{{ $currency->code }}</span>
                                @if($currency->is_default)
                                    <span class="text-[10px] bg-gold/20 text-gold-dark px-1.5 py-0.5 rounded">Défaut</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-600">{{ $currency->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-lg">{{ $currency->symbol }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono">{{ number_format($currency->rate, 4) }}</span>
                                <button onclick="showRateModal({{ $currency->id }}, '{{ $currency->code }}', {{ $currency->rate }})"
                                        class="p-1 text-neutral-400 hover:text-gold transition-colors" title="Modifier le taux">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($currency->is_default)
                                <span class="text-green-600 font-medium">Défaut</span>
                            @else
                                <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-neutral-400 hover:text-gold transition-colors">
                                        Définir par défaut
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" 
                                       onchange="toggleActive({{ $currency->id }}, this.checked)"
                                       {{ $currency->is_active ? 'checked' : '' }}
                                       {{ $currency->is_default ? 'disabled' : '' }}>
                                <div class="w-9 h-5 bg-neutral-300 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all {{ $currency->is_default ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.currencies.edit', $currency) }}" 
                                   class="p-1.5 text-neutral-500 hover:text-blue-600 transition-colors" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if(!$currency->is_default && $currency->code !== 'USD')
                                <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette devise ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-500 hover:text-red-600 transition-colors" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-neutral-500">Aucune devise trouvée</p>
                                <a href="{{ route('admin.currencies.create') }}" class="mt-4 text-gold hover:underline text-sm">
                                    Créer votre première devise
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($currencies->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $currencies->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal modification taux -->
<div id="rateModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-neutral-200 flex justify-between items-center">
            <h3 class="font-semibold text-neutral-900">Modifier le taux</h3>
            <button onclick="closeRateModal()" class="text-neutral-400 hover:text-neutral-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="rateForm" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-neutral-700 mb-1">Taux (1 USD = ?)</label>
                <input type="number" name="rate" id="rateValue" step="0.0001" min="0.0001" 
                       class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 focus:outline-none focus:border-gold">
                <p class="text-xs text-neutral-400 mt-1">Exemple: 1 USD = 2850 CDF</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRateModal()" 
                        class="flex-1 border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
                    Annuler
                </button>
                <button type="submit" 
                        class="flex-1 bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentCurrencyId = null;
    
    function showRateModal(id, code, rate) {
        currentCurrencyId = id;
        const modal = document.getElementById('rateModal');
        const form = document.getElementById('rateForm');
        const rateInput = document.getElementById('rateValue');
        
        rateInput.value = rate;
        form.action = `/admin/currencies/${id}/update-rate`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeRateModal() {
        const modal = document.getElementById('rateModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function toggleActive(id, isActive) {
        fetch(`/admin/currencies/${id}/toggle-active`, {
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
    
    @media (max-width: 768px) {
        .responsive-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>
@endpush
@endsection