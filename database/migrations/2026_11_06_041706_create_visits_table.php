<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sessions: a new visit starts after 30 minutes without activity or on a new campaign link.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->cascadeOnDelete();
            $table->string('key', 40);
            $table->timestamp('started_at')->useCurrent()->index();
            $table->timestamp('last_activity_at')->useCurrent()->index();
            $table->string('ip', 45)->nullable();
            $table->json('location')->nullable();
            $table->string('source', 120)->nullable();
            $table->string('referrer', 300)->nullable();
            $table->string('landing', 200)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_term', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->string('click_id', 10)->nullable();
            $table->unsignedInteger('page_views')->default(0);
            $table->unsignedInteger('engaged_seconds')->default(0);
            $table->unsignedTinyInteger('max_scroll')->default(0);
            $table->timestamps();

            $table->unique(['visitor_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
