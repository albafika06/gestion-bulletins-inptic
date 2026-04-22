<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_audit', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_audit', 'description')) {
                $table->string('description', 500)->nullable();
            }
            if (!Schema::hasColumn('journal_audit', 'details')) {
                $table->text('details')->nullable();
            }
            if (!Schema::hasColumn('journal_audit', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (!Schema::hasColumn('journal_audit', 'user_agent')) {
                $table->string('user_agent', 300)->nullable();
            }
            if (!Schema::hasColumn('journal_audit', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
        });
    }

    public function down(): void {}
};