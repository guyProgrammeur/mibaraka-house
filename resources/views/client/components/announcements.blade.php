@props(['announcement', 'position'])

<div class="announcement announcement-{{ $announcement->type }} announcement-position-{{ $position }} mb-4">
    @switch($announcement->type)
        @case('text')
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    @if($announcement->icon)
                        <i class="{{ $announcement->icon }} text-blue-500 mr-3 text-xl mt-0.5"></i>
                    @endif
                    <div class="flex-1">
                        @if($announcement->badge)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mb-1">{{ $announcement->badge }}</span>
                        @endif
                        <h3 class="font-semibold text-gray-800">{{ $announcement->title }}</h3>
                        @if($announcement->message)
                            <p class="text-gray-600 text-sm mt-1">{{ $announcement->message }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @break

        @case('button')
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 text-white shadow-md">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex-1">
                        @if($announcement->badge)
                            <span class="inline-block bg-white/20 text-white text-xs px-2 py-1 rounded mb-1">{{ $announcement->badge }}</span>
                        @endif
                        <h3 class="font-bold text-lg">{{ $announcement->title }}</h3>
                        @if($announcement->message)
                            <p class="text-white/90 text-sm mt-1">{{ $announcement->message }}</p>
                        @endif
                    </div>
                    @if($announcement->button_text && $announcement->button_link)
                        <a href="{{ $announcement->button_link }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition shadow-md whitespace-nowrap">
                            {{ $announcement->button_text }}
                        </a>
                    @endif
                </div>
            </div>
            @break

        @case('image')
            @if($announcement->image_url)
                <a href="{{ $announcement->button_link ?: '#' }}" class="block transition-transform hover:scale-[1.01] duration-300">
                    <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full rounded-lg shadow-md">
                </a>
            @endif
            @break

        @case('image_text')
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    @if($announcement->image_url)
                        <div class="h-48 md:h-full">
                            <img src="{{ $announcement->image_url }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-6 flex flex-col justify-center">
                        @if($announcement->badge)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mb-2 w-fit">{{ $announcement->badge }}</span>
                        @endif
                        <h3 class="font-bold text-xl text-gray-800">{{ $announcement->title }}</h3>
                        @if($announcement->message)
                            <p class="text-gray-600 mt-2 leading-relaxed">{{ $announcement->message }}</p>
                        @endif
                        @if($announcement->button_text && $announcement->button_link)
                            <a href="{{ $announcement->button_link }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-fit">
                                {{ $announcement->button_text }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @break

        @case('banner')
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-6 text-white text-center shadow-lg">
                @if($announcement->icon)
                    <i class="{{ $announcement->icon }} text-3xl mb-2 inline-block"></i>
                @endif
                @if($announcement->badge)
                    <span class="inline-block bg-white/20 text-white text-xs px-2 py-1 rounded mb-2">{{ $announcement->badge }}</span>
                @endif
                <h3 class="font-bold text-2xl">{{ $announcement->title }}</h3>
                @if($announcement->message)
                    <p class="mt-2 text-white/90 max-w-md mx-auto">{{ $announcement->message }}</p>
                @endif
                @if($announcement->button_text && $announcement->button_link)
                    <a href="{{ $announcement->button_link }}" class="inline-block mt-4 bg-white text-purple-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition shadow-md">
                        {{ $announcement->button_text }}
                    </a>
                @endif
            </div>
            @break
    @endswitch
</div>