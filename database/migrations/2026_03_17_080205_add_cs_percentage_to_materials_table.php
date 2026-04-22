<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // % of material revenue that goes to the CS who made the deal
            // Admin sets this per material. Remaining % goes to academy.
            $table->unsignedTinyInteger('cs_percentage')
                  ->default(0)
                  ->after('price')
                  ->comment('% of material price credited to CS commission (0-100)');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('cs_percentage');
        });
    }
};