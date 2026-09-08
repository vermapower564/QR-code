<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('qr_profiles')->onDelete('cascade');
            $table->timestamp('scanned_at');
            $table->string('ip_hash')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('device')->nullable();
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->string('referrer')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
    }
};
