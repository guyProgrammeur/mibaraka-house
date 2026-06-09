<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            
            // Snapshot du produit au moment de la commande (au cas où le produit change plus tard)
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('unit_price', 12, 2); // Prix unitaire au moment de la commande
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            
            // Métadonnées
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};