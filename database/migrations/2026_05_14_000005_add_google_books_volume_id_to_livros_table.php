<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->string('google_books_volume_id', 64)->nullable()->after('id');
            $table->unique('google_books_volume_id');
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropUnique(['google_books_volume_id']);
            $table->dropColumn('google_books_volume_id');
        });
    }
};
