<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->dateTime('cidadao_entregou_em')->nullable()->after('previsto_entrega_em');
            $table->string('condicao_na_devolucao', 32)->nullable()->after('entregue_em');
            $table->index(['cidadao_entregou_em', 'entregue_em']);
        });
    }

    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->dropIndex(['cidadao_entregou_em', 'entregue_em']);
            $table->dropColumn(['cidadao_entregou_em', 'condicao_na_devolucao']);
        });
    }
};
