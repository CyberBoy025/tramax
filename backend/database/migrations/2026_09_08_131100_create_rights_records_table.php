<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Per discovery.md §2 RIGHTS_RECORD — ownership record-keeping only
// (masters, publishing, copyright, composition, licensing), no contract
// automation, per README.md §14's MVP depth confirmation.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rights_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('track_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('master_owner')->nullable();
            $table->string('publishing_owner')->nullable();
            $table->string('songwriter')->nullable();
            $table->string('producer')->nullable();
            $table->string('copyright_status')->default('Active'); // Active, Disputed, Expired
            $table->string('licensing_status')->default('Unlicensed'); // Unlicensed, Licensed, Exclusive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rights_records');
    }
};
