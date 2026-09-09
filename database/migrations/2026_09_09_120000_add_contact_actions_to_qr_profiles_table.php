<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_profiles', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('phone');
            $table->text('address')->nullable()->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('qr_profiles', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'address']);
        });
    }
};
