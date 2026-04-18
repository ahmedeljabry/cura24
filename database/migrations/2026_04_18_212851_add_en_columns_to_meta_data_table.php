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
        Schema::table('meta_data', function (Blueprint $table) {
            $table->string('meta_title_en')->nullable();
            $table->text('meta_tags_en')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->string('facebook_meta_tags_en')->nullable();
            $table->text('facebook_meta_description_en')->nullable();
            $table->string('twitter_meta_tags_en')->nullable();
            $table->text('twitter_meta_description_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta_data', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title_en',
                'meta_tags_en',
                'meta_description_en',
                'facebook_meta_tags_en',
                'facebook_meta_description_en',
                'twitter_meta_tags_en',
                'twitter_meta_description_en'
            ]);
        });
    }
};
