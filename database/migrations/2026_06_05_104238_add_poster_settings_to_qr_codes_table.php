<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('poster_template')->default('classic')->after('description');
            $table->boolean('show_brand_name')->default(true)->after('poster_template');
            $table->boolean('show_tagline')->default(true)->after('show_brand_name');
            $table->string('poster_background_color')->default('#FFFFFF')->after('show_tagline');
            $table->string('poster_primary_color')->default('#D4AF37')->after('poster_background_color');
            $table->string('poster_text_color')->default('#1a1a1a')->after('poster_primary_color');
            $table->string('custom_message')->nullable()->after('poster_text_color');
        });
    }
    
    public function down()
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn([
                'poster_template', 
                'show_brand_name', 
                'show_tagline', 
                'poster_background_color',
                'poster_primary_color',
                'poster_text_color',
                'custom_message'
            ]);
        });
    }
};