<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_history', function (Blueprint $table) {

            $table->enum('action_type', [
                'Created',
                'Status_Changed',
                'Owner_Changed',
                'Data_Updated',
                'Call_Logged',
                'Registered',
                'Archived',
                'Restored',
                'Note_Added',
                'Auto_Public',
                'Auto_Archived',
                'Interest_Updated',
            ])->nullable()->after('lead_id');

            $table->json('changed_fields')->nullable()->after('new_status');

            $table->text('reason')->nullable()->after('changed_fields');

            $table->enum('call_outcome', [
                'No_Answer',
                'Interested',
                'Not_Interested',
                'Call_Again',
                'Registered',
                'Wrong_Number',
                'Follow_Up_Scheduled',
            ])->nullable()->after('reason');

            $table->string('ip_address', 45)->nullable()->after('call_outcome');
            $table->string('user_agent', 500)->nullable()->after('ip_address');

            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::table('lead_history', function (Blueprint $table) {
            $table->dropIndex(['action_type']);
            $table->dropColumn([
                'action_type',
                'changed_fields',
                'reason',
                'call_outcome',
                'ip_address',
                'user_agent',
            ]);
        });
    }
};