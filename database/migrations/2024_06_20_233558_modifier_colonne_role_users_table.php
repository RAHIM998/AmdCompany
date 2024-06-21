<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modifier la colonne 'role' pour utiliser le type enum
            $table->enum('role', ['admin', 'client', 'livraison'])->default('client')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revenir à l'état précédent lors du rollback
            $table->enum('role', ['admin', 'client'])->default('client')->change();
        });
    }
};
