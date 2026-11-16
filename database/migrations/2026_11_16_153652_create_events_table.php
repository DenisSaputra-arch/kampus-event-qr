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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->dateTime('start_at');
            $table->unsignedInteger('quota');
            // Status event: 'published' (dapat didaftar), 'draft' (belum tampil), 'cancelled' (dibatalkan)
            $table->string('status', 20)->default('published');
            $table->timestamps();

            // Indexing untuk optimalisasi query pencarian dan filter status
            $table->index('status');
            $table->index('start_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
