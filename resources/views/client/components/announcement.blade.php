@props(['announcement', 'position'])

@switch($announcement->type)
    
    {{-- Type: Texte simple --}}
    @case('text')
        <div class="bg-neutral-50 border-l-4 border-gold p-4 rounded-lg">
            <div class="flex items-start gap-3">
                @if($announcement->icon)
                    <i class="{{ $announcement->icon }} text-gold text-xl"></i>
                @endif
                <div class="flex-1">
                    @if($announcement->badge)
                        <span class="inline-block bg-gold/20 text-gold-dark text-[10px] px-2 py-0.5 rounded mb-1">
                            {{ $announcement->badge }}
                        </span>
                    @endif
                    <h3 class="font-semibold text-neutral-900">{{ $announcement->title }}</h3>
                    @if($announcement->message)
                        <p class="text-neutral-600 text-sm mt-1">{{ $announcement->message }}</p>
                    @endif
                </div>
            </div>
        </div>
        @break
    
    {{-- Type: Texte + Bouton --}}
    @case('button')
        <div class="bg-gradient-to-r from-neutral-800 to-neutral-900 rounded-lg p-5 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex-1">
                    @if($announcement->badge)
                        <span class="inline-block bg-gold/20 text-gold text-[10px] px-2 py-0.5 rounded mb-2">
                            {{ $announcement->badge }}
                        </span>
                    @endif
                    <h3 class="font-semibold text-lg">{{ $announcement->title }}</h3>
                    @if($announcement->message)
                        <p class="text-neutral-300 text-sm mt-1">{{ $announcement->message }}</p>
                    @endif
                </div>
                @if($announcement->button_text && $announcement->button_link)
                    <a href="{{ $announcement->button_link }}" 
                       class="flex-shrink-0 bg-gold text-black px-5 py-2 rounded-lg font-medium text-sm hover:bg-gold-dark transition-colors">
                        {{ $announcement->button_text }}
                    </a>
                @endif
            </div>
        </div>
        @break
    
    {{-- Type: Image seule --}}
    @case('image')
        @if($announcement->image_url && $announcement->image_url != '')
            <div class="rounded-lg overflow-hidden">
                @if($announcement->button_link)
                    <a href="{{ $announcement->button_link }}">
                        <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full h-auto">
                    </a>
                @else
                    <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full h-auto">
                @endif
            </div>
        @else
            {{-- Fallback si pas d'image --}}
            <div class="bg-neutral-100 border border-neutral-200 rounded-lg p-6 text-center">
                <i class="fas fa-image text-neutral-400 text-3xl mb-2"></i>
                <p class="text-neutral-500 text-sm">{{ $announcement->title ?? 'Annonce' }}</p>
            </div>
        @endif
        @break
    
    {{-- Type: Image + Texte + Bouton --}}
    @case('image_text')
        <div class="bg-white rounded-lg overflow-hidden border border-neutral-100 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2">
                @if($announcement->image_url && $announcement->image_url != '')
                    <div class="h-48 md:h-full">
                        <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="p-6 flex flex-col justify-center">
                    @if($announcement->badge)
                        <span class="inline-block bg-gold/20 text-gold-dark text-[10px] px-2 py-0.5 rounded w-fit mb-2">
                            {{ $announcement->badge }}
                        </span>
                    @endif
                    <h3 class="font-semibold text-xl text-neutral-900">{{ $announcement->title }}</h3>
                    @if($announcement->message)
                        <p class="text-neutral-600 mt-2">{{ $announcement->message }}</p>
                    @endif
                    @if($announcement->button_text && $announcement->button_link)
                        <a href="{{ $announcement->button_link }}" 
                           class="inline-block mt-4 bg-neutral-900 text-white px-5 py-2 rounded-lg font-medium text-sm hover:bg-gold hover:text-black transition-colors w-fit">
                            {{ $announcement->button_text }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @break
    
    {{-- Type: Bannière pleine largeur --}}
    @case('banner')
        <div class="bg-gradient-to-r from-gold/10 via-gold/5 to-gold/10 border-y border-gold/30 py-8 text-center">
            <div class="max-w-3xl mx-auto px-4">
                @if($announcement->icon)
                    <i class="{{ $announcement->icon }} text-gold text-3xl mb-3 inline-block"></i>
                @endif
                @if($announcement->badge)
                    <span class="inline-block bg-gold/20 text-gold-dark text-[10px] px-2 py-0.5 rounded mb-2">
                        {{ $announcement->badge }}
                    </span>
                @endif
                <h3 class="font-serif text-2xl md:text-3xl font-light text-neutral-900">{{ $announcement->title }}</h3>
                @if($announcement->message)
                    <p class="text-neutral-500 mt-2">{{ $announcement->message }}</p>
                @endif
                @if($announcement->button_text && $announcement->button_link)
                    <a href="{{ $announcement->button_link }}" 
                       class="inline-block mt-5 border border-neutral-900 text-neutral-900 px-6 py-2 rounded-lg font-medium text-sm hover:bg-neutral-900 hover:text-white transition-colors">
                        {{ $announcement->button_text }}
                    </a>
                @endif
            </div>
        </div>
        @break
    
    {{-- Type par défaut --}}
    @default
        <div class="bg-neutral-50 border-l-4 border-gold p-4 rounded-lg">
            <div class="flex items-start gap-3">
                @if($announcement->icon)
                    <i class="{{ $announcement->icon }} text-gold text-xl"></i>
                @endif
                <div class="flex-1">
                    <h3 class="font-semibold text-neutral-900">{{ $announcement->title }}</h3>
                    @if($announcement->message)
                        <p class="text-neutral-600 text-sm mt-1">{{ $announcement->message }}</p>
                    @endif
                </div>
            </div>
        </div>
        @break

@endswitch