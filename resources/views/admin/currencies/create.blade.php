@extends('admin.layouts.app')

@section('title', 'Nouvelle devise')

@section('content')
<div class="max-w-2xl mx-auto pb-24 md:pb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 px-4 md:px-0">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.currencies.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium text-sm md:text-base">Nouvelle devise</span>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-neutral-900">Créer une devise</h1>
            <p class="text-xs md:text-sm text-neutral-500 mt-1">Ajoutez une nouvelle devise pour la boutique</p>
        </div>
        
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('admin.currencies.index') }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
                Annuler
            </a>
            <button type="submit" form="create-currency-form" 
                    class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors shadow-sm">
                Créer la devise
            </button>
        </div>
    </div>

    <form id="create-currency-form" action="{{ route('admin.currencies.store') }}" method="POST" class="space-y-6 px-4 md:px-0">
        @csrf

        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Informations de la devise</h2>
                <p class="text-[11px] md:text-xs text-neutral-500 mt-0.5">Les informations de base de la devise</p>
            </div>
            <div class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                           class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm uppercase focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('code') border-red-500 @enderror"
                           placeholder="Ex: EUR, GBP, CDF" maxlength="3">
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Code ISO à 3 lettres (ex: USD, EUR, GBP, CDF)</p>
                    @error('code')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Symbole -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Symbole <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="symbol" value="{{ old('symbol') }}" required
                           class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('symbol') border-red-500 @enderror"
                           placeholder="Ex: €, £, FC, $">
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Symbole de la devise (ex: $, €, £, FC)</p>
                    @error('symbol')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Nom</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('name') border-red-500 @enderror"
                           placeholder="Ex: Euro, Livre Sterling, Franc Congolais">
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Nom complet de la devise (optionnel)</p>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Taux -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Taux de change <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-neutral-500">1 USD =</span>
                        <input type="number" name="rate" value="{{ old('rate', 1) }}" step="0.0001" min="0.0001" required
                               class="flex-1 border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/20 transition-colors @error('rate') border-red-500 @enderror"
                               placeholder="Taux de change">
                    </div>
                    <p class="text-[11px] md:text-xs text-neutral-400 mt-1">Taux de conversion par rapport au dollar américain (USD)</p>
                    @error('rate')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-sm md:text-base text-neutral-900">Options</h2>
            </div>
            <div class="p-4 md:p-6 space-y-4">
                <div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" class="sr-only peer" {{ old('is_default') ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-neutral-300 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        <span class="ml-3 text-sm font-medium text-neutral-700">Devise par défaut</span>
                    </label>
                    <p class="text-[11px] text-neutral-400 mt-1 ml-14">La devise par défaut sera utilisée pour l'affichage principal des prix</p>
                </div>
                
                <div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-neutral-300 rounded-full peer peer-checked:bg-gold peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        <span class="ml-3 text-sm font-medium text-neutral-700">Devise active</span>
                    </label>
                    <p class="text-[11px] text-neutral-400 mt-1 ml-14">Les devises inactives ne seront pas proposées aux clients</p>
                </div>
            </div>
        </div>
    </form>

    <!-- Boutons flottants pour mobile -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-neutral-200 px-4 py-3 flex items-center gap-3 z-50">
        <a href="{{ route('admin.currencies.index') }}" 
           class="flex-1 border border-neutral-300 text-neutral-700 h-11 flex items-center justify-center rounded-xl text-sm font-medium active:bg-neutral-100 transition-colors">
            Annuler
        </a>
        <button type="submit" form="create-currency-form" 
                class="flex-1 bg-neutral-900 text-white h-11 flex items-center justify-center rounded-xl text-sm font-medium active:bg-neutral-800 transition-colors shadow-sm">
            Créer la devise
        </button>
    </div>
</div>
@endsection