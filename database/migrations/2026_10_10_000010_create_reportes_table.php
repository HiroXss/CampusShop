<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reportable_type'); // 'producto' | 'emprendimiento' (morph map)
            $table->unsignedBigInteger('reportable_id');
            $table->string('motivo', 300);
            $table->enum('estado', ['pendiente', 'descartado', 'aceptado'])->default('pendiente');
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'reportable_type', 'reportable_id'], 'reportes_unico_usuario_elemento');
            $table->index(['reportable_type', 'reportable_id'], 'reportes_elemento_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};