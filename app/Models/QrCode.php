<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrCode extends Model
{
    protected $table = 'qr_codes';
    
    protected $fillable = [
        'name',
        'slug',
        'type',
        'code',
        'category_id',
        'product_id',
        'size',
        'color',
        'custom_color',
        'format',
        'logo_path',
        'scan_count',
        'last_scanned_at',
        'is_active',
        'description'
    ];
    
    protected $casts = [
        'scan_count' => 'integer',
        'last_scanned_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $appends = [
        'qr_url',
        'destination_url',
        'type_label',
        'size_pixels'
    ];
    
    // ========== RELATIONS ==========
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    // ========== SCOPES ==========
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
    
    // ========== ACCESSORS ==========
    
    /**
     * URL de destination du QR code (front client)
     */
    public function getDestinationUrlAttribute(): string
    {
        return route('qr.redirect', $this->code);
    }
    
    /**
     * URL pour générer l'image du QR code
     */
    public function getQrUrlAttribute(): string
    {
        return route('qr.generate', $this->code);
    }
    
    /**
     * Libellé du type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'catalog' => 'Catalogue complet',
            'category' => 'Catégorie: ' . ($this->category?->name ?? 'Non définie'),
            'product' => 'Produit: ' . ($this->product?->name ?? 'Non défini'),
            default => 'Inconnu'
        };
    }
    
    /**
     * Taille en pixels
     */
    public function getSizePixelsAttribute(): int
    {
        return match($this->size) {
            'small' => 200,
            'large' => 400,
            default => 300
        };
    }
    
    /**
     * Couleur du QR (format hex)
     */
    public function getQrColorAttribute(): string
    {
        if ($this->color === 'custom' && $this->custom_color) {
            return $this->custom_color;
        }
        
        return match($this->color) {
            'white' => '#FFFFFF',
            default => '#000000'
        };
    }
    
    // ========== MUTATORS ==========
    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
    
    // ========== METHODES ==========
    
    /**
     * Générer un code unique
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
    
    /**
     * Incrémenter le compteur de scans
     */
    public function incrementScanCount(): void
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }
    
    /**
     * Obtenir le contenu du QR (données brutes)
     */
    public function getQrContent(): string
    {
        return $this->destination_url;
    }
    
    /**
     * Vérifier si le QR est valide (destination active)
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        return match($this->type) {
            'category' => $this->category && $this->category->is_active,
            'product' => $this->product && $this->product->is_active,
            'catalog' => true,
            default => false
        };
    }
}