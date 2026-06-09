@extends('admin.layouts.app')

@section('title', 'Modifier - ' . $customer->name)
@section('header', 'Modifier le client')

@section('content')
<div class="bg-white rounded-lg shadow max-w-2xl mx-auto">
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required 
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required 
                           class="w-full border rounded-lg px-3 py-2"
                           placeholder="0812345678 ou 243812345678">
                    <p class="text-xs text-gray-500 mt-1">Format: 0812345678 (sans +243)</p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" 
                       class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <input type="text" name="default_address" value="{{ old('default_address', $customer->default_address) }}" 
                       class="w-full border rounded-lg px-3 py-2"
                       placeholder="Numéro et rue">
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city) }}" 
                           class="w-full border rounded-lg px-3 py-2"
                           placeholder="Kinshasa, Lubumbashi...">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quartier</label>
                    <input type="text" name="neighborhood" value="{{ old('neighborhood', $customer->neighborhood) }}" 
                           class="w-full border rounded-lg px-3 py-2"
                           placeholder="Gombe, Limete...">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Devise préférée *</label>
                    <select name="preferred_currency" class="w-full border rounded-lg px-3 py-2">
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->code }}" {{ old('preferred_currency', $customer->preferred_currency) == $currency->code ? 'selected' : '' }}>
                            {{ $currency->code }} - {{ $currency->symbol }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center pt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm font-medium text-gray-700">Client actif</span>
                    </label>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
                <textarea name="notes" rows="4" class="w-full border rounded-lg px-3 py-2">{{ old('notes', $customer->notes) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Ces notes ne sont visibles que par l'administration.</p>
            </div>
            
            <!-- Statistiques (lecture seule) -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Statistiques (lecture seule)</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Total commandes</p>
                        <p class="text-xl font-semibold">{{ $customer->total_orders }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total dépensé</p>
                        <p class="text-xl font-semibold">{{ number_format($customer->total_spent, 2) }} $</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Client depuis</p>
                        <p class="text-xl font-semibold">{{ $customer->customer_since }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ces statistiques sont mises à jour automatiquement après chaque commande.
                </p>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-between items-center">
            <div>
                <a href="{{ route('admin.customers.toggle', $customer) }}" 
                   class="px-4 py-2 border rounded-lg text-{{ $customer->is_active ? 'yellow' : 'green' }}-700 hover:bg-gray-100">
                    <i class="fas fa-{{ $customer->is_active ? 'eye-slash' : 'eye' }} mr-2"></i>
                    {{ $customer->is_active ? 'Désactiver' : 'Activer' }}
                </a>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </form>
</div>
@endsection