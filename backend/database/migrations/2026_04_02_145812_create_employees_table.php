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
		Schema::create('employees', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
			$table->string('code', 20)->unique();
			$table->string('address', 255);
			$table->string('phone_number', 12)->unique();
			$table->enum('gender', ['male', 'female']);
			$table->date('birth_date');
			$table->text('avatar')->nullable();
			$table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
			$table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
			$table->string('province_code', 20)->nullable();
			$table->string('ward_code', 20)->nullable();
			$table->foreign('province_code')->references('code')->on('provinces')->nullOnDelete();
			$table->foreign('ward_code')->references('code')->on('wards')->nullOnDelete();
			$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
			$table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('employees');
	}
};
