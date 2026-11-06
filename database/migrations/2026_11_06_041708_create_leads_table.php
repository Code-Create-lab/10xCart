<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contact-form enquiries (also emailed). Kept when the linked visitor is deleted.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('ref', 20)->unique();
            $table->foreignId('visitor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('interest', 60);
            $table->string('plan', 30)->nullable();
            $table->text('message')->nullable();
            $table->string('ip', 45)->nullable();
            $table->json('context')->nullable();
            $table->json('location')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
