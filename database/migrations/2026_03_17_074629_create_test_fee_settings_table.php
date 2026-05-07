<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('test_fee_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         
            $table->decimal('fee', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('test_fee_settings')->insert([
            'name'       => 'Standard Placement Test',
            'fee'        => 200.00,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('test_fee_settings');
    }
};
