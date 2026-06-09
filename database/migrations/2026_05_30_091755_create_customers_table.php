<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('phone')->unique(); // Important pour WhatsApp
            $table->string('email')->nullable();
            $table->text('default_address')->nullable();
            $table->string('city')->nullable();
            $table->string('neighborhood')->nullable(); // Quartier pour la livraison
            
            // Statistiques
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            
            // Préférences
            $table->string('preferred_currency', 3)->default('USD'); // USD ou CDF
            
            // Métadonnées
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Index
            $table->index('phone');
            $table->index('total_spent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};