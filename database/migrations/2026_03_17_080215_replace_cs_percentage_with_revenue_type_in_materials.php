<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'cs_percentage')) {
                $table->dropColumn('cs_percentage');
            }
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->enum('revenue_type', ['Individual', 'Shared'])
                  ->default('Shared')
                  ->after('price')
                  ->comment('Individual = credited to the CS who registered. Shared = split equally among all active CS in the same branch.');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'revenue_type')) {
                $table->dropColumn('revenue_type');
            }
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->unsignedTinyInteger('cs_percentage')->default(0)->after('price');
        });
    }
};