<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('cidadao_id')->constrained('users')->cascadeOnDelete();
            
            
            $table->string('cidadao_nome');
            $table->string('cidadao_email');
            $table->string('cidadao_profile_photo_path', 2048)->nullable();
            
            $table->text('comentario');
            $table->integer('classificacao')->default(5); 
            
            
            $table->string('estado')->default('suspenso');
            
            
            $table->text('justificacao_recusa')->nullable();
            
            $table->foreignId('aprovado_por_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aprovado_em')->nullable();
            
            $table->timestamps();
            
            $table->index(['livro_id', 'estado']);
            $table->index(['cidadao_id', 'estado']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
