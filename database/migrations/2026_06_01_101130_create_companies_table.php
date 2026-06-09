<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            
            // Informations générales
            $table->string('name')->default('Mibaraka House');
            $table->string('slogan')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('République Démocratique du Congo');
            
            // Logo et images
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('invoice_logo_path')->nullable();
            
            // Réseaux sociaux
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            
            // Livraison
            $table->decimal('delivery_fee_standard', 10, 2)->default(3.00);
            $table->decimal('delivery_fee_free_threshold', 10, 2)->default(50.00);
            
            // Horaires
            $table->string('opening_hours')->nullable();
            $table->string('closing_hours')->nullable();
            $table->json('working_days')->nullable(); // ["monday","tuesday",...]
            
            // Informations légales
            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();
            
            // Personnalisation
            $table->string('primary_color')->default('#2563eb');
            $table->string('secondary_color')->default('#7c3aed');
            
            // WhatsApp
            $table->string('whatsapp_greeting')->nullable();
            $table->boolean('whatsapp_notifications')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};