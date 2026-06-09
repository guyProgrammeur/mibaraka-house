@extends('client.layouts.app')

@section('title', 'Commande confirmée')

@section('content')
<div class="bg-white text-neutral-900 min-h-screen">
    
    <!-- ================= ANNONCES TOP ================= -->
    @if(isset($announcementsByPosition['top']) && $announcementsByPosition['top']->isNotEmpty())
        <div class="bg-black text-white text-center py-3 text-xs tracking-[0.2em] uppercase border-b border-gold">
            <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-center gap-4 md:gap-8">
                @foreach($announcementsByPosition['top'] as $announcement)
                    <span class="font-light">{{ $announcement->content ?? $announcement->message }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        
        <!-- ================= FIL D'ARIANE ================= -->
        <nav class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-8 text-center">
            <a href="{{ route('client.catalog') }}" class="hover:text-black transition-colors">Accueil</a>
            <span class="mx-2 text-gold">✦</span>
            <a href="{{ route('client.cart.index') }}" class="hover:text-black transition-colors">Panier</a>
            <span class="mx-2 text-gold">✦</span>
            <a href="{{ route('client.checkout.index') }}" class="hover:text-black transition-colors">Commande</a>
            <span class="mx-2 text-gold">✦</span>
            <span class="text-gold font-medium">Confirmation</span>
        </nav>

        <!-- ================= SUCCÈS ================= -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gold/10 flex items-center justify-center">
                <svg class="w-10 h-10 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-serif font-light tracking-wide text-neutral-900 mb-2">
                Commande confirmée !
            </h1>
            <p class="text-neutral-500 text-sm">
                Merci pour votre confiance. Votre commande a bien été enregistrée.
            </p>
            <div class="mt-3 inline-block border border-gold/30 bg-gold/5 px-4 py-1.5">
                <span class="text-[10px] uppercase tracking-wider text-neutral-600">N° commande</span>
                <span class="ml-2 text-sm font-mono font-semibold text-gold">{{ $order->order_number }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- ================= RÉCAPITULATIF COMMANDE ================= -->
            <div class="border border-neutral-100 bg-white">
                <div class="p-5 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Récapitulatif
                    </h2>
                </div>
                
                <div class="p-5 space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600">{{ $item->quantity }}x {{ $item->product_name }}</span>
                        <span class="text-neutral-900 font-medium">{{ $item->formatted_subtotal }}</span>
                    </div>
                    @endforeach
                </div>
                
                <div class="p-5 border-t border-neutral-100 bg-neutral-50/30">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Sous-total</span>
                            <span class="text-neutral-700">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-500">Livraison</span>
                            <span class="text-neutral-700">{{ $order->formatted_delivery_fee }}</span>
                        </div>
                        <div class="border-t border-neutral-200 pt-2 mt-2">
                            <div class="flex justify-between font-semibold">
                                <span class="text-neutral-900">Total</span>
                                <span class="text-gold text-lg">{{ $order->formatted_total }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= LIVRAISON ================= -->
            <div class="border border-neutral-100 bg-white">
                <div class="p-5 border-b border-neutral-100 bg-neutral-50/50">
                    <h2 class="text-xs uppercase tracking-[0.2em] font-semibold text-neutral-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2 2 2 2-2 2 2 2-2 2 2 2-2 2 2 .5-1.5m-6 0h8m-11 0h-4"></path>
                        </svg>
                        Informations de livraison
                    </h2>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div>
                        <p class="text-neutral-900 font-medium">{{ $order->customer_name }}</p>
                        <p class="text-neutral-500 text-xs mt-0.5">{{ $order->customer_phone }}</p>
                    </div>
                    <div class="pt-2 border-t border-neutral-100">
                        <p class="text-neutral-700">{{ $order->delivery_address }}</p>
                        @if($order->delivery_neighborhood)
                            <p class="text-neutral-500 text-xs mt-1">Quartier: {{ $order->delivery_neighborhood }}</p>
                        @endif
                        @if($order->delivery_city)
                            <p class="text-neutral-500 text-xs">Ville: {{ $order->delivery_city }}</p>
                        @endif
                        @if($order->delivery_notes)
                            <div class="mt-3 pt-2 border-t border-neutral-100">
                                <p class="text-[10px] uppercase tracking-wider text-neutral-400 mb-1">Notes</p>
                                <p class="text-neutral-600 text-xs italic">"{{ $order->delivery_notes }}"</p>
                            </div>
                        @endif
                    </div>
                    @if($order->payment_method)
                    <div class="pt-2 border-t border-neutral-100">
                        <p class="text-[10px] uppercase tracking-wider text-neutral-400">Paiement</p>
                        <p class="text-neutral-700 text-sm capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= SUIVI COMMANDE ================= -->
        <div class="mt-8 border border-neutral-100 bg-neutral-50/40 p-6 text-center">
            <div class="flex items-center justify-center gap-2 mb-3">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-[10px] uppercase tracking-wider text-neutral-500">Prochaines étapes</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <div class="w-8 h-8 mx-auto rounded-full bg-gold/10 flex items-center justify-center mb-2">
                        <span class="text-gold font-bold text-sm">1</span>
                    </div>
                    <p class="text-neutral-700 font-medium">Confirmation</p>
                    <p class="text-neutral-400 text-[10px]">Vous recevrez un message WhatsApp</p>
                </div>
                <div>
                    <div class="w-8 h-8 mx-auto rounded-full bg-gold/10 flex items-center justify-center mb-2">
                        <span class="text-gold font-bold text-sm">2</span>
                    </div>
                    <p class="text-neutral-700 font-medium">Préparation</p>
                    <p class="text-neutral-400 text-[10px]">Notre équipe prépare votre commande</p>
                </div>
                <div>
                    <div class="w-8 h-8 mx-auto rounded-full bg-gold/10 flex items-center justify-center mb-2">
                        <span class="text-gold font-bold text-sm">3</span>
                    </div>
                    <p class="text-neutral-700 font-medium">Livraison</p>
                    <p class="text-neutral-400 text-[10px]">Sous 24-48h à Kinshasa</p>
                </div>
            </div>
        </div>

        <!-- ================= BOUTONS ACTION ================= -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ $whatsappLink }}" target="_blank" 
               class="inline-flex items-center justify-center gap-2 border border-green-600 text-green-600 hover:bg-green-600 hover:text-white px-6 py-3 text-xs uppercase tracking-[0.2em] font-semibold transition-all duration-300">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.032 2.002c-5.514 0-9.998 4.48-9.998 9.99 0 1.76.457 3.486 1.33 5.01l-1.43 4.87 4.98-1.4c1.47.8 3.12 1.23 4.81 1.23 5.51 0 9.99-4.48 9.99-9.99 0-5.51-4.48-9.99-9.99-9.99z"/>
                </svg>
                Suivre sur WhatsApp
            </a>
            <a href="{{ route('client.catalog') }}" 
               class="inline-flex items-center justify-center gap-2 border border-neutral-300 text-neutral-700 hover:bg-neutral-900 hover:text-white hover:border-neutral-900 px-6 py-3 text-xs uppercase tracking-[0.2em] font-semibold transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Continuer mes achats
            </a>
        </div>

        <!-- ================= MESSAGE FINAL ================= -->
        <div class="mt-8 text-center">
            <p class="text-[11px] text-neutral-400">
                Un message de confirmation vous a été envoyé par WhatsApp.<br>
                Notre équipe vous contactera pour organiser la livraison.
            </p>
        </div>
    </div>

    <!-- ================= ANNONCES BOTTOM ================= -->
    @if(isset($announcementsByPosition['bottom']) && $announcementsByPosition['bottom']->isNotEmpty())
        <div class="bg-neutral-50 py-8 text-center border-t border-neutral-100 mt-12">
            <div class="max-w-7xl mx-auto px-4">
                @foreach($announcementsByPosition['bottom'] as $announcement)
                    <p class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 font-light">
                        {{ $announcement->content ?? $announcement->message }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection