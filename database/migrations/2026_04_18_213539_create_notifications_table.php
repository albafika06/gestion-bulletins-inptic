<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utilisateur_id');
            $table->string('type', 50);
            $table->string('titre', 200);
            $table->text('message');
            $table->string('lien', 300)->nullable();
            $table->boolean('lu')->default(false);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('annee_univ', 20)->default('2025/2026');
            $table->timestamps();
            $table->index('utilisateur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};