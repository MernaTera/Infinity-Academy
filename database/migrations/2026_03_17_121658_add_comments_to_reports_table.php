<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('report', function (Blueprint $table) {
            $table->text('comments')->nullable()->after('total_score');
        });

        DB::table('report')
            ->where('rejection_note', 'like', '\_\_COMMENTS\_\_%')
            ->orderBy('report_id')
            ->get(['report_id', 'rejection_note'])
            ->each(function ($row) {
                DB::table('report')->where('report_id', $row->report_id)->update([
                    'comments'       => substr($row->rejection_note, 12),
                    'rejection_note' => null,
                ]);
            });
    }

    public function down(): void
    {
        DB::table('report')
            ->whereNotNull('comments')
            ->whereNull('rejection_note')
            ->orderBy('report_id')
            ->get(['report_id', 'comments'])
            ->each(function ($row) {
                DB::table('report')->where('report_id', $row->report_id)->update([
                    'rejection_note' => '__COMMENTS__' . $row->comments,
                ]);
            });

        Schema::table('report', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
    }
};
