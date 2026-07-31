<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refund_request', function (Blueprint $table) {
            $table->boolean('includes_material')->default(false)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('refund_request', function (Blueprint $table) {
            $table->dropColumn('includes_material');
        });
    }
};