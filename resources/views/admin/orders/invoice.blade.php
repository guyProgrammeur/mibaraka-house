@extends('admin.layouts.app')

@section('title', 'Facture - ' . $order->order_number)
@section('header', 'Facture')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <!-- En-tête facture -->
        <div class="flex justify-between items-start border-b pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">MIBARAKA HOUSE</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-map-marker-alt mr-1"></i> Kinshasa, RDC<br>
                    <i class="fas fa-phone mr-1"></i> +243 XXX XXX XXX<br>
                    <i class="fas fa-envelope mr-1"></i> contact@mibaraka-house.com
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-800">FACTURE</div>
                <div class="text-gray-600 mt-2">
                    N°: {{ $order->order_number }}<br>
                    Date: {{ $order->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
        
        <!-- Client et livraison -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Client</h3>
                <p class="text-gray-700">
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_phone }}
                    @if($order->customer_email)<br>{{ $order->customer_email }}@endif
                </p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Adresse de livraison</h3>
                <p class="text-gray-700">
                    {{ $order->delivery_address }}
                    @if($order->delivery_neighborhood)<br>{{ $order->delivery_neighborhood }}@endif
                    @if($order->delivery_city)<br>{{ $order->delivery_city }}@endif
                </p>
            </div>
        </div>
        
        <!-- Tableau des produits -->
        <table class="w-full mb-8">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Produit</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Prix unitaire</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Quantité</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $item->product_name }}</td>
                    <td class="px-4 py-3 text-sm text-center">{{ number_format($item->unit_price, 2) }} {{ $order->currency_code }}</td>
                    <td class="px-4 py-3 text-sm text-center">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($item->subtotal, 2) }} {{ $order->currency_code }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-medium">Sous-total</td>
                    <td class="px-4 py-3 text-right">{{ number_format($order->subtotal, 2) }} {{ $order->currency_code }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-medium">Frais de livraison</td>
                    <td class="px-4 py-3 text-right">{{ number_format($order->delivery_fee, 2) }} {{ $order->currency_code }}</td>
                </tr>
                <tr class="border-t-2">
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-lg">TOTAL</td>
                    <td class="px-4 py-3 text-right font-bold text-lg">{{ number_format($order->total_amount, 2) }} {{ $order->currency_code }}</td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Statut et paiement -->
        <div class="grid grid-cols-2 gap-8 pt-4 border-t">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Statut</h3>
                <span class="px-3 py-1 text-sm rounded-full 
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $order->status_label }}
                </span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Paiement</h3>
                <span class="px-3 py-1 text-sm rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                </span>
                @if($order->payment_method)
                <p class="text-sm text-gray-600 mt-1">Méthode: {{ $order->payment_method }}</p>
                @endif
            </div>
        </div>
        
        <!-- Notes -->
        @if($order->delivery_notes || $order->admin_notes)
        <div class="mt-6 pt-4 border-t">
            <h3 class="font-semibold text-gray-800 mb-2">Notes</h3>
            @if($order->delivery_notes)
            <p class="text-sm text-gray-600"><strong>Livraison:</strong> {{ $order->delivery_notes }}</p>
            @endif
            @if($order->admin_notes)
            <p class="text-sm text-gray-600"><strong>Administratif:</strong> {{ $order->admin_notes }}</p>
            @endif
        </div>
        @endif
        
        <!-- Pied de page -->
        <div class="mt-8 pt-4 border-t text-center text-sm text-gray-500">
            <p>Merci de votre confiance !</p>
            <p class="mt-1">Cette facture fait foi. En cas de question, contactez-nous au +243 XXX XXX XXX</p>
        </div>
    </div>
    
    <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
        <a href="{{ route('admin.orders.show', $order) }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
            Retour
        </a>
    </div>
</div>

@push('styles')
<style media="print">
    body * {
        visibility: hidden;
    }
    .bg-white, .bg-white * {
        visibility: visible;
    }
    .bg-white {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
    }
    .bg-gray-50, .bg-gray-50 * {
        visibility: hidden;
    }
    button, .bg-gray-50 {
        display: none !important;
    }
</style>
@endpush
@endsection