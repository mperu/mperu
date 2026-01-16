<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // Fiscal mode: oggi only "withholding", domani potrai aggiungere "vat"
            $table->string('fiscal_mode')->default('withholding')->after('status');

            // Ritenuta (es. 20)
            $table->unsignedTinyInteger('withholding_rate')->default(20)->after('balance_amount');

            // Importi fiscali (sempre 2 decimali)
            $table->decimal('withholding_amount', 10, 2)->default(0)->after('withholding_rate');
            $table->decimal('net_amount', 10, 2)->default(0)->after('withholding_amount');

            $table->index(['fiscal_mode', 'withholding_rate']);
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex(['fiscal_mode', 'withholding_rate']);
            $table->dropColumn([
                'fiscal_mode',
                'withholding_rate',
                'withholding_amount',
                'net_amount',
            ]);
        });
    }
};