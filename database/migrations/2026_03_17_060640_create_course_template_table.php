<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_template', function (Blueprint $table) {
            $table->id('course_template_id');

            $table->string('name', 150);

            $table->boolean('private_allowed')->default(true);
            $table->boolean('private_only')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id');

            $table->timestamps();

            $table->unique('name');

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_template');
    }
};
