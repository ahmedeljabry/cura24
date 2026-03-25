<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seller_subscriptions', function (Blueprint $table) {
            $table->bigInteger('service')->default(0)->after('connect');
            $table->bigInteger('job')->default(0)->after('service');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seller_subscriptions', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->dropColumn('job');
        });
    }
};
