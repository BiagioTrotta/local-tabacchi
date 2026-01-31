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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Totale vendita (calcolato, non fidarti del client)
            $table->decimal('total_amount', 8, 2)->default(0);

            // Metodo di pagamento (contanti, carta, ecc.)
            $table->string('payment_method')->default('contanti');

            // Stato vendita (utile per resi, annullamenti)
            $table->string('status')->default('completed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
