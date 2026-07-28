<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_approval_log', function (Blueprint $table) {
            $table->text('waiting_list_meta')->nullable()->after('rejection_note');
        });
    }

    public function down(): void
    {
        Schema::table('installment_approval_log', function (Blueprint $table) {
            $table->dropColumn('waiting_list_meta');
        });
    }
};
