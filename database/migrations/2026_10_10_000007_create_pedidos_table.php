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
            $table->foreignId('comprador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('emprendimiento_id')->constrained()->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'confirmado', 'entregado', 'rechazado', 'cancelado'])
                ->default('pendiente');
            $table->enum('metodo_pago', ['efectivo', 'transferencia']);
            $table->string('nota', 300)->nullable();
            $table->string('motivo', 300)->nullable();
            $table->decimal('total', 12, 2);
            $table->timestamp('confirmado_at')->nullable();
            $table->timestamp('entregado_at')->nullable();
            $table->timestamps();
            $table->index(['emprendimiento_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
