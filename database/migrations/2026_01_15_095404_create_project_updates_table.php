<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // admin o cliente, opzionale
            $table->string('type')->default('status'); // status|note|comment|system

            $table->text('message')->nullable();

            // per audit "prima/dopo" (es: status change)
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();

            $table->timestamps();

            $table->index(['project_id', 'created_at']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};