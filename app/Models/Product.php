<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'products';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image_path',
        'image_secondary',
        'category_id',
        'stock_quantity',
        'stock_alert_threshold',
        'track_stock',
        'is_featured',
        'is_active',
        'views',
        'unit',
        'weight'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'stock_alert_threshold' => 'integer',
        'track_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
        'weight' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $appends = [
        'formatted_price',
        'stock_status',
        'is_low_stock',
        'is_out_of_stock',
        'image_url'
    ];
    
    // ========== RELATIONS ==========
    
    /**
     * La catégorie du produit
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Les lignes de commande de ce produit
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // ========== SCOPES ==========
    
    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope pour les produits phares
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope pour les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('track_stock', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }
    
    /**
     * Scope pour les produits en rupture
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('track_stock', true)
                     ->where('stock_quantity', '<=', 0);
    }
    
    /**
     * Scope pour les produits avec stock bas
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
                     ->whereRaw('stock_quantity <= stock_alert_threshold')
                     ->where('stock_quantity', '>', 0);
    }
    
    /**
     * Scope pour les produits d'une catégorie spécifique
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    /**
     * Scope pour rechercher par nom
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
    
    /**
     * Scope pour filtrer par prix minimum/maximum
     */
    public function scopePriceRange($query, ?float $min, ?float $max)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }
    
    // ========== ACCESSORS & MUTATORS ==========
    
    /**
     * Mutator : générer le slug automatiquement
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
    
    /**
     * Accessor : prix formaté en USD
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$ ' . number_format($this->price, 2, ',', ' ');
    }
    
    /**
     * Accessor : statut du stock
     */
    public function getStockStatusAttribute(): string
    {
        if (!$this->track_stock) {
            return 'en_stock'; // Gestion non activée, considéré en stock
        }
        
        if ($this->stock_quantity <= 0) {
            return 'rupture';
        }
        
        if ($this->stock_quantity <= $this->stock_alert_threshold) {
            return 'stock_bas';
        }
        
        return 'en_stock';
    }
    
    /**
     * Accessor : libellé du statut stock
     */
    public function getStockStatusLabelAttribute(): string
    {
        return match($this->stock_status) {
            'rupture' => 'Rupture de stock',
            'stock_bas' => 'Stock bas',
            default => 'En stock'
        };
    }
    
    /**
     * Accessor : couleur du statut stock (pour CSS)
     */
    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'rupture' => 'red',
            'stock_bas' => 'orange',
            default => 'green'
        };
    }
    
    /**
     * Accessor : vérifier si stock bas
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->track_stock && 
               $this->stock_quantity <= $this->stock_alert_threshold && 
               $this->stock_quantity > 0;
    }
    
    /**
     * Accessor : vérifier si rupture
     */
    public function getIsOutOfStockAttribute(): bool
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }
    
    /**
     * Accessor : URL complète de l'image
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/placeholder.png');
    }
    
    /**
     * Accessor : URL de la seconde image
     */
    public function getImageSecondaryUrlAttribute(): string
    {
        if ($this->image_secondary) {
            return asset('storage/' . $this->image_secondary);
        }
        return $this->image_url;
    }
    
    /**
     * Accessor : URL du produit
     */
    public function getUrlAttribute(): string
    {
        return route('products.show', $this->slug);
    }
    
    /**
     * Accessor : prix dans une devise spécifique
     */
    public function getPriceInCurrency($currencyCode = 'USD'): float
    {
        if ($currencyCode === 'USD') {
            return $this->price;
        }
        
        $currency = Currency::where('code', $currencyCode)->first();
        return $currency ? $this->price * $currency->rate : $this->price;
    }
    
    /**
     * Accessor : prix formaté dans une devise spécifique
     */
    public function getFormattedPriceInCurrency($currencyCode = 'USD'): string
    {
        $currency = Currency::where('code', $currencyCode)->first();
        $amount = $this->getPriceInCurrency($currencyCode);
        
        if ($currency && $currency->code === 'CDF') {
            return $currency->symbol . ' ' . number_format($amount, 0, ',', ' ');
        }
        
        return $currency->symbol . ' ' . number_format($amount, 2, ',', ' ');
    }
    
    // ========== METHODES UTILITAIRES ==========
    
    /**
     * Incrémenter le compteur de vues
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }
    
    /**
     * Vérifier la disponibilité pour une quantité
     */
    public function isAvailable(int $quantity = 1): bool
    {
        if (!$this->track_stock) {
            return true;
        }
        
        return $this->stock_quantity >= $quantity;
    }
    
    /**
     * Réduire le stock (après commande)
     */
    public function decreaseStock(int $quantity): bool
    {
        if (!$this->track_stock) {
            return true;
        }
        
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        
        return false;
    }
    
    /**
     * Augmenter le stock (retour produit, réassort)
     */
    public function increaseStock(int $quantity): void
    {
        if ($this->track_stock) {
            $this->increment('stock_quantity', $quantity);
        }
    }
    
    /**
     * Obtenir les produits similaires (même catégorie)
     */
    public function getSimilarProducts(int $limit = 4)
    {
        return Product::active()
                     ->where('category_id', $this->category_id)
                     ->where('id', '!=', $this->id)
                     ->limit($limit)
                     ->get();
    }
    
    /**
     * Calculer la remise (si implémentée plus tard)
     */
    public function getDiscountedPrice(?float $discountPercentage = null): ?float
    {
        if ($discountPercentage && $discountPercentage > 0) {
            return $this->price * (1 - $discountPercentage / 100);
        }
        return null;
    }
    
    /**
     * Dupliquer un produit
     */
    public function duplicate(): Product
    {
        $newProduct = $this->replicate();
        $newProduct->name = $this->name . ' (copie)';
        $newProduct->slug = Str::slug($this->name . '-copie');
        $newProduct->stock_quantity = 0;
        $newProduct->views = 0;
        $newProduct->save();
        
        return $newProduct;
    }
    // Dans la section RELATIONS
public function reviews(): HasMany
{
    return $this->hasMany(ProductReview::class);
}

public function approvedReviews(): HasMany
{
    return $this->hasMany(ProductReview::class)->where('is_approved', true);
}

// Dans la section ACCESSORS
public function getRatingStarsAttribute(): string
{
    $stars = '';
    $rating = $this->avg_rating;
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="fas fa-star text-yellow-400"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $stars .= '<i class="fas fa-star-half-alt text-yellow-400"></i>';
        } else {
            $stars .= '<i class="far fa-star text-gray-300"></i>';
        }
    }
    return $stars;
}

public function getRatingPercentageAttribute(): int
{
    if ($this->reviews_count == 0) return 0;
    return round(($this->avg_rating / 5) * 100);
}

// Dans la section METHODES
public function updateRatingStats()
{
    $avgRating = ProductReview::where('product_id', $this->id)
        ->where('is_approved', true)
        ->avg('rating') ?? 0;
    
    $reviewsCount = ProductReview::where('product_id', $this->id)
        ->where('is_approved', true)
        ->count();
    
    $this->avg_rating = round($avgRating, 1);
    $this->reviews_count = $reviewsCount;
    $this->rating_percentage = $reviewsCount > 0 ? ($avgRating / 5) * 100 : 0;
    $this->save();
}
}