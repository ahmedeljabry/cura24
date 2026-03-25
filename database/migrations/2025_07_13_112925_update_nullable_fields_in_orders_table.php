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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('post_code')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('date')->nullable()->change();
            $table->string('schedule')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('post_code')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('date')->nullable(false)->change();
            $table->string('schedule')->nullable(false)->change();
        });
    }
};
