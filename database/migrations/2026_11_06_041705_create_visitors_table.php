<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Browsers that accepted analytics cookies (one row per "10x_vid" cookie).
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at')->index();
            $table->unsignedInteger('visits_count')->default(0);
            $table->unsignedInteger('page_views_count')->default(0);
            $table->unsignedInteger('engaged_seconds')->default(0);
            $table->string('first_source', 120)->nullable();
            $table->string('last_source', 120)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('network', 120)->nullable();
            $table->string('device', 30)->nullable();
            $table->string('os', 40)->nullable();
            $table->string('browser', 40)->nullable();
            $table->string('model', 40)->nullable();
            $table->string('screen', 20)->nullable();
            $table->string('language', 20)->nullable();
            $table->string('timezone', 60)->nullable();
            $table->json('traits')->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
