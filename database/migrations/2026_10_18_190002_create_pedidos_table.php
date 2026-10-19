<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->nullOnDelete();
            $table->string('codigo')->unique();
            $table->string('servicio');
            $table->decimal('kg', 8, 2)->default(0);
            $table->tinyInteger('paso_actual')->default(1);
            $table->string('estado')->default('recogida');
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('express')->default(false);
            $table->boolean('pendiente_pago')->default(true);
            $table->date('fecha');
            $table->dateTime('recogido_en')->nullable();
            $table->date('entrega_estimada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
