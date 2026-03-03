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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
			$table->string('name', 100);
			$table->string('code',20)->unique();
			$table->text('description')->nullable();
			$table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();
			$table->enum('status', ['A', 'X'])->default('A');
			$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
			$table->foreignId('update_by')->nullable()->constrained('users')->nullOnDelete();
			$table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
