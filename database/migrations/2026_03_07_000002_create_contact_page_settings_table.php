<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_page_settings', function (Blueprint $table) {
            $table->id();
            
            $table->string('header_badge')->nullable();
            $table->string('header_title')->nullable();
            $table->text('header_subtitle')->nullable();
            
            // Contact Info
            $table->string('address_title')->nullable();
            $table->text('address_content')->nullable();
            $table->string('phone_title')->nullable();
            $table->text('phone_content')->nullable();
            $table->string('email_title')->nullable();
            $table->text('email_content')->nullable();
            
            // Form Section
            $table->string('form_title')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_page_settings');
    }
};
