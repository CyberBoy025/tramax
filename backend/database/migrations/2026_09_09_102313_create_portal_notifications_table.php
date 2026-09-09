<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// discovery.md §2's NOTIFICATION entity, named "portal_notifications" (not
// "notifications") to avoid colliding with Laravel's own Notifiable/
// database-notifications schema — User already has the Notifiable trait,
// but nothing calls ->notify() and no notifications ever get created here
// automatically yet; this is the system-of-record table for the artist
// portal's read-only inbox, populated manually for now.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_notifications');
    }
};
