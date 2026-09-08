<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('artist_name');
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('location')->nullable();
            $table->string('genre')->nullable();
            $table->unsignedSmallInteger('years_active')->nullable();
            $table->json('social_links')->nullable();
            $table->json('streaming_links')->nullable();
            $table->text('biography')->nullable();
            $table->string('demo_file_url')->nullable();
            // Submitted -> Under Review -> Shortlisted -> Accepted (or Rejected), per README.md Phase 4
            $table->string('status')->default('Submitted');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_applications');
    }
};
