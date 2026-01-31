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
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();

            // Collegamento opzionale alla vendita
            $table->foreignId('sale_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // IN = entrata, OUT = uscita
            $table->enum('type', ['IN', 'OUT']);

            // Importo del movimento
            $table->decimal('amount', 8, 2);

            // Metodo di pagamento
            $table->enum('payment_method', ['cash', 'card', 'mixed'])
                ->default('cash');

            // Descrizione libera
            $table->string('description')->nullable();

            // Data/ora reale del movimento
            $table->timestamp('occurred_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
