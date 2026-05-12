<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('requisicoes');

        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('numero')->unique();

            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('cidadao_id')->constrained('users')->cascadeOnDelete();

            // Snapshot do cidadão no momento da requisição
            $table->string('cidadao_nome');
            $table->string('cidadao_email');
            $table->string('cidadao_profile_photo_path', 2048)->nullable();

            $table->dateTime('requisitado_em');
            $table->date('previsto_entrega_em');

            // Confirmado por Admin aquando da boa recepção/entrega
            $table->date('entregue_em')->nullable();
            $table->foreignId('confirmado_por_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('dias_decorridos')->nullable();

            $table->timestamps();

            $table->index(['cidadao_id', 'entregue_em']);
            $table->index(['livro_id', 'entregue_em']);
            $table->index(['previsto_entrega_em', 'entregue_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};

