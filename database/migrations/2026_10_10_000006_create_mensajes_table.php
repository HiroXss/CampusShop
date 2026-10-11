<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('conversaciones')->cascadeOnDelete();
            $table->foreignId('emisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('contenido', 1000);
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();
            $table->index(['conversacion_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};