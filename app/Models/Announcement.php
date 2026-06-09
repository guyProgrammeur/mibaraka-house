<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    protected $table = 'announcements';
    
    protected $fillable = [
        'title', 'message', 'type', 'position', 'badge', 
        'button_text', 'button_link', 'image', 'icon', 
        'order', 'is_active', 'start_date', 'end_date'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'order' => 'integer',
    ];
    
    protected $appends = ['image_url', 'is_available'];
    
    /**
     * Types d'annonces disponibles
     */
    public static function getTypes(): array
    {
        return [
            'text' => 'Texte seul',
            'button' => 'Texte + Bouton',
            'image' => 'Image seule',
            'image_text' => 'Image + Texte + Bouton',
            'banner' => 'Bannière pleine largeur',
        ];
    }
    
    /**
     * Positions disponibles
     */
    public static function getPositions(): array
    {
        return [
            'top' => 'Haut de page',
            'middle' => 'Milieu de page',
            'bottom' => 'Bas de page',
        ];
    }
    
    /**
     * Accessor : URL de l'image
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return '';
    }
    
    /**
     * Accessor : Vérifier si l'annonce est disponible
     */
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }
        
        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Scope pour les annonces actives par position
     */
    public function scopeActive($query, $position = null)
    {
        $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
            
        if ($position) {
            $query->where('position', $position);
        }
        
        return $query->orderBy('order');
    }
}