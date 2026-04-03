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
		// Regions table
		Schema::create('administrative_units', function (Blueprint $table) {
			$table->id();
			$table->string('full_name', 255)->nullable();
			$table->string('full_name_en', 255)->nullable();
			$table->string('short_name', 255)->nullable();
			$table->string('short_name_en', 255)->nullable();
			$table->string('code_name', 255)->nullable();
			$table->string('code_name_en', 255)->nullable();
		});

		// Provinces table
		Schema::create('provinces', function (Blueprint $table) {
			$table->string('code', 20)->primary();
			$table->string('name', 255);
			$table->string('name_en', 255)->nullable();
			$table->string('full_name', 255);
			$table->string('full_name_en', 255)->nullable();
			$table->string('code_name', 255)->nullable();
			$table->foreignId('administrative_unit_id')->nullable()->constrained('administrative_units')->nullOnDelete();
			$table->index('administrative_unit_id');
		});

		// Wards table
		Schema::create('wards', function (Blueprint $table) {
			$table->string('code', 20)->primary();
			$table->string('name', 255);
			$table->string('name_en', 255)->nullable();
			$table->string('full_name', 255)->nullable();
			$table->string('full_name_en', 255)->nullable();
			$table->string('code_name', 255)->nullable();
			$table->string('province_code', 20)->nullable();
			$table->foreign('province_code')->references('code')->on('provinces')->nullOnDelete();
			$table->foreignId('administrative_unit_id')->nullable()->constrained('administrative_units')->nullOnDelete();
			$table->index('province_code');
			$table->index('administrative_unit_id');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('administrative_units');
		Schema::dropIfExists('provinces');
		Schema::dropIfExists('wards');
	}
};
