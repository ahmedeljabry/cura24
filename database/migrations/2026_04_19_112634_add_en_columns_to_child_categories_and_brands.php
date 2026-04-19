<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('child_categories', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
            if (!Schema::hasColumn('child_categories', 'description_en')) {
                $table->text('description_en')->nullable()->after('description');
            }
        });

        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('child_categories', function (Blueprint $table) {
            if (Schema::hasColumn('child_categories', 'name_en')) {
                $table->dropColumn('name_en');
            }
            if (Schema::hasColumn('child_categories', 'description_en')) {
                $table->dropColumn('description_en');
            }
        });

        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'title_en')) {
                $table->dropColumn('title_en');
            }
        });
    }
};
