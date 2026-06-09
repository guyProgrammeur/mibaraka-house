@extends('admin.layouts.app')

@section('title', 'Gestion des commandes')
@section('header', 'Commandes')

@section('breadcrumb')
    <div class="text-sm text-neutral-500">
        <span class="text-gold">Commandes</span> / Liste
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white border border-neutral-200 rounded-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Total commandes</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-2">{{ number_format(array_sum($statusCounts)) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-neutral-200 rounded-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">En attente</p>
                    <p class="text-2xl font-bold text-amber-600 mt-2">{{ number_format($statusCounts['pending']) }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-neutral-200 rounded-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-2">{{ number_format($totalRevenue, 0, ',', ' ') }} <span class="text-sm">USD</span></p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-neutral-200 rounded-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">CA aujourd'hui</p>
                    <p class="text-2xl font-bold text-purple-600 mt-2">{{ number_format($todayRevenue, 0, ',', ' ') }} <span class="text-sm">USD</span></p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions et filtres -->
    <div class="bg-white border border-neutral-200 rounded-sm">
        <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50 flex flex-wrap justify-between items-center gap-4">
            <div class="flex gap-3">
                <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-black px-4 py-2 text-xs uppercase tracking-wider font-semibold transition-all rounded-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nouvelle commande
                </a>
                <a href="{{ route('admin.orders.export', request()->query()) }}" class="inline-flex items-center gap-2 bg-neutral-800 hover:bg-neutral-900 text-white px-4 py-2 text-xs uppercase tracking-wider font-semibold transition-all rounded-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exporter CSV
                </a>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="N° commande, client, téléphone..." class="border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                
                <select name="status" class="border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                    <option value="">Tous statuts</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente ({{ $statusCounts['pending'] }})</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmées ({{ $statusCounts['confirmed'] }})</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En préparation ({{ $statusCounts['preparing'] }})</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Prêtes ({{ $statusCounts['ready'] }})</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrées ({{ $statusCounts['delivered'] }})</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulées ({{ $statusCounts['cancelled'] }})</option>
                </select>
                
                <select name="payment_status" class="border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold transition">
                    <option value="">Tous paiements</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payé</option>
                </select>
                
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-neutral-200 rounded-sm px-3 py-2 text-sm">
                
                <button type="submit" class="bg-neutral-800 hover:bg-black text-white px-4 py-2 text-xs uppercase tracking-wider font-medium transition-all rounded-sm flex items-center justify-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Filtrer
                </button>
            </form>
        </div>
    </div>
    
    <!-- Badges statuts rapides -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.index') }}" class="px-3 py-1 text-xs rounded-full {{ !request('status') ? 'bg-neutral-800 text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }} transition">Tous</a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-700 hover:bg-amber-200' }} transition">En attente</a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'confirmed' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }} transition">Confirmées</a>
        <a href="{{ route('admin.orders.index', ['status' => 'preparing']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'preparing' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-700 hover:bg-purple-200' }} transition">En préparation</a>
        <a href="{{ route('admin.orders.index', ['status' => 'ready']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'ready' ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} transition">Prêtes</a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'delivered' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} transition">Livrées</a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="px-3 py-1 text-xs rounded-full {{ request('status') == 'cancelled' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }} transition">Annulées</a>
    </div>
    
    <!-- Tableau des commandes -->
    <div class="bg-white border border-neutral-200 rounded-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">N° commande</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Paiement</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-neutral-50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-gold hover:underline font-medium">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-neutral-800">{{ $order->customer_name }}</div>
                            <div class="text-xs text-neutral-400">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-neutral-600">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                            @if($order->isRecent())
                            <span class="ml-1 text-xs text-emerald-600 font-medium">Nouvelle</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-neutral-800">
                            {{ number_format($order->total_amount, 2) }} {{ $order->currency_code }}
                        </td>
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
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="p-1.5 text-neutral-500 hover:text-gold transition" title="Voir détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="p-1.5 text-neutral-500 hover:text-gold transition" title="Facture">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                                @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                <a href="{{ route('admin.orders.whatsapp-merchant', $order) }}" class="p-1.5 text-neutral-500 hover:text-emerald-600 transition" title="WhatsApp">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-neutral-400">
                            <svg class="w-12 h-12 mx-auto text-neutral-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>Aucune commande trouvée</p>
                            <a href="{{ route('admin.orders.create') }}" class="inline-block mt-3 text-gold hover:underline text-sm">Créer une commande</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-neutral-200 bg-neutral-50">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection