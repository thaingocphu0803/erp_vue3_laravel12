<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdministrativeUnitSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		$path = 'data/administrative_units.sql';

		$sql = File::get(database_path($path));

		DB::unprepared($sql);
	}
}
