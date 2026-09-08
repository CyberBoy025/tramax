<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Entries are immutable once written (discovery.md §2's AUDIT_LOG_ENTRY) —
// no updated_at, and user_id is nullOnDelete so a deleted admin's history
// stays in the trail instead of disappearing with the account.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log_entries');
    }
};
