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
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'country_en')) {
                $table->string('country_en')->nullable()->after('country');
            }
        });
        Schema::table('service_cities', function (Blueprint $table) {
            if (!Schema::hasColumn('service_cities', 'service_city_en')) {
                $table->string('service_city_en')->nullable()->after('service_city');
            }
        });
        Schema::table('service_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('service_areas', 'service_area_en')) {
                $table->string('service_area_en')->nullable()->after('service_area');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'country_en')) {
                $table->dropColumn('country_en');
            }
        });
        Schema::table('service_cities', function (Blueprint $table) {
            if (Schema::hasColumn('service_cities', 'service_city_en')) {
                $table->dropColumn('service_city_en');
            }
        });
        Schema::table('service_areas', function (Blueprint $table) {
            if (Schema::hasColumn('service_areas', 'service_area_en')) {
                $table->dropColumn('service_area_en');
            }
        });
    }
};
