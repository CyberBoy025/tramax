<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('artist_name');
            $table->string('slug')->unique();
            $table->text('biography')->nullable();
            $table->string('genre')->nullable();
            $table->string('photo_url')->nullable();
            $table->json('social_links')->nullable();
            $table->string('status')->default('Active'); // Active, Development, Inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_profiles');
    }
};
