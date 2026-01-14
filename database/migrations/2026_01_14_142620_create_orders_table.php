<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status')->default('pending'); // pending|deposit_paid|paid|cancelled|refunded

            $table->decimal('total_amount', 10, 2)->default(0);

            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->timestamp('deposit_paid_at')->nullable();

            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->timestamp('balance_paid_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};