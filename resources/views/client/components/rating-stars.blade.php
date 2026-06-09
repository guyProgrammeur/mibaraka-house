@php
    $rating = $rating ?? 0;
    $size = $size ?? 'sm';
    $sizes = ['sm' => 'text-sm', 'md' => 'text-base', 'lg' => 'text-xl'];
@endphp

<div class="flex {{ $sizes[$size] }}">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= floor($rating))
            <i class="fas fa-star text-yellow-400"></i>
        @elseif ($i - 0.5 <= $rating)
            <i class="fas fa-star-half-alt text-yellow-400"></i>
        @else
            <i class="far fa-star text-gray-300"></i>
        @endif
    @endfor
</div>