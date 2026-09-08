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
            $table->foreignId('profile_id')->constrained('qr_profiles')->onDelete('cascade');
            $table->string('format')->default('png'); // png, svg, pdf
            $table->string('file_path')->nullable();
            $table->string('foreground_color')->default('#000000');
            $table->string('background_color')->default('#ffffff');
            $table->string('style')->default('square'); // square, rounded, dots
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
