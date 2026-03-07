<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_page_settings', function (Blueprint $table) {
            $table->id();
            
            // Hero Section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            
            // Mission Section
            $table->string('mission_badge')->nullable();
            $table->string('mission_title')->nullable();
            $table->text('mission_text')->nullable();
            $table->string('mission_image')->nullable();
            
            // Stats Section
            $table->string('stat_1_value')->nullable();
            $table->string('stat_1_label')->nullable();
            $table->string('stat_2_value')->nullable();
            $table->string('stat_2_label')->nullable();
            $table->string('stat_3_value')->nullable();
            $table->string('stat_3_label')->nullable();
            $table->string('stat_4_value')->nullable();
            $table->string('stat_4_label')->nullable();
            
            // Values Section
            $table->string('values_title')->nullable();
            $table->string('value_1_title')->nullable();
            $table->text('value_1_text')->nullable();
            $table->string('value_1_icon')->nullable();
            $table->string('value_2_title')->nullable();
            $table->text('value_2_text')->nullable();
            $table->string('value_2_icon')->nullable();
            $table->string('value_3_title')->nullable();
            $table->text('value_3_text')->nullable();
            $table->string('value_3_icon')->nullable();
            
            // CTA Section
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_page_settings');
    }
};
