<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }
            if (!Schema::hasColumn('menus', 'content_en')) {
                $table->longText('content_en')->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'title_en')) {
                $table->dropColumn('title_en');
            }
            if (Schema::hasColumn('menus', 'content_en')) {
                $table->dropColumn('content_en');
            }
        });
    }
};
