<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emprendimiento_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('sesion', 64)->comment('hash de la sesión del navegador');
            $table->date('fecha');
            $table->index(['emprendimiento_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};