@extends('admin.layouts.app')

@section('title', 'Clients inactifs')
@section('header', 'Clients inactifs (plus de 6 mois)')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center gap-3 text-blue-600 bg-blue-50 p-3 rounded-lg mb-4">
            <i class="fas fa-info-circle text-xl"></i>
            <div class="text-sm">
                Clients n'ayant pas commandé depuis plus de 6 mois. Vous pouvez leur envoyer une offre de réactivation.
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total commandes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                        <div class="text-xs text-gray-500">Inscrit le {{ $customer->customer_since }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div><i class="fas fa-phone text-gray-400 mr-1"></i> {{ $customer->formatted_phone }}</div>
                        @if($customer->email)
                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($customer->last_order_at)
                            <span class="text-red-600">{{ $customer->last_order_at->diffForHumans() }}</span>
                            <div class="text-xs text-gray-500">{{ $customer->last_order_at->format('d/m/Y') }}</div>
                        @else
                            <span class="text-gray-400">Jamais commandé</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $customer->total_orders }} commandes</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ $customer->whatsapp_link }}" target="_blank" class="text-green-600 hover:text-green-800">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-user-check text-4xl mb-2 block"></i>
                        Aucun client inactif ! Tous vos clients sont actifs.
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
@endsection