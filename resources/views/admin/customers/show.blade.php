@extends('admin.layouts.app')

@section('title', 'Client - ' . $customer->name)
@section('header', 'Détails du client')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne gauche : Informations client -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Carte profil -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 text-center border-b">
                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-3xl font-bold mx-auto">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h2 class="mt-4 text-xl font-semibold">{{ $customer->name }}</h2>
                <p class="text-gray-500 text-sm">Client depuis {{ $customer->customer_since }}</p>
                
                <div class="mt-3 flex justify-center gap-2">
                    @if($customer->is_frequent_buyer)
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        <i class="fas fa-star"></i> Client fidèle
                    </span>
                    @endif
                    <span class="px-2 py-1 text-xs rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $customer->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Coordonnées</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <i class="fas fa-phone w-5 text-gray-400"></i>
                            <a href="{{ $customer->whatsapp_link }}" target="_blank" class="text-blue-600 hover:underline">
                                {{ $customer->formatted_phone }}
                            </a>
                        </p>
                        @if($customer->email)
                        <p class="text-sm">
                            <i class="fas fa-envelope w-5 text-gray-400"></i>
                            {{ $customer->email }}
                        </p>
                        @endif
                        <p class="text-sm">
                            <i class="fas fa-money-bill-wave w-5 text-gray-400"></i>
                            Devise préférée : <strong>{{ $customer->preferred_currency }}</strong>
                        </p>
                    </div>
                </div>
                
                @if($customer->full_address)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Adresse</h3>
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                        {{ $customer->full_address }}
                    </p>
                </div>
                @endif
                
                <div class="pt-4 border-t">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-blue-600">{{ $ordersCount }}</p>
                            <p class="text-xs text-gray-500">Commandes</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($totalSpent, 2) }} $</p>
                            <p class="text-xs text-gray-500">Total dépensé</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <button type="button" onclick="openWhatsappModal()" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Produits favoris -->
        @if($favoriteProducts->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="font-medium">Produits favoris</h3>
            </div>
            <div class="p-4 space-y-3">
                @foreach($favoriteProducts as $product)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" class="w-10 h-10 object-cover rounded" alt="">
                        @else
                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        @endif
                        <span class="text-sm">{{ $product->name }}</span>
                    </div>
                    <span class="text-sm font-medium">{{ $product->pivot->total_quantity ?? $product->total_quantity ?? '?' }} vendus</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- Colonne droite : Commandes et statistiques -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Statistiques commandes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="font-medium">Statistiques des commandes</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 md:grid-cols-6 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-gray-700">{{ $orderStats['pending'] }}</p>
                        <p class="text-xs text-gray-500">En attente</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $orderStats['confirmed'] }}</p>
                        <p class="text-xs text-gray-500">Confirmées</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ $orderStats['preparing'] }}</p>
                        <p class="text-xs text-gray-500">Préparation</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-yellow-600">{{ $orderStats['ready'] }}</p>
                        <p class="text-xs text-gray-500">Prêtes</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $orderStats['delivered'] }}</p>
                        <p class="text-xs text-gray-500">Livrées</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-red-600">{{ $orderStats['cancelled'] }}</p>
                        <p class="text-xs text-gray-500">Annulées</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphique mensuel -->
        @if($monthlyOrders->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="font-medium">Évolution des commandes (12 derniers mois)</h3>
            </div>
            <div class="p-6">
                <canvas id="ordersChart" height="200"></canvas>
            </div>
        </div>
        @endif
        
        <!-- Historique des commandes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="font-medium">Historique des commandes</h3>
                <a href="{{ route('admin.orders.index', ['customer_phone' => $customer->phone]) }}" class="text-blue-600 text-sm hover:underline">
                    Voir toutes
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° commande</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->orders->take(10) as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm">{{ number_format($order->total_amount, 2) }} {{ $order->currency_code }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Aucune commande
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal WhatsApp -->
<div id="whatsappModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-medium">Envoyer un message WhatsApp</h3>
            <button onclick="closeWhatsappModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.customers.whatsapp', $customer) }}" method="POST">
            @csrf
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-3">
                    <i class="fab fa-whatsapp text-green-600 mr-1"></i>
                    À : {{ $customer->name }} ({{ $customer->formatted_phone }})
                </p>
                <textarea name="message" rows="5" required 
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Votre message..."></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <button type="button" onclick="closeWhatsappModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fab fa-whatsapp mr-2"></i>Envoyer
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openWhatsappModal() {
        document.getElementById('whatsappModal').classList.remove('hidden');
        document.getElementById('whatsappModal').classList.add('flex');
    }
    
    function closeWhatsappModal() {
        document.getElementById('whatsappModal').classList.add('hidden');
        document.getElementById('whatsappModal').classList.remove('flex');
    }
    
    @if($monthlyOrders->count() > 0)
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyOrders->pluck('month')->map(function($m) { 
                return \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'); 
            })) !!},
            datasets: [{
                label: 'Chiffre d\'affaires ($)',
                data: {!! json_encode($monthlyOrders->pluck('total')) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
    @endif
</script>
@endpush
@endsection