<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emprendimiento_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->text('descripcion');
            $table->enum('tipo', ['producto', 'servicio'])->default('producto');
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('stock')->nullable()->comment('null = sin control de inventario');
            $table->enum('estado', ['disponible', 'agotado', 'inactivo'])->default('disponible');
            $table->boolean('oculto')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['estado', 'oculto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};