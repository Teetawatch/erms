<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('project_id')->constrained('tasks')->cascadeOnDelete();
            $table->date('start_date')->nullable()->after('due_date');
            $table->integer('estimated_hours')->nullable()->after('start_date');
            $table->integer('progress')->default(0)->after('estimated_hours');
            $table->json('tags')->nullable()->after('progress');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'start_date', 'estimated_hours', 'progress', 'tags']);
        });
    }
};
