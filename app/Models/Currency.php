<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $table = 'currencies';
    
    protected $fillable = [
        'code',
        'symbol',
        'name',
        'rate',
        'is_default',
        'is_active'
    ];
    
    protected $casts = [
        'rate' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // ========== RELATIONS ==========
    
    /**
     * Une devise peut avoir plusieurs commandes
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    // ========== SCOPES ==========
    
    /**
     * Scope pour la devise par défaut
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    
    /**
     * Scope pour les devises actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // ========== ACCESSORS & MUTATORS ==========
    
    /**
     * Formater le taux avec 4 décimales
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 4, ',', ' ');
    }
    
    /**
     * Obtenir le symbole avec espace (ex: "$ " ou "FC ")
     */
    public function getSymbolWithSpaceAttribute(): string
    {
        return $this->symbol . ' ';
    }
    
    // ========== METHODES UTILITAIRES ==========
    
    /**
     * Convertir un montant USD vers cette devise
     */
    public function convertFromUsd(float $amountInUsd): float
    {
        if ($this->code === 'USD') {
            return $amountInUsd;
        }
        return $amountInUsd * $this->rate;
    }
    
    /**
     * Convertir un montant de cette devise vers USD
     */
    public function convertToUsd(float $amountInThisCurrency): float
    {
        if ($this->code === 'USD') {
            return $amountInThisCurrency;
        }
        return $amountInThisCurrency / $this->rate;
    }
    
    /**
     * Formater un montant dans cette devise
     */
    public function formatAmount(float $amount): string
    {
        $formatted = match($this->code) {
            'CDF' => number_format($amount, 0, ',', ' '),
            default => number_format($amount, 2, ',', ' ')
        };
        
        return $this->symbol . ' ' . $formatted;
    }
    
    /**
     * Définir une nouvelle devise par défaut (désactive l'ancienne)
     */
    public function setAsDefault(): void
    {
        static::where('is_default', true)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }
    
    /**
     * Vérifier si c'est la devise USD
     */
    public function isUsd(): bool
    {
        return $this->code === 'USD';
    }
    
    /**
     * Vérifier si c'est la devise CDF
     */
    public function isCdf(): bool
    {
        return $this->code === 'CDF';
    }
}