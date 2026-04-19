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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('title_en')->nullable();
            $table->longText('blog_content_en')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->string('tag_name_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'blog_content_en', 'excerpt_en', 'tag_name_en']);
        });
    }
};
