@extends('admin.layouts.app')

@section('title', 'Tableau de bord')
@section('header', 'Tableau de bord')

@section('content')
<!-- Cartes statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">Commandes</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_orders']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">En attente</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['pending_orders']) }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">Clients</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_customers']) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">CA mensuel</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} <span class="text-sm font-normal">FC</span></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Deuxième ligne de stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">Produits actifs</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box text-indigo-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">Stock bas</p>
                <p class="text-3xl font-bold text-orange-600 mt-1">{{ number_format($stats['low_stock_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">Rupture</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($stats['out_of_stock_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm uppercase tracking-wide">Taux conversion</p>
                <p class="text-3xl font-bold text-teal-600 mt-1">
                    @if($stats['total_orders'] > 0)
                        {{ round(($stats['pending_orders'] / $stats['total_orders']) * 100) }}%
                    @else
                        0%
                    @endif
                </p>
            </div>
            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                <i class="fas fa-percent text-teal-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Commandes récentes -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history text-gold mr-2"></i>
                Commandes récentes
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° commande</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recent_orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($order->total_amount, 2) }} $</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                                @elseif($order->status === 'ready') bg-indigo-100 text-indigo-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            Aucune commande récente
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 text-right">
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Voir toutes les commandes <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Meilleurs produits -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-fire text-gold mr-2"></i>
                Meilleurs produits
            </h3>
        </div>
        <div class="p-6">
            @if($top_products->count() > 0)
                <div class="space-y-5">
                    @foreach($top_products as $index => $product)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                    @if($index == 0) bg-gold text-black
                                    @elseif($index == 1) bg-gray-300 text-gray-700
                                    @elseif($index == 2) bg-amber-600 text-white
                                    @else bg-gray-200 text-gray-600 @endif">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm font-medium text-gray-800">{{ Str::limit($product->name, 30) }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($product->total_sold) }} vendus</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="h-2 rounded-full transition-all duration-500 
                                @if($index == 0) bg-gold
                                @elseif($index == 1) bg-gray-500
                                @elseif($index == 2) bg-amber-500
                                @else bg-blue-500 @endif" 
                                style="width: {{ min(100, ($product->total_sold / max($top_products->first()->total_sold, 1)) * 100) }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-simple text-4xl mb-2 block"></i>
                    Aucune donnée de vente
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Graphique des ventes mensuelles -->
<div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-chart-line text-gold mr-2"></i>
            Évolution des ventes (12 derniers mois)
        </h3>
    </div>
    <div class="p-6">
        @if($monthly_sales->count() > 0)
            <canvas id="salesChart" height="300"></canvas>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-line text-4xl mb-2 block"></i>
                Aucune donnée de vente disponible
            </div>
        @endif
    </div>
</div>

<!-- Top catégories -->
<div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-tags text-gold mr-2"></i>
            Catégories les plus vendues
        </h3>
    </div>
    <div class="p-6">
        @if($top_categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($top_categories as $category)
                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $category->name }}</p>
                        <p class="text-sm text-gray-500">{{ number_format($category->total_sold) }} produits vendus</p>
                    </div>
                    <div class="w-10 h-10 bg-gold/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-tag text-gold"></i>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-tags text-4xl mb-2 block"></i>
                Aucune donnée de catégorie disponible
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($monthly_sales->count() > 0)
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthly_sales->pluck('month')->map(function($m) { 
                $date = \Carbon\Carbon::createFromFormat('Y-m', $m);
                return $date->translatedFormat('M Y');
            })) !!},
            datasets: [{
                label: 'Chiffre d\'affaires ($)',
                data: {!! json_encode($monthly_sales->pluck('total')) !!},
                borderColor: '#D4AF37',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#D4AF37',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'CA: $ ' + context.raw.toLocaleString('fr-FR');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$ ' + value.toLocaleString('fr-FR');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush

@push('styles')
<style>
    .bg-gold {
        background-color: #D4AF37;
    }
    .text-gold {
        color: #D4AF37;
    }
    .border-gold {
        border-color: #D4AF37;
    }
    .bg-gold\/20 {
        background-color: rgba(212, 175, 55, 0.2);
    }
</style>
@endpush

@endsection