<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhatsAppBookingIncludesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('whats_app_booking_includes')) {
            // Create the whats_app_booking_addons table
            Schema::create('whats_app_booking_includes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('whats_app_booking_id');
                $table->unsignedBigInteger('include_id');
                $table->double('quantity', 8, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whats_app_booking_includes');
    }
}
