@extends('admin.layouts.app')

@section('title', 'Aperçu des conversions')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.currencies.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium">Aperçu des conversions</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">Aperçu des conversions</h1>
            <p class="text-sm text-neutral-500 mt-1">Visualisez les conversions entre les devises</p>
        </div>
        <a href="{{ route('admin.currencies.index') }}" 
           class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-100 transition-colors">
            Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tableau des conversions -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Conversions USD → Devises</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Taux de change actuels</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Devise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Taux</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Exemple (10 USD)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($currencies as $currency)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ $currency->code }}</span>
                                    <span class="text-sm text-neutral-500">{{ $currency->symbol }}</span>
                                    @if($currency->is_default)
                                        <span class="text-[10px] bg-gold/20 text-gold-dark px-1.5 py-0.5 rounded">Défaut</span>
                                    @endif
                                </div>
                                <div class="text-xs text-neutral-400">{{ $currency->name }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono">1 USD = {{ number_format($currency->rate, 4) }} {{ $currency->code }}</td>
                            <td class="px-6 py-4 font-medium">{{ $currency->symbol }} {{ number_format($currency->convertFromUsd(10), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des montants tests -->
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                <h2 class="font-semibold text-neutral-900">Conversion de montants</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Depuis la devise par défaut ({{ $defaultCurrency->code }})</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Montant</th>
                            @foreach($currencies->take(4) as $currency)
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">{{ $currency->code }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($testAmounts as $amount)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 font-semibold">{{ $defaultCurrency->symbol }} {{ number_format($amount, 2) }}</td>
                            @foreach($currencies->take(4) as $currency)
                            <td class="px-6 py-4 font-mono">{{ $currency->symbol }} {{ number_format($currency->convertFromUsd($amount), 2) }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Graphique simple des taux -->
    <div class="mt-6 bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
            <h2 class="font-semibold text-neutral-900">Comparaison des taux</h2>
            <p class="text-xs text-neutral-500 mt-0.5">Valeur relative par rapport au dollar américain (USD)</p>
        </div>
        <div class="p-6">
            @foreach($currencies->where('code', '!=', 'USD')->take(6) as $currency)
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">{{ $currency->code }}</span>
                    <span class="text-neutral-500">{{ number_format($currency->rate, 4) }}</span>
                </div>
                <div class="w-full bg-neutral-100 rounded-full h-2">
                    <div class="bg-gold rounded-full h-2" style="width: {{ min(($currency->rate / 3000) * 100, 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection