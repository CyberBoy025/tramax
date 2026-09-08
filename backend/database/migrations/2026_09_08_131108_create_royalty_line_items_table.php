<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Per discovery.md §2 ROYALTY_LINE_ITEM — the per-source breakdown shown
// under a statement's totals (see the Phase 2 wireframe's statement detail).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('royalty_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('royalty_statement_id')->constrained()->cascadeOnDelete();
            $table->string('source'); // Streaming, Publishing, Licensing, Other
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('royalty_line_items');
    }
};
