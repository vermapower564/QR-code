<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->json('theme_data')->nullable(); // custom colors, fonts, background, button style
            $table->string('status')->default('active'); // active, inactive, suspended, deleted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_profiles');
    }
};
