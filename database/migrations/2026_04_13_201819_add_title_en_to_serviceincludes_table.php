<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleEnToServiceincludesTable extends Migration
{
    public function up()
    {
        Schema::table('serviceincludes', function (Blueprint $table) {
            $table->string('include_service_title_en')->nullable()->after('include_service_title');
        });
    }


    public function down()
    {
        Schema::table('serviceincludes', function (Blueprint $table) {
            $table->dropColumn('include_service_title_en');
        });
    }
}
