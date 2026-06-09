@foreach($products as $product)
    <div class="group relative bg-white border border-neutral-100 hover:border-neutral-900 transition-all duration-300 p-3 flex flex-col justify-between">
        <div class="relative w-full aspect-square bg-neutral-50 overflow-hidden mb-3">
            <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
            
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                @if($product->is_featured)
                    <span class="bg-black text-[#D4AF37] text-[10px] tracking-widest uppercase px-2 py-0.5 font-semibold">
                        Sélection
                    </span>
                @endif
                @if($product->created_at->gt(now()->subDays(7)))
                    <span class="bg-[#D4AF37] text-white text-[10px] tracking-widest uppercase px-2 py-0.5 font-semibold">
                        Nouveau
                    </span>
                @endif
            </div>
        </div>

        <div>
            <p class="text-[11px] text-neutral-400 uppercase tracking-wider mb-1">
                {{ $product->category->name ?? '' }}
            </p>
            <h3 class="text-xs font-medium text-neutral-900 line-clamp-2 hover:text-[#D4AF37] transition-colors mb-2">
                <a href="{{ route('client.catalog.product', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <div class="mt-auto pt-2 border-t border-neutral-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-neutral-900 font-serif">
                {{ number_format($product->price, 2) }} {{ $currency->symbol ?? $currency->code }}
            </span>
            
            @if($product->avg_rating > 0)
                <div class="flex items-center text-[11px] text-[#D4AF37]">
                    <span>★</span>
                    <span class="text-neutral-500 ml-0.5">{{ number_format($product->avg_rating, 1) }}</span>
                </div>
            @endif
        </div>
    </div>
@endforeach