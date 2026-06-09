<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2); // Prix en USD (devise de référence)
            $table->string('image_path')->nullable();
            $table->string('image_secondary')->nullable(); // Deuxième image
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('restrict'); // Empêche suppression si produits existent
            
            // Gestion des stocks (prêt pour le futur)
            $table->integer('stock_quantity')->default(0);
            $table->integer('stock_alert_threshold')->default(5);
            $table->boolean('track_stock')->default(false); // Désactivé au début
            
            // Visibilité
            $table->boolean('is_featured')->default(false); // Produit phare
            $table->boolean('is_active')->default(true);
            $table->integer('views')->default(0); // Compteur de vues
            
            // Métadonnées
            $table->string('unit')->nullable(); // kg, litre, pièce, pack...
            $table->decimal('weight', 10, 2)->nullable(); // Poids en kg
            
            $table->timestamps();
            
            // Index
            $table->index('category_id');
            $table->index('is_featured');
            $table->index('is_active');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};