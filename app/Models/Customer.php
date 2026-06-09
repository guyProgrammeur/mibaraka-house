<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $table = 'customers';
    
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'default_address',
        'city',
        'neighborhood',
        'total_orders',
        'total_spent',
        'last_order_at',
        'last_active_at',
        'preferred_currency',
        'notes',
        'is_active'
    ];
    
    protected $casts = [
        'user_id' => 'integer',
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'last_order_at' => 'datetime',
        'last_active_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $appends = [
        'formatted_total_spent',
        'is_frequent_buyer',
        'customer_since'
    ];
    
    // ========== RELATIONS ==========
    
    /**
     * Relation avec l'utilisateur Laravel (si le client a un compte)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Toutes les commandes du client
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)
                    ->orderBy('created_at', 'desc');
    }
    
    /**
     * La dernière commande du client
     */
    public function lastOrder(): HasOne
    {
        return $this->hasOne(Order::class)->latest();
    }
    
    /**
     * La devise préférée du client
     */
    public function preferredCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'preferred_currency', 'code');
    }
    
    // ========== SCOPES ==========
    
    /**
     * Scope pour les clients actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope pour les clients fidèles (plus de 5 commandes)
     */
    public function scopeFrequentBuyers($query)
    {
        return $query->where('total_orders', '>=', 5);
    }
    
    /**
     * Scope pour les clients qui ont dépensé plus qu'un montant
     */
    public function scopeSpentMoreThan($query, float $amount)
    {
        return $query->where('total_spent', '>=', $amount);
    }
    
    /**
     * Scope pour les clients actifs récemment (dernier mois)
     */
    public function scopeActiveRecently($query)
    {
        return $query->where('last_active_at', '>=', now()->subDays(30));
    }
    
    /**
     * Scope pour rechercher par téléphone
     */
    public function scopeWherePhone($query, string $phone)
    {
        return $query->where('phone', $phone);
    }
    
    /**
     * Scope pour rechercher par nom
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
    
    // ========== ACCESSORS & MUTATORS ==========
    
    /**
     * Mutator : nettoyer le numéro de téléphone
     */
    public function setPhoneAttribute($value)
    {
        // Supprime les espaces, tirets, et le + si présent
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        $this->attributes['phone'] = $cleaned;
    }
    
    /**
     * Accessor : téléphone formaté
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        
        // Format RDC: +243 XXX XXX XXX
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '243') {
            return '+' . substr($phone, 0, 3) . ' ' . 
                   substr($phone, 3, 3) . ' ' . 
                   substr($phone, 6, 3) . ' ' . 
                   substr($phone, 9, 2);
        }
        
        // Format local: 08XXXXXXXXX
        if (strlen($phone) === 10) {
            return substr($phone, 0, 3) . ' ' . 
                   substr($phone, 3, 3) . ' ' . 
                   substr($phone, 6, 4);
        }
        
        return $phone;
    }
    
    /**
     * Accessor : lien WhatsApp direct
     */
    public function getWhatsappLinkAttribute(): string
    {
        $phone = $this->phone;
        // Ajouter le code pays 243 si absent
        if (strlen($phone) === 10) {
            $phone = '243' . $phone;
        }
        return 'https://wa.me/' . $phone;
    }
    
    /**
     * Accessor : montant total dépensé formaté
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        return $defaultCurrency->formatAmount($this->total_spent);
    }
    
    /**
     * Accessor : vérifier si client fidèle
     */
    public function getIsFrequentBuyerAttribute(): bool
    {
        return $this->total_orders >= 5;
    }
    
    /**
     * Accessor : depuis quand client
     */
    public function getCustomerSinceAttribute(): string
    {
        return $this->created_at->format('d/m/Y');
    }
    
    /**
     * Accessor : âge du client (nombre de jours)
     */
    public function getCustomerAgeInDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }
    
    /**
     * Accessor : adresse complète formatée
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];
        
        if ($this->default_address) {
            $parts[] = $this->default_address;
        }
        if ($this->neighborhood) {
            $parts[] = $this->neighborhood;
        }
        if ($this->city) {
            $parts[] = $this->city;
        }
        
        return implode(', ', $parts);
    }
    
    // ========== METHODES UTILITAIRES ==========
    
    /**
     * Mettre à jour les statistiques du client
     */
    public function updateStats(): void
    {
        $totalOrders = $this->orders()->count();
        $totalSpent = $this->orders()
                           ->where('status', 'delivered')
                           ->sum('total_amount');
        $lastOrder = $this->orders()->latest()->first();
        
        $this->update([
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'last_order_at' => $lastOrder ? $lastOrder->created_at : null
        ]);
    }
    
    /**
     * Marquer comme actif (mettre à jour last_active_at)
     */
    public function markAsActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }
    
    /**
     * Obtenir le panier actuel du client (en session ou en base)
     */
    public function getCart(): array
    {
        // Pour l'instant, retourne un panier vide
        // Sera implémenté avec la gestion de panier plus tard
        return session()->get("cart_{$this->id}", []);
    }
    
    /**
     * Créer ou récupérer un client par téléphone
     */
    public static function firstOrCreateByPhone(string $phone, array $data = []): self
    {
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
        
        return static::firstOrCreate(
            ['phone' => $cleanedPhone],
            array_merge([
                'name' => $data['name'] ?? 'Client',
                'is_active' => true,
                'preferred_currency' => 'USD'
            ], $data)
        );
    }
    
    /**
     * Envoyer un message WhatsApp au client
     */
    public function sendWhatsappMessage(string $message): string
    {
        $phone = $this->phone;
        if (strlen($phone) === 10) {
            $phone = '243' . $phone;
        }
        
        $encodedMessage = urlencode($message);
        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }
    
    /**
     * Vérifier si le client a commandé récemment
     */
    public function hasOrderedRecently(int $days = 30): bool
    {
        return $this->orders()
                    ->where('created_at', '>=', now()->subDays($days))
                    ->exists();
    }
    
    /**
     * Obtenir les produits favoris du client (les plus achetés)
     */
    public function getFavoriteProducts(int $limit = 5)
    {
        return Product::select('products.*')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.customer_id', $this->id)
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Statut du client (texte)
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="badge badge-danger">Inactif</span>';
        }
        
        if ($this->is_frequent_buyer) {
            return '<span class="badge badge-warning">⭐⭐ Client fidèle</span>';
        }
        
        if ($this->total_orders > 0) {
            return '<span class="badge badge-success">Actif</span>';
        }
        
        return '<span class="badge badge-info">Nouveau</span>';
    }
}