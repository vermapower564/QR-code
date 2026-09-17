<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_events', function (Blueprint $table) {
            if (!Schema::hasColumn('analytics_events', 'user_agent_hash')) {
                $table->string('user_agent_hash')->nullable()->after('ip_hash');
            }
            if (!Schema::hasColumn('analytics_events', 'region')) {
                $table->string('region')->nullable()->after('country');
            }
            if (!Schema::hasColumn('analytics_events', 'city')) {
                $table->string('city')->nullable()->after('region');
            }
            if (!Schema::hasColumn('analytics_events', 'os')) {
                $table->string('os')->nullable()->after('device');
            }
            if (!Schema::hasColumn('analytics_events', 'referrer')) {
                $table->string('referrer')->nullable()->after('browser');
            }
            if (!Schema::hasColumn('analytics_events', 'is_bot')) {
                $table->boolean('is_bot')->default(false)->after('referrer');
            }
            if (!Schema::hasColumn('analytics_events', 'occurred_at')) {
                $table->timestamp('occurred_at')->nullable()->after('is_bot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('analytics_events', function (Blueprint $table) {
            $columns = ['user_agent_hash', 'region', 'city', 'os', 'referrer', 'is_bot', 'occurred_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('analytics_events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
