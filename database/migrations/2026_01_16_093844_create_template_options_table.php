<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_options', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();   // es: hosting_12m
            $table->string('label');
            $table->string('type');            // bool | number (MVP)
            $table->integer('price_delta')->default(0); // centesimi

            $table->json('constraints')->nullable(); // es: {"available_for":["bronze","silver"]}
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_options');
    }
};