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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('cidadao_id')->constrained('users')->cascadeOnDelete();
            
            // Snapshot do cidadão no momento da review
            $table->string('cidadao_nome');
            $table->string('cidadao_email');
            $table->string('cidadao_profile_photo_path', 2048)->nullable();
            
            $table->text('comentario');
            $table->integer('classificacao')->default(5); // 1-5 stars
            
            // Estado: suspenso, ativo, recusado
            $table->string('estado')->default('suspenso');
            
            // Justificação do admin quando recusado
            $table->text('justificacao_recusa')->nullable();
            
            $table->foreignId('aprovado_por_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aprovado_em')->nullable();
            
            $table->timestamps();
            
            $table->index(['livro_id', 'estado']);
            $table->index(['cidadao_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
