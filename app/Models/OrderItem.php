<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';
    
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'unit_price',
        'quantity',
        'subtotal',
        'notes'
    ];
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $appends = [
        'formatted_unit_price',
        'formatted_subtotal'
    ];
    
    // ========== RELATIONS ==========
    
    /**
     * La commande associée
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Le produit associé (si encore existant)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    // ========== SCOPES ==========
    
    /**
     * Scope pour une commande spécifique
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }
    
    /**
     * Scope pour un produit spécifique
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
    
    // ========== ACCESSORS & MUTATORS ==========
    
    /**
     * Accessor : prix unitaire formaté
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        $currency = Currency::where('code', $this->order->currency_code ?? 'USD')->first();
        
        if (!$currency) {
            $currency = Currency::default()->first();
        }
        
        return $currency->formatAmount($this->unit_price);
    }
    
    /**
     * Accessor : sous-total formaté
     */
    public function getFormattedSubtotalAttribute(): string
    {
        $currency = Currency::where('code', $this->order->currency_code ?? 'USD')->first();
        
        if (!$currency) {
            $currency = Currency::default()->first();
        }
        
        return $currency->formatAmount($this->subtotal);
    }
    
    /**
     * Accessor : URL de l'image du produit
     */
    public function getProductImageUrlAttribute(): string
    {
        if ($this->product_image) {
            return asset('storage/' . $this->product_image);
        }
        
        if ($this->product && $this->product->image_path) {
            return asset('storage/' . $this->product->image_path);
        }
        
        return asset('images/placeholder.png');
    }
    
    /**
     * Accessor : vérifier si le produit existe toujours
     */
    public function getProductExistsAttribute(): bool
    {
        return !is_null($this->product);
    }
    
    /**
     * Mutator : calculer automatiquement le sous-total
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        
        // Recalculer le sous-total si le prix unitaire existe
        if (isset($this->attributes['unit_price'])) {
            $this->attributes['subtotal'] = $this->attributes['unit_price'] * $value;
        }
    }
    
    /**
     * Mutator : calculer automatiquement le sous-total quand le prix change
     */
    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
        
        // Recalculer le sous-total si la quantité existe
        if (isset($this->attributes['quantity'])) {
            $this->attributes['subtotal'] = $value * $this->attributes['quantity'];
        }
    }
    
    // ========== METHODES UTILITAIRES ==========
    
    /**
     * Créer une ligne de commande à partir d'un produit
     */
    public static function createFromProduct(Product $product, int $quantity, array $extra = []): self
    {
        return self::create(array_merge([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_image' => $product->image_path,
            'unit_price' => $product->price,
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity
        ], $extra));
    }
    
    /**
     * Mettre à jour la ligne de commande (quantité)
     */
    public function updateQuantity(int $newQuantity): void
    {
        $this->quantity = $newQuantity;
        $this->subtotal = $this->unit_price * $newQuantity;
        $this->save();
        
        // Recalculer le total de la commande
        $this->order->recalculateTotal();
    }
    
    /**
     * Vérifier si le produit est toujours disponible
     */
    public function isProductStillAvailable(): bool
    {
        if (!$this->product) {
            return false;
        }
        
        if (!$this->product->is_active) {
            return false;
        }
        
        if ($this->product->track_stock && $this->product->stock_quantity < $this->quantity) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtenir le libellé complet de la ligne
     */
    public function getLabelAttribute(): string
    {
        return "{$this->quantity}x {$this->product_name} - {$this->formatted_subtotal}";
    }
    
    /**
     * Dupliquer la ligne pour une nouvelle commande
     */
    public function duplicateForOrder(int $newOrderId): self
    {
        $newItem = $this->replicate();
        $newItem->order_id = $newOrderId;
        $newItem->save();
        
        return $newItem;
    }
}