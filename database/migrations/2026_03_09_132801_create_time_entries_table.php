<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('hours', 6, 2);
            $table->text('description')->nullable();
            $table->date('date_worked');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
