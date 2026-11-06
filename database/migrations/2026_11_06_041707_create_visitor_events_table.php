<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * What happened during a visit: page views, sections scrolled to, clicks, form activity.
     */
    public function up(): void
    {
        Schema::create('visitor_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('name', 100)->nullable();
            $table->string('path', 200)->nullable();
            $table->timestamp('created_at')->index();

            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_events');
    }
};
