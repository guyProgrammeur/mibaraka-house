@extends('admin.layouts.app')

@section('title', 'Top clients')
@section('header', 'Top 10 des meilleurs clients')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-sm text-gray-600">
            Classement basé sur le montant total dépensé (commandes livrées uniquement)
        </p>
    </div>
    
    <div class="divide-y divide-gray-200">
        @foreach($customers as $index => $customer)
        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
            <div class="flex items-center gap-4">
                <div class="w-10 text-center">
                    @if($index == 0)
                        <i class="fas fa-trophy text-yellow-500 text-2xl"></i>
                    @elseif($index == 1)
                        <i class="fas fa-medal text-gray-400 text-2xl"></i>
                    @elseif($index == 2)
                        <i class="fas fa-medal text-amber-600 text-2xl"></i>
                    @else
                        <span class="text-xl font-bold text-gray-400">{{ $index + 1 }}</span>
                    @endif
                </div>
                
                <div>
                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                    <div class="text-sm text-gray-500">{{ $customer->formatted_phone }}</div>
                </div>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $customer->total_orders }}</div>
                    <div class="text-xs text-gray-500">commandes</div>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($customer->total_spent, 2) }} $</div>
                    <div class="text-xs text-gray-500">dépensés</div>
                </div>
                
                <div class="text-right">
                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye mr-1"></i>Voir
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if($customers->isEmpty())
    <div class="px-6 py-12 text-center text-gray-500">
        <i class="fas fa-chart-line text-4xl mb-2 block"></i>
        Aucune donnée disponible
    </div>
    @endif
</div>
@endsection