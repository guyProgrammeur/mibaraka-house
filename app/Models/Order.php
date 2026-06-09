<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $table = 'orders';
    
    protected $fillable = [
        'order_number',
        'customer_id',
        'user_id',
        'status',
        'subtotal',
        'delivery_fee',
        'total_amount',
        'currency_code',
        'currency_id',
        'exchange_rate',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'delivery_city',
        'delivery_neighborhood',
        'delivery_notes',
        'delivered_at',
        'payment_method',
        'payment_status',
        'whatsapp_sent',
        'whatsapp_sent_at',
        'admin_notes'
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'whatsapp_sent' => 'boolean',
        'whatsapp_sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $appends = [
        'formatted_subtotal',
        'formatted_total',
        'formatted_delivery_fee',
        'status_label',
        'status_badge',
        'can_be_cancelled'
    ];
    
    // ========== CONSTANTES ==========
    
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_MOBILE_MONEY = 'mobile_money';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    
    // ========== RELATIONS ==========
    
    /**
     * Le client associé à la commande
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * L'administrateur qui a traité la commande
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * La devise utilisée pour la commande
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    
    /**
     * Les lignes de commande (produits)
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // ========== SCOPES ==========
    
    /**
     * Scope pour les commandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    
    /**
     * Scope pour les commandes confirmées
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }
    
    /**
     * Scope pour les commandes livrées
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }
    
    /**
     * Scope pour les commandes annulées
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
    
    /**
     * Scope pour les commandes non annulées (en cours)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }
    
    /**
     * Scope pour les commandes d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    /**
     * Scope pour les commandes d'une période
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    /**
     * Scope pour un statut de paiement spécifique
     */
    public function scopePaymentStatus($query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }
    
    /**
     * Scope pour rechercher par numéro de commande
     */
    public function scopeWhereOrderNumber($query, string $orderNumber)
    {
        return $query->where('order_number', 'like', "%{$orderNumber}%");
    }
    
    /**
     * Scope pour rechercher par téléphone client
     */
    public function scopeWhereCustomerPhone($query, string $phone)
    {
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
        return $query->where('customer_phone', 'like', "%{$cleanedPhone}%");
    }
    
    // ========== ACCESSORS & MUTATORS ==========
    
    /**
     * Mutator : générer le numéro de commande automatiquement
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = $order->generateOrderNumber();
            }
        });
    }
    
    /**
     * Générer un numéro de commande unique
     */
    public function generateOrderNumber(): string
    {
        $prefix = 'MB';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        
        return "{$prefix}-{$date}-{$random}";
    }
    
    /**
     * Accessor : sous-total formaté
     */
    public function getFormattedSubtotalAttribute(): string
    {
        $currency = Currency::where('code', $this->currency_code)->first();
        return $currency->formatAmount($this->subtotal);
    }
    
    /**
     * Accessor : total formaté
     */
    public function getFormattedTotalAttribute(): string
    {
        $currency = Currency::where('code', $this->currency_code)->first();
        return $currency->formatAmount($this->total_amount);
    }
    
    /**
     * Accessor : frais de livraison formatés
     */
    public function getFormattedDeliveryFeeAttribute(): string
    {
        if ($this->delivery_fee == 0) {
            return 'Gratuit';
        }
        
        $currency = Currency::where('code', $this->currency_code)->first();
        return $currency->formatAmount($this->delivery_fee);
    }
    
    /**
     * Accessor : libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONFIRMED => 'Confirmée',
            self::STATUS_PREPARING => 'En préparation',
            self::STATUS_READY => 'Prête à être livrée',
            self::STATUS_DELIVERED => 'Livrée',
            self::STATUS_CANCELLED => 'Annulée',
            default => 'Inconnu'
        };
    }
    
    /**
     * Accessor : badge Bootstrap du statut
     */
    public function getStatusBadgeAttribute(): string
{
    return $this->status_label;
}
    
    /**
     * Accessor : badge du statut paiement
     */
    public function getPaymentStatusBadgeAttribute(): string
{
    return match($this->payment_status) {
        self::PAYMENT_PENDING => 'En attente',
        self::PAYMENT_PAID => 'Payé',
        self::PAYMENT_FAILED => 'Échoué',
        default => 'Inconnu'
    };
}
    
    /**
     * Accessor : la commande peut-elle être annulée ?
     */
    public function getCanBeCancelledAttribute(): bool
    {
        return !in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }
    
    /**
     * Accessor : la commande peut-elle être modifiée ?
     */
    public function getCanBeModifiedAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }
    
    // ========== METHODES UTILITAIRES ==========
    
    /**
     * Changer le statut de la commande
     */
    public function changeStatus(string $newStatus, ?string $note = null): bool
    {
        // Vérifier si la transition est autorisée
        $allowedTransitions = [
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
            self::STATUS_PREPARING => [self::STATUS_READY, self::STATUS_CANCELLED],
            self::STATUS_READY => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
            self::STATUS_DELIVERED => [],
            self::STATUS_CANCELLED => []
        ];
        
        if (!in_array($newStatus, $allowedTransitions[$this->status])) {
            return false;
        }
        
        // Si livrée, enregistrer la date
        if ($newStatus === self::STATUS_DELIVERED) {
            $this->delivered_at = now();
        }
        
        $this->status = $newStatus;
        
        if ($note) {
            $this->admin_notes = ($this->admin_notes ? $this->admin_notes . "\n" : '') . 
                                 now()->format('d/m/Y H:i') . " - {$note}";
        }
        
        $this->save();
        
        // Mettre à jour les stats du client si la commande est livrée
        if ($newStatus === self::STATUS_DELIVERED && $this->customer) {
            $this->customer->updateStats();
        }
        
        return true;
    }
    
    /**
     * Marquer le paiement comme payé
     */
    public function markAsPaid(string $method = null): void
    {
        $this->payment_status = self::PAYMENT_PAID;
        
        if ($method) {
            $this->payment_method = $method;
        }
        
        $this->save();
    }
    
    /**
     * Marquer WhatsApp comme envoyé
     */
    public function markWhatsappAsSent(): void
    {
        $this->whatsapp_sent = true;
        $this->whatsapp_sent_at = now();
        $this->save();
    }
    
    /**
     * Calculer le total de la commande
     */
    public function recalculateTotal(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $total = $subtotal + $this->delivery_fee;
        
        $this->subtotal = $subtotal;
        $this->total_amount = $total;
        $this->save();
    }
    
    /**
     * Générer le message WhatsApp pour la commande
     */
    public function generateWhatsappMessage(): string
{
    $message = "*NOUVELLE COMMANDE - Mibaraka House*\n\n";
    $message .= "*N° Commande:* {$this->order_number}\n";
    $message .= "*Client:* {$this->customer_name}\n";
    $message .= "*Téléphone:* {$this->customer_phone}\n\n";
    
    $message .= "*DÉTAILS DE LA COMMANDE:*\n";
    $message .= "-------------------------\n";
    
    foreach ($this->items as $item) {
        $message .= "- {$item->quantity}x {$item->product_name}\n";
        $message .= "  = {$item->formatted_subtotal}\n";
    }
    
    $message .= "-------------------------\n";
    $message .= "Sous-total: {$this->formatted_subtotal}\n";
    
    if ($this->delivery_fee > 0) {
        $message .= "Frais de livraison: {$this->formatted_delivery_fee}\n";
    }
    
    $message .= "TOTAL: {$this->formatted_total}\n\n";
    
    if ($this->delivery_address) {
        $message .= "Adresse de livraison:\n{$this->delivery_address}\n\n";
    }
    
    if ($this->delivery_notes) {
        $message .= "Notes:\n{$this->delivery_notes}\n\n";
    }
    
    $message .= "Statut: En attente de confirmation\n";
    
    return $message;
}
    /**
     * Lien WhatsApp pour envoyer la commande au commerçant
     */
    public function getWhatsappLinkForMerchantAttribute(): string
    {
        $message = $this->generateWhatsappMessage();
        $encodedMessage = urlencode($message);
        
        // À remplacer par le numéro WhatsApp du commerçant
        $merchantPhone = config('app.merchant_whatsapp', '243000000000');
        
        return "https://wa.me/{$merchantPhone}?text={$encodedMessage}";
    }
    
    /**
     * Lien WhatsApp pour envoyer la confirmation au client
     */
    public function getWhatsappLinkForCustomerAttribute(): string
{
    $message = "Bonjour {$this->customer_name},\n\n";
    $message .= "Votre commande n°{$this->order_number} a été ";
    
    if ($this->status === self::STATUS_CONFIRMED) {
        $message .= "confirmée ! Nous la préparons pour vous.";
    } elseif ($this->status === self::STATUS_READY) {
        $message .= "prise en charge pour la livraison.";
    } elseif ($this->status === self::STATUS_DELIVERED) {
        $message .= "livrée avec succès ! Merci d'avoir choisi Mibaraka House.";
    }
    
    $message .= "\n\nContactez-nous au +243 XXX XXX XXX pour toute question.";
    
    $encodedMessage = urlencode($message);
    $customerPhone = $this->customer_phone;
    
    if (strlen($customerPhone) === 10) {
        $customerPhone = '243' . $customerPhone;
    }
    
    return "https://wa.me/{$customerPhone}?text={$encodedMessage}";
}
    
    /**
     * Obtenir les statistiques rapides de la commande
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_items' => $this->items->sum('quantity'),
            'unique_products' => $this->items->count(),
            'age_in_hours' => $this->created_at->diffInHours(now()),
            'is_recent' => $this->created_at->diffInHours(now()) <= 24
        ];
    }
    
    /**
     * Vérifier si la commande est récente (moins de 24h)
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInHours(now()) <= 24;
    }
}