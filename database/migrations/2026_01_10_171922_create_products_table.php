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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('categoria');
            $table->integer('codice')->unique(); // codice ADM
            $table->string('denominazione_commerciale');

            $table->decimal('prezzo_kg_euro', 8, 2);
            $table->decimal('prezzo_confezione_euro', 6, 2);
            $table->string('tipo_confezione');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
