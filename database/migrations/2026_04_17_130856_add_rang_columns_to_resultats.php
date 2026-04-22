<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resultats_semestres', function (Blueprint $table) {
            if (!Schema::hasColumn('resultats_semestres', 'rang')) {
                $table->integer('rang')->nullable();
            }
            if (!Schema::hasColumn('resultats_semestres', 'stat_moyenne_classe')) {
                $table->float('stat_moyenne_classe')->nullable();
            }
            if (!Schema::hasColumn('resultats_semestres', 'stat_min')) {
                $table->float('stat_min')->nullable();
            }
            if (!Schema::hasColumn('resultats_semestres', 'stat_max')) {
                $table->float('stat_max')->nullable();
            }
        });

        Schema::table('resultats_annuels', function (Blueprint $table) {
            if (!Schema::hasColumn('resultats_annuels', 'rang_annuel')) {
                $table->integer('rang_annuel')->nullable();
            }
            if (!Schema::hasColumn('resultats_annuels', 'stat_moyenne_classe')) {
                $table->float('stat_moyenne_classe')->nullable();
            }
        });
    }

    public function down(): void {}
};