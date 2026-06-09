@extends('admin.layouts.app')

@section('title', 'Tableau de bord')
@section('header', 'Tableau de bord')

@section('breadcrumb')
    <div class="text-sm text-neutral-500">
        <span class="text-gold">Dashboard</span> / Aperçu général
    </div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Cartes statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white border border-neutral-200 rounded-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Commandes totales</p>
                    <p class="text-3xl font-bold text-neutral-800 mt-2">{{ number_format($stats['total_orders']) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs text-amber-600">En attente: {{ number_format($stats['pending_orders']) }}</span>
            </div>
        </div>
        
        <div class="bg-white border border-neutral-200 rounded-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Chiffre d'affaires</p>
                    <p class="text-3xl font-bold text-neutral-800 mt-2">{{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} <span class="text-sm font-normal">USD</span></p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xs text-neutral-500">Ce mois-ci</span>
            </div>
        </div>
        
        <div class="bg-white border border-neutral-200 rounded-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Clients</p>
                    <p class="text-3xl font-bold text-neutral-800 mt-2">{{ number_format($stats['total_customers']) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-neutral-200 rounded-sm p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Produits actifs</p>
                    <p class="text-3xl font-bold text-neutral-800 mt-2">{{ number_format($stats['total_products']) }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-3">
                @if($stats['low_stock_products'] > 0)
                <span class="text-xs text-amber-600">Stock bas: {{ number_format($stats['low_stock_products']) }}</span>
                @endif
                @if($stats['out_of_stock_products'] > 0)
                <span class="text-xs text-red-600">Rupture: {{ number_format($stats['out_of_stock_products']) }}</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Graphique et classement -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique des ventes mensuelles -->
        <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">
                    <span class="w-2 h-2 bg-gold inline-block rounded-full mr-2"></span>
                    Évolution des ventes
                </h3>
            </div>
            <div class="p-5">
                @if($monthly_sales->count() > 0)
                    <canvas id="salesChart" height="280"></canvas>
                @else
                    <div class="text-center py-12 text-neutral-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p>Aucune donnée de vente disponible</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Top produits -->
        <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">
                    <span class="w-2 h-2 bg-gold inline-block rounded-full mr-2"></span>
                    Meilleurs produits
                </h3>
            </div>
            <div class="p-5">
                @if($top_products->count() > 0)
                    <div class="space-y-4">
                        @foreach($top_products as $index => $product)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                        @if($index == 0) bg-gold text-black
                                        @elseif($index == 1) bg-neutral-300 text-neutral-700
                                        @elseif($index == 2) bg-amber-600 text-white
                                        @else bg-neutral-100 text-neutral-500 @endif">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium text-neutral-700">{{ Str::limit($product->name, 30) }}</span>
                                </div>
                                <span class="text-sm font-semibold text-neutral-900">{{ number_format($product->total_sold) }} vendus</span>
                            </div>
                            <div class="w-full bg-neutral-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full 
                                    @if($index == 0) bg-gold
                                    @elseif($index == 1) bg-neutral-500
                                    @elseif($index == 2) bg-amber-500
                                    @else bg-blue-500 @endif" 
                                    style="width: {{ min(100, ($product->total_sold / max($top_products->first()->total_sold, 1)) * 100) }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-neutral-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p>Aucune donnée de vente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Commandes récentes et top catégories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Commandes récentes -->
        <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50 flex justify-between items-center">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">
                    <span class="w-2 h-2 bg-gold inline-block rounded-full mr-2"></span>
                    Commandes récentes
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-gold hover:text-gold-dark transition">Voir tout →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-neutral-50 border-b border-neutral-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">N° commande</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recent_orders as $order)
                        <tr class="hover:bg-neutral-50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-gold hover:underline font-medium">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-neutral-600">{{ $order->customer_name }}</td>
                            <td class="px-4 py-3 font-medium text-neutral-800">{{ number_format($order->total_amount, 2) }} USD</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($order->status === 'pending') bg-amber-100 text-amber-700
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                                    @elseif($order->status === 'preparing') bg-purple-100 text-purple-700
                                    @elseif($order->status === 'ready') bg-indigo-100 text-indigo-700
                                    @elseif($order->status === 'delivered') bg-emerald-100 text-emerald-700
                                    @else bg-neutral-100 text-neutral-500 @endif">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-400">
                                Aucune commande récente
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Top catégories -->
        <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-700">
                    <span class="w-2 h-2 bg-gold inline-block rounded-full mr-2"></span>
                    Catégories les plus vendues
                </h3>
            </div>
            <div class="p-5">
                @if($top_categories->count() > 0)
                    <div class="space-y-3">
                        @foreach($top_categories as $category)
                        <div class="flex items-center justify-between p-3 bg-neutral-50 rounded-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gold/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">{{ $category->name }}</p>
                                    <p class="text-xs text-neutral-500">{{ number_format($category->total_sold) }} produits vendus</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-semibold text-gold">{{ number_format($category->total_sold) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-neutral-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path></svg>
                        <p>Aucune donnée de catégorie</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gold {
        background-color: #D4AF37;
    }
    .bg-gold\/20 {
        background-color: rgba(212, 175, 55, 0.2);
    }
    .text-gold {
        color: #D4AF37;
    }
    .hover\:text-gold-dark:hover {
        color: #AA7C11;
    }
    .border-gold {
        border-color: #D4AF37;
    }
</style>
@endpush

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
                label: 'Chiffre d\'affaires (USD)',
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
                        boxWidth: 8,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'CA: ' + context.raw.toLocaleString('fr-FR') + ' USD';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' $';
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection