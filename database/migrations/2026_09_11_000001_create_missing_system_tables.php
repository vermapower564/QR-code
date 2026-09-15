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
        if (!Schema::hasTable('custom_domains')) {
            Schema::create('custom_domains', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('profile_id')->nullable()->constrained('qr_profiles')->onDelete('set null');
                $table->string('domain')->unique();
                $table->string('domain_type')->default('custom');
                $table->string('verification_token')->nullable();
                $table->string('verification_status')->default('pending');
                $table->string('ssl_status')->default('pending');
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blocked_domains')) {
            Schema::create('blocked_domains', function (Blueprint $table) {
                $table->id();
                $table->string('domain')->unique();
                $table->string('reason')->nullable();
                $table->string('status')->default('blocked');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('profile_reports')) {
            Schema::create('profile_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('qr_profiles')->onDelete('cascade');
                $table->string('reporter_ip_hash')->nullable();
                $table->string('reason');
                $table->text('description')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('admin_name')->nullable();
                $table->string('action');
                $table->string('target_type')->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->json('details')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('profile_slug_histories')) {
            Schema::create('profile_slug_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('qr_profiles')->onDelete('cascade');
                $table->string('old_slug')->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_slug_histories');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('profile_reports');
        Schema::dropIfExists('blocked_domains');
        Schema::dropIfExists('custom_domains');
    }
};
