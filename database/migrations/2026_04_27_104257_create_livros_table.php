<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->text('isbn');
            $table->text('nome');
            $table->foreignId('editora_id')->constrained('editoras')->cascadeOnDelete();
            $table->longText('bibliografia')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->text('preco')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
