<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('qr_profiles')->onDelete('cascade');
            $table->string('event_type'); // qr_scan, profile_view, link_click, contact_save, phone_click, email_click, whatsapp_click, website_click, share
            $table->unsignedBigInteger('link_id')->nullable();
            $table->string('ip_hash')->nullable();
            $table->string('country')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
