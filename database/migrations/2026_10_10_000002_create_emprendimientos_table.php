<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprendimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre', 80)->unique();
            $table->string('slug', 100)->unique();
            $table->text('descripcion');
            $table->string('logo', 255);
            $table->string('banner', 255)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('instagram', 60)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'pausado', 'suspendido'])
                ->default('pendiente')->index();
            $table->string('motivo_rechazo', 300)->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->boolean('destacado')->default(false);
            $table->boolean('oculta')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprendimientos');
    }
};
