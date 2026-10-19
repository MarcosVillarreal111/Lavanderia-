<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->boolean('archivado')->default(false)->after('entrega_estimada');
            $table->dateTime('archivado_en')->nullable()->after('archivado');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['archivado', 'archivado_en']);
        });
    }
};
