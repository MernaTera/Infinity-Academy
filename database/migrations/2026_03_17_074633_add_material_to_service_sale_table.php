<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `service_sale` MODIFY `service_type` ENUM('Test','Certificate','Material') NOT NULL");

        Schema::table('service_sale', function (Blueprint $table) {
            if (!Schema::hasColumn('service_sale', 'material_id')) {
                $table->unsignedBigInteger('material_id')->nullable()->after('certificate_setting_id');
                $table->index('material_id', 'idx_service_sale_material');
            }
            if (!Schema::hasColumn('service_sale', 'enrollment_id')) {
                $table->unsignedBigInteger('enrollment_id')->nullable()->after('student_id');
                $table->index('enrollment_id', 'idx_service_sale_enrollment');
            }
        });

        DB::statement("ALTER TABLE `lead_history` MODIFY `action_type` ENUM('Created','Status_Changed','Owner_Changed','Data_Updated','Call_Logged','Registered','Archived','Restored','Note_Added','Auto_Public','Auto_Archived','Interest_Updated','Test_Purchased','Certificate_Purchased','Material_Purchased') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE `lead_history` SET `action_type` = 'Note_Added' WHERE `action_type` = 'Material_Purchased'");
        DB::statement("ALTER TABLE `lead_history` MODIFY `action_type` ENUM('Created','Status_Changed','Owner_Changed','Data_Updated','Call_Logged','Registered','Archived','Restored','Note_Added','Auto_Public','Auto_Archived','Interest_Updated','Test_Purchased','Certificate_Purchased') NULL");

        Schema::table('service_sale', function (Blueprint $table) {
            if (Schema::hasColumn('service_sale', 'material_id')) {
                $table->dropIndex('idx_service_sale_material');
                $table->dropColumn('material_id');
            }
            if (Schema::hasColumn('service_sale', 'enrollment_id')) {
                $table->dropIndex('idx_service_sale_enrollment');
                $table->dropColumn('enrollment_id');
            }
        });

        DB::statement("DELETE FROM `service_sale` WHERE `service_type` = 'Material'");
        DB::statement("ALTER TABLE `service_sale` MODIFY `service_type` ENUM('Test','Certificate') NOT NULL");
    }
};
