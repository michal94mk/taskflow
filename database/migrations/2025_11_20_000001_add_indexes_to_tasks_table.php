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
        Schema::table('tasks', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('due_date');
            $table->index(['user_id', 'task_status_id'], 'user_status_index');
            $table->index(['user_id', 'task_priority_id'], 'user_priority_index');
            $table->index(['user_id', 'created_at'], 'user_created_index');
            $table->index(['user_id', 'due_date'], 'user_due_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('user_status_index');
            $table->dropIndex('user_priority_index');
            $table->dropIndex('user_created_index');
            $table->dropIndex('user_due_date_index');
            $table->dropIndex(['due_date']);
        });
    }
};

