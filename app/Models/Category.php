<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
        'position',
        'is_active'
    ];
    
    protected $casts = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // ========== RELATIONS ==========
    
    /**
     * Relation réflexive : le parent (volet principal)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    /**
     * Relation réflexive : les enfants (sous-catégories)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('position');
    }
    
    /**
     * Tous les produits de cette catégorie
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)
                    ->where('is_active', true);
    }
    
    /**
     * Tous les produits (même inactifs) pour l'admin
     */
    public function allProducts(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    // ========== SCOPES ==========
    
    /**
     * Scope pour les volets principaux (sans parent)
     */
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id')
                     ->orderBy('position');
    }
    
    /**
     * Scope pour les sous-catégories (avec parent)
     */
    public function scopeSubCategories($query)
    {
        return $query->whereNotNull('parent_id')
                     ->orderBy('position');
    }
    
    /**
     * Scope pour les catégories actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope pour rechercher par slug
     */
    public function scopeWhereSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
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
     * Accessor : obtenir l'URL de la catégorie
     */
    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }
    
    /**
     * Accessor : obtenir le niveau de profondeur
     */
    public function getDepthLevelAttribute(): string
    {
        return $this->parent_id ? 'Sous-catégorie' : 'Volet principal';
    }
    
    /**
     * Accessor : chemin complet de la catégorie (ex: Alimentaire > Boissons)
     */
    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }
    
    // ========== METHODES UTILITAIRES ==========
    
    /**
     * Vérifier si c'est un volet principal
     */
    public function isMainCategory(): bool
    {
        return is_null($this->parent_id);
    }
    
    /**
     * Vérifier si c'est une sous-catégorie
     */
    public function isSubCategory(): bool
    {
        return !is_null($this->parent_id);
    }
    
    /**
     * Obtenir le nombre total de produits (y compris sous-catégories)
     */
    public function getTotalProductsCount(): int
    {
        if ($this->isMainCategory()) {
            // Compter les produits dans toutes les sous-catégories
            $subCategoryIds = $this->children->pluck('id')->toArray();
            return Product::whereIn('category_id', $subCategoryIds)
                         ->where('is_active', true)
                         ->count();
        }
        
        // C'est une sous-catégorie
        return $this->products()->count();
    }
    
    /**
     * Récupérer tous les produits (incluant sous-catégories)
     */
    public function getAllProducts()
    {
        if ($this->isMainCategory()) {
            $subCategoryIds = $this->children->pluck('id')->toArray();
            return Product::whereIn('category_id', $subCategoryIds)
                         ->where('is_active', true)
                         ->orderBy('name');
        }
        
        return $this->products();
    }
    
    /**
     * Obtenir l'arbre complet des catégories (pour menu hiérarchique)
     */
    public static function getTree()
    {
        return static::with(['children' => function($query) {
                        $query->active()->orderBy('position');
                    }])
                    ->mainCategories()
                    ->active()
                    ->get();
    }
    
    /**
     * Dupliquer une catégorie avec toutes ses sous-catégories
     */
    public function duplicate(): Category
    {
        $newCategory = $this->replicate();
        $newCategory->name = $this->name . ' (copie)';
        $newCategory->slug = Str::slug($this->name . '-copie');
        $newCategory->save();
        
        // Dupliquer les sous-catégories
        foreach ($this->children as $child) {
            $newChild = $child->replicate();
            $newChild->parent_id = $newCategory->id;
            $newChild->name = $child->name;
            $newChild->slug = Str::slug($child->name);
            $newChild->save();
        }
        
        return $newCategory;
    }
}