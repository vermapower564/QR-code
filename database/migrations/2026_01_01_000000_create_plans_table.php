<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->integer('profile_limit')->default(1);
            $table->integer('link_limit')->default(10);
            $table->integer('storage_limit')->default(100); // in MB
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
