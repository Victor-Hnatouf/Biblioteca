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
        Schema::create('alerta_disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('cidadao_id')->constrained('users')->cascadeOnDelete();
            
            // Snapshot do cidadão no momento do pedido
            $table->string('cidadao_nome');
            $table->string('cidadao_email');
            
            $table->boolean('notificado')->default(false);
            $table->timestamp('notificado_em')->nullable();
            
            $table->timestamps();
            
            $table->index(['livro_id', 'notificado']);
            $table->index(['cidadao_id', 'notificado']);
            
            // Um cidadão só pode ter um alerta ativo por livro
            $table->unique(['livro_id', 'cidadao_id', 'notificado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerta_disponibilidades');
    }
};
