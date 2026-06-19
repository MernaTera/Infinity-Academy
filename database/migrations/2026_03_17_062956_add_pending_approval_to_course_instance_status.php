<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE course_instance 
            MODIFY COLUMN status 
            ENUM('Upcoming','Active','Completed','Cancelled','Pending_Approval') 
            NOT NULL DEFAULT 'Upcoming'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE course_instance 
            MODIFY COLUMN status 
            ENUM('Upcoming','Active','Completed','Cancelled') 
            NOT NULL DEFAULT 'Upcoming'
        ");
    }
};
