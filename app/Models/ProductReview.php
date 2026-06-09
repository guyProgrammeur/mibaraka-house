<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $table = 'product_reviews';
    
    protected $fillable = [
        'user_id', 'customer_name', 'product_id', 'rating', 'comment', 'is_approved'
    ];
    
    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];
    
    protected $appends = ['rating_stars'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-yellow-400"></i>';
            } elseif ($i - 0.5 <= $this->rating) {
                $stars .= '<i class="fas fa-star-half-alt text-yellow-400"></i>';
            } else {
                $stars .= '<i class="far fa-star text-gray-300"></i>';
            }
        }
        return $stars;
    }
    
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
    
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}