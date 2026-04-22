<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_audit', function (Blueprint $table) {
            $table->string('table_cible')->nullable()->change();
            $table->string('nom_utilisateur')->nullable()->change();
            $table->string('id_enregistrement')->nullable()->change();
            $table->text('ancienne_valeur')->nullable()->change();
            $table->text('nouvelle_valeur')->nullable()->change();
        });
    }

    public function down(): void {}
};