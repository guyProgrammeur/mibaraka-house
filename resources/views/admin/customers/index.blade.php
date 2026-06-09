@extends('admin.layouts.app')

@section('title', 'Clients')
@section('header', 'Gestion des clients')

@section('content')
<!-- Cartes statistiques -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total clients</p>
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <i class="fas fa-users text-blue-400 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Clients actifs</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
            </div>
            <i class="fas fa-user-check text-green-400 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Clients fidèles</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['frequent'] }}</p>
            </div>
            <i class="fas fa-star text-yellow-400 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Inactifs</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['inactive'] }}</p>
            </div>
            <i class="fas fa-user-slash text-red-400 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Nouveaux (mois)</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['new_this_month'] }}</p>
            </div>
            <i class="fas fa-user-plus text-purple-400 text-3xl"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
        <div class="flex gap-2">
            <a href="{{ route('admin.customers.refresh-stats') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm" onclick="return confirm('Recalculer les statistiques de tous les clients ?')">
                <i class="fas fa-chart-line mr-2"></i>Recalculer stats
            </a>
            <a href="{{ route('admin.customers.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-download mr-2"></i>Exporter CSV
            </a>
            <a href="{{ route('admin.customers.broadcast-form') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fab fa-whatsapp mr-2"></i>Message groupé
            </a>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, téléphone, email..." class="border rounded-lg px-3 py-2 text-sm">
            
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tous statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                <option value="frequent" {{ request('status') == 'frequent' ? 'selected' : '' }}>Fidèles (5+ commandes)</option>
                <option value="inactive_6months" {{ request('status') == 'inactive_6months' ? 'selected' : '' }}>Inactifs 6 mois</option>
            </select>
            
            <input type="date" name="registered_from" value="{{ request('registered_from') }}" placeholder="Inscrit du" class="border rounded-lg px-3 py-2 text-sm">
            <input type="date" name="registered_to" value="{{ request('registered_to') }}" placeholder="Inscrit au" class="border rounded-lg px-3 py-2 text-sm">
            
            <div class="flex gap-2">
                <input type="number" name="min_spent" value="{{ request('min_spent') }}" placeholder="Dépensé min $" class="border rounded-lg px-3 py-2 text-sm w-1/2">
                <input type="number" name="max_spent" value="{{ request('max_spent') }}" placeholder="Dépensé max $" class="border rounded-lg px-3 py-2 text-sm w-1/2">
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-search mr-2"></i>Filtrer
            </button>
        </form>
    </div>
    
    <!-- Tableau des clients -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adresse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commandes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dépensé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                <div class="text-xs text-gray-500">Client depuis {{ $customer->customer_since }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div><i class="fas fa-phone text-gray-400 mr-1"></i> {{ $customer->formatted_phone }}</div>
                            @if($customer->email)
                            <div class="text-gray-500 text-xs"><i class="fas fa-envelope mr-1"></i> {{ $customer->email }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($customer->full_address)
                            <div class="max-w-xs truncate">{{ $customer->full_address }}</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-center">
                        <span class="font-semibold {{ $customer->total_orders >= 5 ? 'text-yellow-600' : 'text-gray-700' }}">
                            {{ $customer->total_orders }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        {{ number_format($customer->total_spent, 2) }} $
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($customer->last_order_at)
                            {{ $customer->last_order_at->diffForHumans() }}
                        @else
                            <span class="text-gray-400">Jamais</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($customer->is_frequent_buyer)
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                            <i class="fas fa-star mr-1"></i>Fidèle
                        </span>
                        @elseif($customer->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            Actif
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                            Inactif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Supprimer ce client ? Les commandes associées ne seront pas supprimées.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.customers.toggle', $customer) }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas {{ $customer->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2 block"></i>
                        Aucun client trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $customers->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Confirmation de suppression
    document.querySelectorAll('.delete-form button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('⚠️ Attention : Cette action est irréversible. Les commandes du client resteront en base. Continuer ?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection