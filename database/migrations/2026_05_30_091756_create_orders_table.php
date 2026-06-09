<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Statut de la commande
            $table->enum('status', [
                'pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'
            ])->default('pending');
            
            // Montants - IMPORTANT: nullable avec default 0
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            
            // Devise et taux
            $table->string('currency_code', 3)->default('USD');
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            
            // Informations client (copie)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('delivery_address');
            $table->string('delivery_city')->nullable();
            $table->string('delivery_neighborhood')->nullable();
            
            // Livraison
            $table->text('delivery_notes')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Paiement
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            
            // WhatsApp
            $table->boolean('whatsapp_sent')->default(false);
            $table->timestamp('whatsapp_sent_at')->nullable();
            
            // Notes
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('order_number');
            $table->index('status');
            $table->index('customer_phone');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};