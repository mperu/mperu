<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();      // bronze, silver, gold
            $table->string('name');
            $table->text('description')->nullable();

            // Prezzi in centesimi per evitare float
            $table->unsignedInteger('base_price'); // es: 187500 = 1.875,00 EUR

            $table->string('preview_image')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};