<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('message');
        });
    }

    public function down()
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
}
