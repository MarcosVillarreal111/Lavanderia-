<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('email');
            $table->boolean('notif_whatsapp_lista')->default(true)->after('whatsapp');
            $table->boolean('notif_en_camino')->default(true)->after('notif_whatsapp_lista');
            $table->boolean('notif_promos')->default(false)->after('notif_en_camino');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp',
                'notif_whatsapp_lista',
                'notif_en_camino',
                'notif_promos',
            ]);
        });
    }
};
