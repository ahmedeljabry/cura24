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
        // Update language slugs to match mcamara supported locales and disable arabic
        \App\Language::where('slug', 'en_GB')->update(['slug' => 'en']);
        \App\Language::where('slug', 'it_IT')->update(['slug' => 'it']);
        \App\Language::where('slug', 'ar')->update(['status' => 'draft']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse if necessary
        \App\Language::where('slug', 'en')->update(['slug' => 'en_GB']);
        \App\Language::where('slug', 'it')->update(['slug' => 'it_IT']);
    }
};
