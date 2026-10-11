<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            // Se incluyen productos eliminados lógicamente (conservan sus datos)
            $table->foreignId('producto_id')->constrained('productos');
            $table->string('nombre_producto', 120);
            $table->decimal('precio_unitario', 10, 2);
            $table->unsignedSmallInteger('cantidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};
