<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patch', function (Blueprint $table) {
            $table->dropUnique('patch_name_unique');
            $table->unique(['branch_id', 'name'], 'patch_branch_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('patch', function (Blueprint $table) {
            $table->dropUnique('patch_branch_name_unique');
            $table->unique('name', 'patch_name_unique');
        });
    }
};