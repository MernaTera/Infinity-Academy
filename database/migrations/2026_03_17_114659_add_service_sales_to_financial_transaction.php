<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('enrollment_id')->nullable()->change();
            $table->unsignedBigInteger('service_sale_id')->nullable()->after('enrollment_id');
            $table->foreign('service_sale_id', 'financial_transaction_service_sale_fk')
                ->references('service_sale_id')
                ->on('service_sale')
                ->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE `financial_transaction` MODIFY `transaction_category` ENUM('Course','Material','Test','Certificate','Other') NOT NULL");

        DB::statement("ALTER TABLE `lead_history` MODIFY `action_type` ENUM('Created','Status_Changed','Owner_Changed','Data_Updated','Call_Logged','Registered','Archived','Restored','Note_Added','Auto_Public','Auto_Archived','Interest_Updated','Test_Purchased','Certificate_Purchased') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE `lead_history` SET `action_type` = 'Note_Added' WHERE `action_type` IN ('Test_Purchased','Certificate_Purchased')");
        DB::statement("ALTER TABLE `lead_history` MODIFY `action_type` ENUM('Created','Status_Changed','Owner_Changed','Data_Updated','Call_Logged','Registered','Archived','Restored','Note_Added','Auto_Public','Auto_Archived','Interest_Updated') NULL");

        DB::statement("DELETE FROM `financial_transaction` WHERE `service_sale_id` IS NOT NULL");
        DB::statement("ALTER TABLE `financial_transaction` MODIFY `transaction_category` ENUM('Course','Material','Test','Other') NOT NULL");

        Schema::table('financial_transaction', function (Blueprint $table) {
            $table->dropForeign('financial_transaction_service_sale_fk');
            $table->dropColumn('service_sale_id');
            $table->unsignedBigInteger('enrollment_id')->nullable(false)->change();
        });
    }
};
