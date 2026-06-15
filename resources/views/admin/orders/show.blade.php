@extends('admin.layouts.app')

@section('title', 'Commande ' . $order->order_number)
@section('header', 'Détails de la commande')

@section('breadcrumb')
    <div class="text-sm text-neutral-500">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-gold">Commandes</a> <span class="mx-1">/</span>
        <span class="text-gold">{{ $order->order_number }}</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Actions rapides -->
    <div class="bg-white border border-neutral-200 rounded-sm p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="inline-flex items-center gap-2 bg-neutral-800 hover:bg-neutral-900 text-white px-3 py-1.5 text-xs uppercase tracking-wider font-semibold transition rounded-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Facture PDF
            </a>
            <a href="{{ route('admin.orders.whatsapp-merchant', $order) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs uppercase tracking-wider font-semibold transition rounded-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                WhatsApp Commerçant
            </a>
            <a href="{{ route('admin.orders.whatsapp-customer', $order) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs uppercase tracking-wider font-semibold transition rounded-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                WhatsApp Client
            </a>
        </div>
        
        <div class="flex gap-2">
            @if($order->can_be_cancelled)
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Annuler cette commande ?')">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 text-xs uppercase tracking-wider font-semibold transition rounded-sm">
                    Annuler la commande
                </button>
            </form>
            @endif
            
            @if($order->payment_status === 'pending')
            <form action="{{ route('admin.orders.mark-paid', $order) }}" method="POST" class="inline" onsubmit="return confirm('Marquer cette commande comme payée ?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs uppercase tracking-wider font-semibold transition rounded-sm">
                    Marquer comme payée
                </button>
            </form>
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Infos commande et client -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statut actuel -->
            <div class="bg-white border border-neutral-200 rounded-sm">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Statut de la commande
                    </h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                                @if($order->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                                @elseif($order->status === 'preparing') bg-purple-100 text-purple-700
                                @elseif($order->status === 'ready') bg-indigo-100 text-indigo-700
                                @elseif($order->status === 'delivered') bg-emerald-100 text-emerald-700
                                @else bg-neutral-100 text-neutral-500 @endif">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <div class="text-sm text-neutral-500">
                            Créée le {{ $order->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    
                    <!-- Barre de progression des statuts -->
                    <div class="mt-6">
                        @php
                            $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered'];
                            $currentIndex = array_search($order->status, $statuses);
                        @endphp
                        <div class="flex items-center justify-between">
                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        @if($index <= $currentIndex) bg-gold text-black @else bg-neutral-200 text-neutral-500 @endif">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="text-xs mt-1 text-center {{ $index <= $currentIndex ? 'text-gold' : 'text-neutral-400' }}">
                                        {{ ucfirst($status) }}
                                    </div>
                                </div>
                                @if($index < count($statuses) - 1)
                                    <div class="flex-1 h-0.5 {{ $index < $currentIndex ? 'bg-gold' : 'bg-neutral-200' }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Formulaire changement statut -->
                    <div class="mt-6 pt-4 border-t border-neutral-100">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex gap-3">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="flex-1 border border-neutral-200 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-gold">
                                <option value="">Changer le statut</option>
                                @if($order->status === 'pending')
                                    <option value="confirmed">✓ Confirmer la commande</option>
                                    <option value="cancelled">✗ Annuler la commande</option>
                                @elseif($order->status === 'confirmed')
                                    <option value="preparing">Commencer la préparation</option>
                                    <option value="cancelled">Annuler la commande</option>
                                @elseif($order->status === 'preparing')
                                    <option value="ready">Marquer comme prête</option>
                                    <option value="cancelled">Annuler la commande</option>
                                @elseif($order->status === 'ready')
                                    <option value="delivered">Marquer comme livrée</option>
                                @endif
                            </select>
                            <button type="submit" class="bg-gold hover:bg-gold-dark text-black px-4 py-2 text-xs uppercase tracking-wider font-semibold transition rounded-sm">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Informations client -->
            <div class="bg-white border border-neutral-200 rounded-sm">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informations client
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-neutral-400">Nom complet</p>
                            <p class="font-medium">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">Téléphone</p>
                            <p class="font-medium">
                                {{ $order->customer_phone }}
                                <a href="https://wa.me/{{ $order->customer_phone }}" target="_blank" class="text-emerald-600 ml-2 hover:underline text-sm">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </a>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-neutral-400">Email</p>
                            <p class="font-medium">{{ $order->customer_email ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Adresse de livraison -->
            <div class="bg-white border border-neutral-200 rounded-sm">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Adresse de livraison
                    </h3>
                </div>
                <div class="p-5">
                    @if($order->delivery_address)
                        <p class="mb-2">{{ $order->delivery_address }}</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            @if($order->delivery_city)
                                <div><span class="text-neutral-500">Ville:</span> {{ $order->delivery_city }}</div>
                            @endif
                            @if($order->delivery_neighborhood)
                                <div><span class="text-neutral-500">Quartier:</span> {{ $order->delivery_neighborhood }}</div>
                            @endif
                        </div>
                        @if($order->delivery_notes)
                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-sm">
                                <p class="text-xs text-amber-700 font-semibold mb-1">📝 Notes de livraison</p>
                                <p class="text-sm">{{ $order->delivery_notes }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-neutral-400">Pas d'adresse de livraison renseignée</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Colonne droite : Produits et résumé -->
        <div class="space-y-6">
            <!-- Résumé de la commande -->
            <div class="bg-white border border-neutral-200 rounded-sm">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="font-semibold text-neutral-800">Résumé</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-600">N° commande</span>
                        <span class="font-mono font-semibold">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-neutral-100">
                        <span class="text-neutral-600">Sous-total</span>
                        <span>{{ number_format($order->subtotal, 2) }} {{ $order->currency_code }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-600">Frais de livraison</span>
                        <span>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 2) . ' ' . $order->currency_code : 'Gratuit' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-neutral-100 font-bold text-lg">
                        <span>Total</span>
                        <span class="text-gold">{{ number_format($order->total_amount, 2) }} {{ $order->currency_code }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Informations paiement -->
            <div class="bg-white border border-neutral-200 rounded-sm">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="font-semibold text-neutral-800">Paiement</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-neutral-600">Statut</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-600">Méthode</span>
                        <span>
                            @if($order->payment_method === 'cash') Espèces
                            @elseif($order->payment_method === 'mobile_money') Mobile Money
                            @elseif($order->payment_method === 'bank_transfer') Virement bancaire
                            @else Non spécifié @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-600">Devise</span>
                        <span>{{ $order->currency_code }}</span>
                    </div>
                </div>
            </div>
            
            <!-- WhatsApp Status -->
            <div class="bg-white border border-neutral-200 rounded-sm">
                <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
                    <h3 class="font-semibold text-neutral-800">Notifications WhatsApp</h3>
                </div>
                <div class="p-5">
                    @if($order->whatsapp_sent)
                        <div class="flex items-center gap-2 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Notification envoyée le {{ $order->whatsapp_sent_at?->format('d/m/Y à H:i') }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Aucune notification envoyée</span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.orders.whatsapp-merchant', $order) }}" class="text-sm text-gold hover:underline">
                                Envoyer la notification →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des produits -->
    <div class="bg-white border border-neutral-200 rounded-sm">
        <div class="px-5 py-4 border-b border-neutral-100 bg-neutral-50">
            <h3 class="font-semibold text-neutral-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Produits commandés
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 border-b border-neutral-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500">Produit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-neutral-500">Quantité</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-500">Prix unitaire</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-500">Sous-total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 2) }} {{ $order->currency_code }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->subtotal, 2) }} {{ $order->currency_code }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-neutral-50 border-t border-neutral-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-gold">{{ number_format($order->total_amount, 2) }} {{ $order->currency_code }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Notes admin -->
    @if($order->admin_notes)
    <div class="bg-amber-50 border border-amber-200 rounded-sm">
        <div class="px-5 py-4 border-b border-amber-200">
            <h3 class="font-semibold text-amber-800">📝 Notes internes</h3>
        </div>
        <div class="p-5">
            <p class="text-amber-800 whitespace-pre-line">{{ $order->admin_notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection