<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Per discovery.md §2 ROYALTY_STATEMENT — manually-entered records and
// statements, not an automated calculation engine (README.md §14).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('royalty_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_profile_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('company_share', 14, 2)->default(0);
            $table->decimal('artist_share', 14, 2)->default(0);
            $table->string('status')->default('Draft'); // Draft, Pending, Paid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('royalty_statements');
    }
};
