<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // 1. Visual & Branding
            $table->string('site_name')->default('Green Mart');
            $table->string('site_tagline')->nullable(); // Slogan
            $table->text('site_description')->nullable(); // Meta Description (SEO)
            $table->string('site_logo')->nullable();     // Path gambar logo
            $table->string('site_favicon')->nullable();  // Path gambar favicon
            
            // 2. Kontak
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            
            // 3. Sosial Media
            $table->string('link_facebook')->nullable();
            $table->string('link_instagram')->nullable();
            $table->string('link_twitter')->nullable(); // Atau X
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
