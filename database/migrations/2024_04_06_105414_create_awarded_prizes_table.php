<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('awarded_prizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prizes_id');
            $table->integer('simulation_value');
            $table->decimal('awarded', 10, 2)->default(0);
            $table->integer('is_active')->default(1)->comment('0=In Active, 1=Active');
            $table->timestamps();
        });

        Schema::table('awarded_prizes', function (Blueprint $table) {
            $table->foreign('prizes_id')->references('id')->on('prizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awarded_prizes');
    }
};
