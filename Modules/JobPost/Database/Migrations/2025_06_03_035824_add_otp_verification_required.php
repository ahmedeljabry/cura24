<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtpVerificationRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_requests', function (Blueprint $table) {
            $table->string('completion_otp')->nullable()->after('cover_letter');
            $table->timestamp('otp_expires_at')->nullable()->after('completion_otp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_requests', function (Blueprint $table) {
            $table->dropColumn([
                'completion_otp',
                'otp_expires_at'
            ]);
        });
    }
}
