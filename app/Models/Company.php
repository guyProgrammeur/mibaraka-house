<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    protected $table = 'companies';
    
    protected $fillable = [
        'name', 'slogan', 'email', 'phone', 'whatsapp', 'address', 'city', 'country',
        'logo_path', 'favicon_path', 'invoice_logo_path',
        'facebook', 'instagram', 'twitter', 'youtube',
        'delivery_fee_standard', 'delivery_fee_free_threshold',
        'opening_hours', 'closing_hours', 'working_days',
        'registration_number', 'tax_number',
        'primary_color', 'secondary_color',
        'whatsapp_greeting', 'whatsapp_notifications'
    ];
    
    protected $casts = [
        'working_days' => 'array',
        'delivery_fee_standard' => 'decimal:2',
        'delivery_fee_free_threshold' => 'decimal:2',
        'whatsapp_notifications' => 'boolean',
    ];
    
    protected $appends = [
        'logo_url',
        'favicon_url',
        'invoice_logo_url',
        'formatted_phone',
        'formatted_whatsapp'
    ];
    
    /**
     * Obtenir l'instance de l'entreprise (singleton)
     */
    public static function instance()
    {
        return self::first() ?? self::create();
    }
    
    /**
     * Accessor : URL du logo
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('images/default-logo.png');
    }
    
    /**
     * Accessor : URL du favicon
     */
    public function getFaviconUrlAttribute(): string
    {
        if ($this->favicon_path && Storage::disk('public')->exists($this->favicon_path)) {
            return asset('storage/' . $this->favicon_path);
        }
        return asset('favicon.ico');
    }
    
    /**
     * Accessor : URL du logo facture
     */
    public function getInvoiceLogoUrlAttribute(): string
    {
        if ($this->invoice_logo_path && Storage::disk('public')->exists($this->invoice_logo_path)) {
            return asset('storage/' . $this->invoice_logo_path);
        }
        return $this->logo_url;
    }
    
    /**
     * Accessor : téléphone formaté
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (strlen($phone) === 10) {
            return substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 4);
        }
        return $phone;
    }
    
    /**
     * Accessor : WhatsApp formaté
     */
    public function getFormattedWhatsappAttribute(): string
    {
        $whatsapp = $this->whatsapp;
        if (strlen($whatsapp) === 10) {
            return substr($whatsapp, 0, 3) . ' ' . substr($whatsapp, 3, 3) . ' ' . substr($whatsapp, 6, 4);
        }
        return $whatsapp;
    }
    
    /**
     * Accessor : lien WhatsApp direct
     */
    public function getWhatsappLinkAttribute(): string
    {
        $phone = $this->whatsapp;
        if (strlen($phone) === 10) {
            $phone = '243' . $phone;
        }
        return "https://wa.me/{$phone}";
    }
    
    /**
     * Calculer les frais de livraison
     */
    public function calculateDeliveryFee(float $subtotal): float
    {
        if ($subtotal >= $this->delivery_fee_free_threshold) {
            return 0;
        }
        return $this->delivery_fee_standard;
    }
    
    /**
     * Obtenir les jours ouvrés formatés
     */
    public function getFormattedWorkingDaysAttribute(): string
    {
        if (!$this->working_days) {
            return 'Lundi - Samedi';
        }
        
        $days = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
        ];
        
        $selected = array_map(fn($d) => $days[$d] ?? $d, $this->working_days);
        return implode(', ', $selected);
    }
}