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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
			$table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
			$table->integer('province_id')->nullable();
			$table->integer('ward_id')->nullable();
			$table->string('address');
			$table->string('phone_number', 12);
			$table->text('avatar_url');
			$table->string('avatar_color',10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
