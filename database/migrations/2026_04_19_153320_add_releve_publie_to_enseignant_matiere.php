<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enseignant_matiere', function (Blueprint $table) {
            if (!Schema::hasColumn('enseignant_matiere', 'releve_publie')) {
                $table->boolean('releve_publie')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('enseignant_matiere', function (Blueprint $table) {
            $table->dropColumn('releve_publie');
        });
    }
};