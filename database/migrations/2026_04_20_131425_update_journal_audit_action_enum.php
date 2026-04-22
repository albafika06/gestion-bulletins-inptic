<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE journal_audit MODIFY COLUMN action 
            ENUM(
                'INSERT','UPDATE','DELETE','LOGIN','LOGOUT','IMPORT','EXPORT',
                'GENERATION_BULLETIN',
                'NOTE_SAISIE',
                'BULLETIN_PUBLIE',
                'CONNEXION',
                'DECONNEXION',
                'ETUDIANT_AJOUTE',
                'ETUDIANT_MODIFIE',
                'IMPORT_EXCEL',
                'AFFECTATION_MATIERE',
                'RESET_PASSWORD',
                'RELEVE_PUBLIE'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE journal_audit MODIFY COLUMN action 
            ENUM('INSERT','UPDATE','DELETE','LOGIN','LOGOUT','IMPORT','EXPORT','GENERATION_BULLETIN') 
            NOT NULL
        ");
    }
};