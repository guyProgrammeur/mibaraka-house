@extends('admin.layouts.app')

@section('title', $qrCode->name . ' - QR Code')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.qr-codes.index') }}" class="text-neutral-500 hover:text-neutral-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-neutral-400">/</span>
                <span class="text-neutral-900 font-medium">{{ $qrCode->name }}</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-neutral-900">{{ $qrCode->name }}</h1>
            <p class="text-sm text-neutral-500 mt-1">Code: {{ $qrCode->code }}</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.qr-codes.preview', $qrCode) }}" target="_blank"
               class="bg-gold text-black px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold-dark transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Aperçu Poster
            </a>
            <a href="{{ route('admin.qr-codes.download', $qrCode) }}" 
               class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Télécharger PDF
            </a>
            <a href="{{ route('admin.qr-codes.download-qr', $qrCode) }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gold hover:text-black hover:border-gold transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                QR Code seul
            </a>
            <a href="{{ route('admin.qr-codes.edit', $qrCode) }}" 
               class="border border-neutral-300 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : QR Code + Actions -->
        <div class="lg:col-span-1">
            <!-- Carte QR Code -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden sticky top-6">
                <div class="p-6 border-b border-neutral-100">
                    <h2 class="font-semibold text-neutral-900">Aperçu du QR Code</h2>
                    <p class="text-xs text-neutral-500 mt-1">Scannez avec votre smartphone</p>
                </div>
                
                <div class="p-6 flex flex-col items-center">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-neutral-100">
                        <img src="{{ $qrCodeDataUrl }}" alt="QR Code" class="w-48 h-48 md:w-64 md:h-64">
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-2 justify-center">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-neutral-100 rounded text-xs text-neutral-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                            Taille: {{ ucfirst($qrCode->size) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-neutral-100 rounded text-xs text-neutral-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                            </svg>
                            Template: {{ ucfirst($qrCode->poster_template ?? 'classic') }}
                        </span>
                    </div>
                </div>
                
                <!-- Informations du QR Code -->
                <div class="bg-neutral-50 p-4 border-t border-neutral-100">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Statut</span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full {{ $qrCode->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                <span class="font-medium">{{ $qrCode->is_active ? 'Actif' : 'Inactif' }}</span>
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Type</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $qrCode->type === 'catalog' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $qrCode->type === 'category' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $qrCode->type === 'product' ? 'bg-green-100 text-green-700' : '' }}">
                                {{ ucfirst($qrCode->type) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Code unique</span>
                            <span class="font-mono text-xs text-neutral-900">{{ $qrCode->code }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Nombre de scans</span>
                            <span class="font-bold text-gold">{{ number_format($qrCode->scan_count) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Créé le</span>
                            <span>{{ $qrCode->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if($qrCode->last_scanned_at)
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Dernier scan</span>
                            <span>{{ $qrCode->last_scanned_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Détails -->
        <div class="lg:col-span-2">
            <!-- Informations générales -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-neutral-100">
                    <h2 class="font-semibold text-neutral-900">Informations générales</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Nom</label>
                        <p class="text-neutral-900">{{ $qrCode->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Destination</label>
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-neutral-700 break-all">{{ $qrCode->destination_url }}</p>
                            <button onclick="copyToClipboard('{{ $qrCode->destination_url }}')" 
                                    class="p-1 text-neutral-400 hover:text-gold transition-colors" title="Copier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    @if($qrCode->description)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Description</label>
                        <p class="text-neutral-600">{{ $qrCode->description }}</p>
                    </div>
                    @endif
                    
                    @if($qrCode->custom_message)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Message personnalisé</label>
                        <p class="text-neutral-600 italic">"{{ $qrCode->custom_message }}"</p>
                    </div>
                    @endif
                    
                    @if($qrCode->type === 'category' && $qrCode->category)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Catégorie associée</label>
                        <div class="flex items-center gap-2">
                            <span class="text-neutral-900">{{ $qrCode->category->name }}</span>
                            <a href="{{ route('admin.categories.edit', $qrCode->category) }}" class="text-xs text-gold hover:underline">Voir</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($qrCode->type === 'product' && $qrCode->product)
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Produit associé</label>
                        <div class="flex items-center gap-2">
                            <span class="text-neutral-900">{{ $qrCode->product->name }}</span>
                            <a href="{{ route('admin.products.edit', $qrCode->product) }}" class="text-xs text-gold hover:underline">Voir</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Options d'affichage du poster -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-neutral-100">
                    <h2 class="font-semibold text-neutral-900">Options d'affichage du poster</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Template</label>
                            <p class="text-neutral-900 capitalize">{{ $qrCode->poster_template ?? 'classic' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Couleur principale</label>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full border" style="background-color: {{ $qrCode->poster_primary_color ?? '#D4AF37' }}"></div>
                                <span class="text-neutral-900">{{ $qrCode->poster_primary_color ?? '#D4AF37' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Couleur de fond</label>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full border" style="background-color: {{ $qrCode->poster_background_color ?? '#FFFFFF' }}"></div>
                                <span class="text-neutral-900">{{ $qrCode->poster_background_color ?? '#FFFFFF' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Marque affichée</label>
                            <p class="text-neutral-900">{{ $qrCode->show_brand_name ? 'Oui' : 'Non' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques des scans -->
            <div class="bg-white rounded-lg border border-neutral-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100">
                    <h2 class="font-semibold text-neutral-900">Statistiques des scans</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-4 bg-neutral-50 rounded-lg">
                            <div class="text-3xl font-bold text-gold">{{ number_format($qrCode->scan_count) }}</div>
                            <div class="text-xs text-neutral-500 mt-1">Scans totaux</div>
                        </div>
                        <div class="text-center p-4 bg-neutral-50 rounded-lg">
                            <div class="text-3xl font-bold text-gold">
                                {{ $qrCode->last_scanned_at ? $qrCode->last_scanned_at->diffForHumans() : 'Jamais' }}
                            </div>
                            <div class="text-xs text-neutral-500 mt-1">Dernière activité</div>
                        </div>
                    </div>
                    
                    <!-- QR Code Usage Tips -->
                    <div class="bg-neutral-50 rounded-lg p-4">
                        <h3 class="font-medium text-sm text-neutral-900 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Conseils d'utilisation
                        </h3>
                        <ul class="space-y-2 text-sm text-neutral-600">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Téléchargez le PDF pour une impression professionnelle</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Testez le QR code avec votre smartphone avant impression</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Utilisez une taille suffisante (minimum 5cm x 5cm pour une lecture facile)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Placez le poster à un endroit visible et bien éclairé</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
            }, 2000);
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Style pour l'aperçu du QR code */
    img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush
@endsection