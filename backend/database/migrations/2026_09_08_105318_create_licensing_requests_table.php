<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licensing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('music_required')->nullable();
            $table->string('project_type')->nullable();
            $table->string('usage')->nullable();
            $table->string('duration')->nullable();
            $table->string('territory')->nullable();
            $table->string('budget')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('related_release_id')->nullable()->constrained('releases')->nullOnDelete();
            $table->string('status')->default('New'); // New, In Review, Approved, Declined
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licensing_requests');
    }
};
