<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unique('order_id'); // 1 ordine -> 1 progetto

            $table->string('status')->default('new'); // new|in_progress|review|delivered|closed

            $table->string('subdomain')->nullable(); // preview/customer subdomain
            $table->string('snapshot_path')->nullable(); // pacchetto/snapshot pronto
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};