@extends('admin.layouts.app')

@section('title', 'Commande - ' . $order->order_number)
@section('header', 'Détails de la commande')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne gauche : Informations commande -->
    <div class="lg:col-span-2 space-y-6">
        <!-- En-tête commande -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
                <div>
                    <h2 class="text-xl font-semibold">{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-500">Créée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="openStatusModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-exchange-alt mr-2"></i>Changer statut
                    </button>
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-print mr-2"></i>Facture
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border">
                            <a href="{{ route('admin.orders.whatsapp-merchant', $order) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                <i class="fab fa-whatsapp text-green-600 mr-2"></i>Envoyer au commerçant
                            </a>
                            <a href="{{ route('admin.orders.whatsapp-customer', $order) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                <i class="fab fa-whatsapp text-blue-600 mr-2"></i>Envoyer au client
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Statut</p>
                    <span class="px-2 py-1 text-sm rounded-full inline-block mt-1
                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                        @elseif($order->status === 'ready') bg-indigo-100 text-indigo-800
                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Paiement</p>
                    <span class="px-2 py-1 text-sm rounded-full inline-block mt-1 {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                    </span>
                    @if($order->payment_method)
                    <p class="text-xs text-gray-500 mt-1">{{ $order->payment_method }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-500">Devise</p>
                    <p class="text-sm font-medium mt-1">{{ $order->currency_code }} (1 = {{ number_format($order->exchange_rate, 2) }})</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">WhatsApp envoyé</p>
                    <p class="text-sm mt-1">
                        @if($order->whatsapp_sent)
                        <span class="text-green-600"><i class="fas fa-check-circle"></i> Oui le {{ $order->whatsapp_sent_at?->format('d/m/H:i') }}</span>
                        @else
                        <span class="text-gray-500">Non</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Produits commandés -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium">Produits commandés</h3>
                <button type="button" onclick="openAddProductModal()" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-plus mr-1"></i>Ajouter un produit
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sous-total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($item->product_image)
                                    <img src="{{ asset('storage/' . $item->product_image) }}" class="w-10 h-10 object-cover rounded" alt="">
                                    @else
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $item->product_name }}</div>
                                        @if($item->product && !$item->product->is_active)
                                        <span class="text-xs text-red-500">Produit désactivé</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ number_format($item->unit_price, 2) }} {{ $order->currency_code }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.orders.items.update', [$order, $item->id]) }}" method="POST" class="flex items-center justify-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-20 border rounded px-2 py-1 text-center text-sm">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                {{ number_format($item->subtotal, 2) }} {{ $order->currency_code }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.orders.items.remove', [$order, $item->id]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">Sous-total</td>
                            <td class="px-6 py-4 text-right font-medium">{{ $order->formatted_subtotal }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">
                                <form action="{{ route('admin.orders.delivery-fee', $order) }}" method="POST" class="flex items-center justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <span>Frais de livraison</span>
                                    <input type="number" name="delivery_fee" value="{{ $order->delivery_fee }}" step="0.01" class="w-24 border rounded px-2 py-1 text-right text-sm">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">{{ $order->formatted_delivery_fee }}</td>
                            <td></td>
                        </tr>
                        <tr class="border-t-2">
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-lg">TOTAL</td>
                            <td class="px-6 py-4 text-right font-bold text-lg">{{ $order->formatted_total }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Notes administratives -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-medium">Notes administratives</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.orders.notes', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <textarea name="admin_notes" rows="4" class="w-full border rounded-lg px-3 py-2">{{ $order->admin_notes }}</textarea>
                    <div class="mt-3 text-right">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Colonne droite : Client et livraison -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Informations client -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium">Client</h3>
                @if($order->customer)
                <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-blue-600 text-sm hover:underline">
                    Voir profil
                </a>
                @endif
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Nom</p>
                    <p class="font-medium">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Téléphone</p>
                    <a href="https://wa.me/{{ $order->customer_phone }}" target="_blank" class="text-green-600 hover:underline">
                        {{ $order->customer_phone }}
                    </a>
                </div>
                @if($order->customer_email)
                <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p>{{ $order->customer_email }}</p>
                </div>
                @endif
                @if($order->customer && $order->customer->total_orders > 0)
                <div class="pt-3 border-t">
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div>
                            <p class="text-xs text-gray-500">Commandes</p>
                            <p class="font-bold">{{ $order->customer->total_orders }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total dépensé</p>
                            <p class="font-bold">{{ number_format($order->customer->total_spent, 2) }} $</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Adresse de livraison -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-medium">Adresse de livraison</h3>
            </div>
            <div class="p-6">
                <p>{{ $order->delivery_address }}</p>
                @if($order->delivery_neighborhood)
                <p class="mt-1">Quartier: {{ $order->delivery_neighborhood }}</p>
                @endif
                @if($order->delivery_city)
                <p>Ville: {{ $order->delivery_city }}</p>
                @endif
                @if($order->delivery_notes)
                <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                    <p class="text-xs text-yellow-800 font-medium">Notes de livraison</p>
                    <p class="text-sm">{{ $order->delivery_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Paiement -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-medium">Paiement</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.orders.payment', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Statut</label>
                            <select name="payment_status" class="w-full border rounded-lg px-3 py-2">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Payé</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Méthode</label>
                            <select name="payment_method" class="w-full border rounded-lg px-3 py-2">
                                <option value="">-- Sélectionner --</option>
                                <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Espèces</option>
                                <option value="mobile_money" {{ $order->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="bank_transfer" {{ $order->payment_method == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-save mr-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Actions supplémentaires -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-medium">Actions</h3>
            </div>
            <div class="p-6 space-y-2">
                @if($order->can_be_cancelled)
                <button type="button" onclick="openCancelModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-times mr-2"></i>Annuler la commande
                </button>
                @endif
                <form action="{{ route('admin.orders.duplicate', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-copy mr-2"></i>Dupliquer la commande
                    </button>
                </form>
                @if(in_array($order->status, ['delivered', 'cancelled']))
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette commande ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-trash-alt mr-2"></i>Supprimer
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Changement de statut -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-medium">Changer le statut</h3>
            <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.orders.status', $order) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau statut</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>En préparation</option>
                        <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Prête</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note (optionnelle)</label>
                    <textarea name="note" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Raison du changement..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajout produit -->
<div id="addProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-medium">Ajouter un produit</h3>
            <button onclick="closeAddProductModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.orders.items.add', $order) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                    <select name="product_id" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- Sélectionner --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} - {{ number_format($product->price, 2) }} $
                            @if($product->track_stock)
                                (Stock: {{ $product->stock_quantity }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" name="quantity" value="1" min="1" required class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <button type="button" onclick="closeAddProductModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Annulation -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-red-600">Annuler la commande</h3>
        </div>
        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Êtes-vous sûr de vouloir annuler cette commande ?</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Raison (optionnelle)</label>
                    <textarea name="reason" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Raison de l'annulation..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Retour
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-check mr-2"></i>Confirmer l'annulation
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openStatusModal() { document.getElementById('statusModal').classList.remove('hidden'); document.getElementById('statusModal').classList.add('flex'); }
    function closeStatusModal() { document.getElementById('statusModal').classList.add('hidden'); document.getElementById('statusModal').classList.remove('flex'); }
    function openAddProductModal() { document.getElementById('addProductModal').classList.remove('hidden'); document.getElementById('addProductModal').classList.add('flex'); }
    function closeAddProductModal() { document.getElementById('addProductModal').classList.add('hidden'); document.getElementById('addProductModal').classList.remove('flex'); }
    function openCancelModal() { document.getElementById('cancelModal').classList.remove('hidden'); document.getElementById('cancelModal').classList.add('flex'); }
    function closeCancelModal() { document.getElementById('cancelModal').classList.add('hidden'); document.getElementById('cancelModal').classList.remove('flex'); }
</script>
@endpush
@endsection