<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();
            
            // Hero Section
            $table->string('hero_badge')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_button_text')->nullable();
            $table->string('hero_button_url')->nullable();
            $table->string('hero_image')->nullable();
            
            // Features Section
            $table->string('features_title')->nullable();
            $table->text('features_subtitle')->nullable();
            
            // Feature 1
            $table->string('feature_1_title')->nullable();
            $table->text('feature_1_text')->nullable();
            $table->string('feature_1_icon')->nullable();
            
            // Feature 2
            $table->string('feature_2_title')->nullable();
            $table->text('feature_2_text')->nullable();
            $table->string('feature_2_icon')->nullable();
            
            // Feature 3
            $table->string('feature_3_title')->nullable();
            $table->text('feature_3_text')->nullable();
            $table->string('feature_3_icon')->nullable();
            
            // Trending Section
            $table->string('trending_title')->nullable();
            $table->text('trending_subtitle')->nullable();
            
            // CTA Section
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
            $table->string('cta_button_1_text')->nullable();
            $table->string('cta_button_1_url')->nullable();
            $table->string('cta_button_2_text')->nullable();
            $table->string('cta_button_2_url')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
