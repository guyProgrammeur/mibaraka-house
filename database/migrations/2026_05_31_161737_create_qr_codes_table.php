<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du QR code (ex: "Promo Noël")
            $table->string('slug')->unique(); // slug pour l'URL
            $table->enum('type', ['catalog', 'category', 'product']); // Type de redirection
            $table->string('code')->unique(); // Code unique pour l'URL
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->enum('size', ['small', 'medium', 'large'])->default('medium'); // Taille du QR
            $table->enum('color', ['black', 'white', 'custom'])->default('black'); // Couleur
            $table->string('custom_color')->nullable(); // Couleur personnalisée (#XXXXXX)
            $table->enum('format', ['png', 'svg'])->default('png'); // Format d'export
            $table->string('logo_path')->nullable(); // Logo au centre du QR
            $table->integer('scan_count')->default(0); // Nombre de scans
            $table->timestamp('last_scanned_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index
            $table->index('code');
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};